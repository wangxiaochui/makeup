<!doctype html>
<html>
<head>
    <title>@yield('title')</title>
    <meta charset="UTF-8">
    <link href="/css/main.css" rel="stylesheet">
    <meta name="viewport" content="width=1650">
    <!-- uc强制横屏 -->
    <meta name="screen-orientation" content="landscape">
    <!-- QQ强制横屏 -->
    <meta name="x5-orientation" content="landscape">
    <!-- UC强制全屏 -->
    <meta name="full-screen" content="yes">
    <!-- QQ强制全屏 -->
    <meta name="x5-fullscreen" content="true">
    <!-- UC应用模式 -->
    <meta name="browsermode" content="application">
    <!-- QQ应用模式 -->
    <meta name="x5-page-mode" content="app">
</head>
<style>
    @yield('style')
</style>
<body>
@yield('content')
</body>
@yield('script')
</html>
