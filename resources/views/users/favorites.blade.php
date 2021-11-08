@extends('layouts.app')

@section('content')
    <div class="row">
        <aside class="col-sm-4">
            {{-- マイクロポスト情報 --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $user->name }}</h3>
                </div>
                <div class="card-body">
                    {{-- ユーザのメールアドレスをもとにGravatarを取得して表示 --}}
                    <img class="rounded img-fluid" src="{{ Gravatar::get($user->email, ['size' => 500]) }}" alt="">
                </div>
             </div>
             @foreach($microposts as $micropost) 
            {{-- フェイバ／アンフェイバ　ボタン --}}
               @include('favorite.favorite_button')
             @endforeach
        </aside>
     
        <div class="col-sm-8">
            {{-- タブ --}}
            @include('users.navtabs')
            {{-- ユーザ一覧 --}}
            @include('microposts.microposts')
        </div>
    </div>
@endsection