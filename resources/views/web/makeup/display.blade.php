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
        @foreach($data as $dk=>$dv)
        <div class="page" style="overflow:hidden">
            @foreach($dv as $ddk=>$ddv)
                <div style="@if(isset($ddv['rotate'])) transform:rotate(7deg);
                        -ms-transform:rotate({{$ddv['rotate']}}deg); 	/* IE 9 */
                        -moz-transform:rotate({{$ddv['rotate']}}deg)                 ; 	/* Firefox */
                        -webkit-transform:rotate({{$ddv['rotate']}}deg); /* Safari 和 Chrome */
                        -o-transform:rotate({{$ddv['rotate']}}deg); @endif  @if(isset($ddv['radius']))border-radius: {{$ddv['radius']}}%  @endif;width:{{$ddv['width']}}%;height:{{$ddv['height']}}%;position:absolute;top:{{$ddv['top']}}%;left:{{$ddv['left']}}%;overflow:hidden">
                    <img src="{{$ddv['path']}}" style='width:{{$ddv['relative_width']}}%;height:{{$ddv['relative_height']}}%;position:relative;top:-{{$ddv['relative_cut_top']}}%;left:-{{$ddv['relative_cut_left']}}%;'>
                </div>
            @endforeach
        </div>
       
        @endforeach
    </div>
@endsection

@section('script')
    <script src="/js/jquery.min.js"></script>
 
    <script>
        $(function(){
            var width = $('.page').width();
            var w_h = {{$w_h}}
            $('.page').height(width/w_h);

            // var promise = $.ajax({
            //     url : '/auto/make',
            //     data: {page: 2, width:width},
            //     type : 'GET',
            //     dataType : 'JSON'
            // });
            // //请求成功
            // promise.always(function(){
            //     //...pedding

            // })

            // //返回成功
            // promise.done(function(data){
            //     console.log(data)
            // })
        })
     
    </script>
@endsection