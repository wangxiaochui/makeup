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
                    <img id="img{{$ddk}}" class="photo"  src="{{$ddv['path']}}" style='width:{{$ddv['relative_width']}}%;height:{{$ddv['relative_height']}}%;position:relative;top:-{{$ddv['relative_cut_top']}}%;left:-{{$ddv['relative_cut_left']}}%;'>
                </div>
                <div class="bw" style="display: none;width:{{$ddv['width']}}%;height:{{$ddv['height']}}%"></div>
            @endforeach
        </div>
       
        @endforeach
    </div>

@endsection

</div>
@section('script')
    <script src="/js/jquery.min.js"></script>
    <script src="/js/hammer.js"></script>
    <script>
        $(function() {
            var width = $('.page').width();
            var w_h = {{$w_h}}
            $('.page').height(width / w_h);
            var height = width / w_h;

            var list=$('.page img');

            $.each(list, function(k,v){
                console.log(v)
                var disX=0;
                var disY=0;
                var oDiv = v;
                var ori_left = oDiv.offsetLeft;
                var ori_top= oDiv.offsetTop;

                oDiv.onmousedown=function(ev)
                {

                    var oEvent=ev||event;
                    var pos=getPos(oEvent);  //这样就可以获取鼠标坐标，比如pos.x表示鼠标横坐标
                    disX=pos.x-oDiv.offsetLeft;
                    disY=pos.y-oDiv.offsetTop;

                    document.onmousemove=function(ev)
                        /*由于要防止鼠标滑动太快跑出div，这里应该用document.
                         因为触发onmousemove时要重新获取鼠标的坐标，不能使用父函数上的pos.x和pos.y，所以必须写var oEvent=ev||event;var pos=getPos(oEvent);*/
                    {
                        var oEvent=ev||event;
                        var pos=getPos(oEvent);

                        var l=pos.x-disX;
                        var t=pos.y-disY;

                        if(ori_top == 0)
                        {
                            t = 0;
                            if(l>0){

                                l=0;
                            }else if(l<2*ori_left){
                                l = 2*ori_left;
                                console.log(l);
                            }
                            else if(l>document.documentElement.clientWidth-oDiv.offsetWidth)
                            {
                                l=document.documentElement.clientWidth-oDiv.offsetWidth;
                            }
                        }

                        if(ori_left == 0){
                            l=0;
                            if(t>0)
                            {
                                t=0;
                            }else if(t<2*ori_top){

                                t = 2*ori_top;
                                //console.log(t)
                            }
                            else if(t>document.documentElement.clientHeight-oDiv.offsetHeight)
                            {
                                t=document.documentElement.clientHeight-oDiv.offsetHeight;

                            }
                        }
                        console.log(l)

                        oDiv.style.left=l+'px';
                        oDiv.style.top=t+'px';
                    }

                    //移动端重新写吧,没法兼容
                    document.addEventListener('touchmove',function(ev){
                        //ev.preventDefault();
                        console.log('aaa')
                        var oEvent=ev||event;
                        var pos=getPos(oEvent);

                        var l=pos.x-disX;
                        var t=pos.y-disY;

                        if(ori_top == 0)
                        {
                            t = 0;
                            if(l>0){

                                l=0;
                            }else if(l<2*ori_left){
                                l = 2*ori_left;
                                console.log(l);
                            }
                            else if(l>document.documentElement.clientWidth-oDiv.offsetWidth)
                            {
                                l=document.documentElement.clientWidth-oDiv.offsetWidth;
                            }
                        }

                        if(ori_left == 0){
                            l=0;
                            if(t>0)
                            {
                                t=0;
                            }else if(t<2*ori_top){

                                t = 2*ori_top;
                            }
                            else if(t>document.documentElement.clientHeight-oDiv.offsetHeight)
                            {
                                t=document.documentElement.clientHeight-oDiv.offsetHeight;

                            }
                        }
                        console.log(l)

                        oDiv.style.left=l+'px';
                        oDiv.style.top=t+'px';
                    } )


                    document.onmouseup=function(ev)
                    {
                        document.onmousemove=null; //将move清除
                        document.onmouseup=null;
                    }

                    return false;  //火狐的bug，要阻止默认事件
                }
            })




            //封装一个函数用于获取鼠标坐标
            function getPos(ev)
            {
                var scrollTop=document.documentElement.scrollTop||document.body.scrollTop;
                var scrollLeft=document.documentElement.scrollLeft||document.body.scrollLeft;

                return {x: ev.clientX+scrollLeft, y: ev.clientY+scrollTop};
            }

        })

    </script>
@endsection