<?php

namespace App\Observers;

use App\Models\Topic;
use App\Handlers\SlugTranslateHandler;
use App\Jobs\TranslateSlug;
use Illuminate\Support\Facades\DB;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function saving(Topic $topic)
    {
        // xss 过滤
        $topic->body = clean($topic->body,'user_topic_body');
        // 生成话题摘录
        $topic->excerpt = make_excerpt($topic->body);

    }

    public function saved(Topic $topic)
    {
        // 如 slug 字段无内容 ,即使用翻译器对title进行翻译
        if (! $topic->slug) {
//            $topic->slug = app(SlugTranslateHandler::class)->translate($topic->title);

            // 直接任务到队列
            dispatch(new TranslateSlug($topic));

        }
    }

    // 当删除话题的时候 清空话题的回复
    public function deleted(Topic $topic)
    {
        DB::table('replies')->where('topic_id', $topic->id)->delete();
    }
}
