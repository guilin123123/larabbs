<?php

use Illuminate\Database\Seeder;
use App\Models\Reply;

class ReplysTableSeeder extends Seeder
{
    public function run()
    {
        // 所有用户ID数组 ru:[1,2,3,4]
        $user_ids = \App\Models\User::all()->pluck('id')->toArray();

        // 所有话题ID数组 ru[1,2,3,4]
        $topic_ids = \App\Models\Topic::all()->pluck('id')->toArray();

        // 获取faker实例
        $faker = app(Faker\Generator::class);

        $replys = factory(Reply::class)
            ->times(1000)
            ->make()
            ->each(function ($reply, $index)
            use($user_ids, $topic_ids, $faker)
        {
            // 从用户ID数组中随机取出一个并赋值
            $reply->user_id = $faker->randomElement($user_ids);
            // 话题ID ,同上
            $reply->topic_id = $faker->randomElement($topic_ids);
        });

        Reply::insert($replys->toArray());
    }

}

