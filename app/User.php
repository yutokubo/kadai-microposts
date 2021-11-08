<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
     
     /**
     * このユーザが所有する投稿。（ Micropostモデルとの関係を定義）
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    public function loadRelationshipCounts()
    {
        //リレーションメソッドを引数として指定する
        $this->loadCount('microposts', 'followings', 'followers', 'favorites');
    } 
    
    /**
     * このユーザがフォロー中のユーザ。（Userモデルとの関係を定義）
     */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    /**
     * このユーザをフォロー中のユーザ。（ Userモデルとの関係を定義）
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
     /**
     * $userIdで指定されたユーザをフォローする。
     *
     * @param  int  $userId
     * @return bool
     */
    public function follow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうかの確認  
        $its_me = $this->id == $userId;

        if ($exist || $its_me) {
            // すでにフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
     /**
     * $userIdで指定されたユーザをアンフォローする。
     *
     * @param  int  $userId
     * @return bool
     */
    public function unfollow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist && !$its_me) {
            // すでにフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
     /**
     * 指定された $userIdのユーザをこのユーザがフォロー中であるか調べる。フォロー中ならtrueを返す。
     *
     * @param  int  $userId
     * @return bool
     */
    public function is_following($userId)
    {
        // フォロー中ユーザの中に $userIdのものが存在するか
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    /**
     * このユーザとフォロー中ユーザの投稿に絞り込む。
     */
    public function feed_microposts()
    {
        // このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザのidもその配列に追加
        $userIds[] = $this->id;
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }
    
    //このユーザーがお気に入り中のマイクロポスト　ー　Userモデルとの関係を定義
    public function favorites()
    {//Modelクラス・中間テーブル・中間テーブル中の自分のid・中間テーブルの関係先id
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'favorite_id')->withTimestamps();
    }
    
    // $micropostIdで指定されたマイクロポストをいいねする。
    
    public function favorite($micropostId) {
          // すでにフォローしているかの確認
        $exist = $this->is_favorite($micropostId);

        if ($exist) {
            // すでにお気に入りしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->favorites()->attach($micropostId);
            return true;
        }
        
    }
   // $micropostIdで指定されたユーザをアンフェイバリットする。
    public function unfavorite($micropostId) {
         // すでにお気に入りしているかの確認
        $exist = $this->is_favorite($micropostId);

        if ($exist) {
            // すでにフォローしていればフォローを外す
            $this->favorites()->detach($micropostId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
        
    }
    
    //指定された $micropostIdのマイクロポストをこのユーザがお気に入り中であるか調べる。お気に入り中ならtrueを返す。
    public function is_favorite($micropostId)
    {
        // お気に入り中のマイクロポストの中に $userIdのものが存在するか
        return $this->favorites()->where('favorite_id', $micropostId)->exists();
    }
}
