<?php

namespace App\Models;

use App\Http\Resources\PostResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'photo', 'title', 'body', 'user_id'
    ];

    public function author(){
        return $this->belongsTo(User::class);
    }

    public function toResource()
    {
        return new PostResource($this);
    }
}
