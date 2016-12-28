@extends('layouts.makeup')

@section('title')
    test title
@endsection

@section('style')
    body{background:#eee;margin:0;padding:0px;}
    #main{margin-top:20px;margin-left:3%}
    .page{width:48%;
    margin-right:-6px;
    background:#fff;
    position:relative;
    display:inline-block;
    box-shadow:0 2px 5px 0 rgba(0,0,0,.16),0 2px 10px 0 rgba(0,0,0,.12);
    word-spacing: 0;
    }
    .page{background:url(/images/makeup/bk.jpg)}
@endsection

@section('content')
    <div id="main">
        <div class="page">
            <img src="/images/makeup/2.jpg" style="width:60%;position:absolute;top:10%;left:20%">
        </div>
        <div class="page">
            <img src="/images/makeup/4.jpg" style="width:60%;position:absolute;top:10%;left:20%">
            <img src="/images/makeup/5.jpg" style="width:60%;position:absolute;top:50%;left:20%">
        </div>
    </div>
@endsection

@section('script')
    <script src="/js/jquery.min.js"></script>
    <script>
        $(function(){
            var width = $('.page').width();
            $('.page').height(width);

            var promise = $.ajax({
                url : '/auto/make',
                data: {page: 2, width:width},
                type : 'GET',
                dataType : 'JSON'
            });
            //请求成功
            promise.always(function(){
                //...pedding

            })

            //返回成功
            promise.done(function(data){
                console.log(data)
            })
        })
    </script>
@endsection