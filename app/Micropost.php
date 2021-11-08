<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    protected $fillable = ['content'];

    /**
     * この投稿を所有するユーザ。（ Userモデルとの関係を定義）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    //  public function loadRelationshipCounts()
    // {
    //     //Micropostモデルとfavoritesのリレーションメソッド
    //     $this->loadCount('favorites');
    // }
    
    //お気に入りされている --userモデルとの関係を定義
     public function favorite_users()
    {
        return $this->belongsToMany(User::class, 'favorites', 'favorite_id', 'user_id')->withTimestamps();
    }
    
     
    
}
