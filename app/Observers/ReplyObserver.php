<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{
    public function saving(Reply $reply)
    {
        // xss 过滤
        $reply->content = clean($reply->content, 'user_topic_body');
    }

    public function created(Reply $reply)
    {
        // 计算话题回复数
        $reply->topic->reply_count = $reply->topic->replies->count();
        $reply->topic->save();

        // 通知作者话题有新回复
        $reply->topic->user->notify(new TopicReplied($reply));
    }
}
