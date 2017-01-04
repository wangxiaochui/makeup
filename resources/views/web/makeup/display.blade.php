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
    .page{background:url("{{$bg}}");background-size:100% 100%}


@endsection

@section('content')
    <div id="main">
        @foreach($data as $dk=>$dv)
        <div class="page" style="overflow:hidden">
            @foreach($dv as $ddk=>$ddv)
                <div class="phone" style="@if(isset($ddv['rotate'])) transform:rotate($ddv['rotate']}}deg);
                        -ms-transform:rotate({{$ddv['rotate']}}deg); 	/* IE 9 */
                        -moz-transform:rotate({{$ddv['rotate']}}deg)                 ; 	/* Firefox */
                        -webkit-transform:rotate({{$ddv['rotate']}}deg); /* Safari 和 Chrome */
                        -o-transform:rotate({{$ddv['rotate']}}deg); @endif  @if(isset($ddv['radius']))border-radius: {{$ddv['radius']}}%  @endif;width:{{$ddv['width']}}%;height:{{$ddv['height']}}%;position:absolute;top:{{$ddv['top']}}%;left:{{$ddv['left']}}%;overflow:hidden">
                    <img  src="{{$ddv['path']}}" style='width:{{$ddv['relative_width']}}%;height:{{$ddv['relative_height']}}%;position:relative;top:-{{$ddv['relative_cut_top']}}%;left:-{{$ddv['relative_cut_left']}}%;'>
                </div>
                <div class="bw" style="display: none;width:{{$ddv['width']}}%;height:{{$ddv['height']}}%"></div>
            @endforeach
        </div>
       
        @endforeach
    </div>

@endsection

@section('script')
    <script src="/js/jquery.min.js"></script>
    <script src="/js/jquery-ui.min.js"></script>
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
            //  var top = $('.page').eq(0).css('top');


                $(".page img").click(function(){
//                    var left = parseFloat($(this).css('left'));
//                    var top =  parseFloat($(this).css('top'));
//                    var real_left = parseFloat($(this).parent().css('left'));
//                    var real_top = parseFloat($(this).parent().css('top'));
//                    console.log(parseFloat(2*parseFloat($(this).css('top'))))
//                    if(left == 0)
//                    {
//                        var x1 = parseFloat(real_left);
//                        var x2 = parseFloat(real_left);
//                        var y1 = parseFloat(real_top)+parseFloat(2*parseFloat($(this).css('top')));
//                        var y2 = parseFloat(real_top);
//                    }else{
//                        var x1 = parseFloat(real_left)+parseFloat(2*parseFloat($(this).css('left')));
//                        var x2 = parseFloat(real_left);
//                        var y1 = parseFloat(real_top);
//                        var y2 = parseFloat(real_top);
//                    }
//                    console.log([x1,x2,y1,y2])
                    $(this).draggable({
                        containment: [],
                        drag : function(event, ui){

                        }
                    });
                })




        })


    </script>
@endsection