<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'content', 'order', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function views()
    {
        return $this->hasMany(PostView::class);
    }

    public function addView()
    {
        $this->views()->create([
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
