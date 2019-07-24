<?php

namespace App\Models;

class Reply extends Model
{
    protected $fillable = ['content', 'user_id', 'topic_id'];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
