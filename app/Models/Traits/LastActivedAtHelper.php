<?php

namespace App\Models\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

trait LastActivedAtHelper
{
    // 缓存相关
    protected $hash_prefix = 'larabbs_last_actived_at_';
    protected $field_prefix = 'user_';

    public function recordLastActivedAt()
    {
        // 获取今天的日期
        $date = Carbon::now()->toDateString();

        // Redis 哈希表的命名, 如: larabbs_last_actived_at_2019-08-06
        $hash = $this->getHashFromDateString($date);

        // 字段名称, 如: user_1
        $field = $this->getHashField();

        // 当前时间, 如: 2019-08-06 23:19:10
        $now = Carbon::now()->toDateTimeString();

        // 数据写入Redis, 字段已存在会被更新
        Redis::hSet($hash, $field, $now);

    }

    public function SyncUserActivedAt()
    {
        // 获取昨天的日期,格式如: 2018-08-06
        $yesterday_date = Carbon::yesterday()->toDateString();

        // Redis哈希表的命名, 如: larabbs_last_actived_at_2018-08-06
        $hash = $this->getHashFromDateString($yesterday_date);

        // 从Redis 中获取所有哈希表里的数据
        $dates = Redis::hGetAll($hash);

        // 遍历, 并同步到数据库中
        foreach ($dates as $user_id => $actived_at) {
            // 会将 'user_1' 转换为 1
            $user_id = str_replace($this->field_prefix,'',$user_id);

            // 只有当用户存在时,才更新到数据库中
            if ($user = $this->find($user_id)) {
                $user->last_actived_at = $actived_at;
                $user->save();
            }
        }

        // 以数据库为中心, 既以同步,便可删除
        Redis::del($hash);
    }

    public function getLastActivedAtAttribute($value)
    {
        // 获取今天的日期
        $date = Carbon::now()->toDateString();

        // Redis哈希表的命名, 如: larabbs_last_actived_at_2018-08-07
        $hash = $this->getHashFromDateString($date);

        // 字段名称, 如: user_1
        $field = $this->getHashField();

        // 三元运算符, 优先选择Redis数据, 否则使用数据库中的数据
        $datetime = Redis::hGet($hash, $field) ? : $value;

        // 如果存在的话,返回对应的Carbon实体
        if ($datetime) {
            return new Carbon($datetime);
        } else {
            // 否则使用用户注册时间
            return $this->created_at;
        }
    }

    public function getHashFromDateString($date)
    {
        // Redis 哈希表的命名, 如:larabbs_last_actived_at_2018-08-07
        return $this->hash_prefix . $date;
    }

    public function getHashField()
    {
        // 字段名称, 如: user_1
        return $this->field_prefix . $this->id;
    }
}
