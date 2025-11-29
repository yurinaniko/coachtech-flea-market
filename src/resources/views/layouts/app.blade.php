<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH FLEA</title>
    {{-- 共通CSS --}}
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/item-list.css') }}">
    @yield('css')
</head>
<body>
    <div class="wrapper">
        {{-- 共通ヘッダー読み込み --}}
        @include('layouts.header')

        {{-- ページごとの内容 --}}
        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>