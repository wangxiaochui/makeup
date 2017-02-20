@extends('layouts.app')

@section('title')
    test
@endsection


@section('content')
    <div id='main-index'>

        <div id='content'>
            @foreach($detail as $dk=>$dv)

            <div class='page'>
                <div class='image'>
                @foreach($dv as $pk=>$pv)
                    @if(isset($pv['img']))


                    @foreach($pv['img'] as $ik=>$iv)
                        <div style="left:{{$iv['left']}}px;top:{{$iv['top']}}px;width:{{$iv['width']}}px;height:{{$iv['height']}}px;background-image:url({{$iv['img_path']}});" class="image loaded" data-msg-id="{{$dk}}" >

                        </div>
                    @endforeach



                    @endif
                @endforeach
                </div>
                    <svg mxlns='http://www.w3.org/2000/svg' version='1.1' >
                        @foreach($dv as $tk=>$tv)
                            @if(isset($tv['text']))

                                <g class="text">
                                @foreach($tv['text'] as $tk=>$tv)
                                    @if(is_array($tv))
                                        <text x="{{$tv['x']}}" y={{$tv['y']}} class="text" font-size="14" data-msg-id="16232986" fill="#000">{{$tv['text']}}</text>
                                    @endif
                                @endforeach
                                </g>


                            @endif
                        @endforeach

                            @foreach($dv as $dk=>$dv)
                                @if(isset($dv['time']))

                                    <g class="label">
                                        <g class="label" data-msg-id="16232986">

                                            <rect x="0" y="{{$dv['time'][5]}}" width="40" height="20" rx="2" ry="2"></rect>
                                            <text class="date" y="{{$dv['time'][4]}}" x="20" text-anchor="middle">
                                                {{$dv['time'][2]}}
                                            </text>
                                            <text class="time" x="20" y="{{$dv['time'][6]}}" text-anchor="middle" fill="#fff">
                                                {{$dv['time'][3]}}
                                            </text>
                                        </g>
                                    </g>


                                @endif
                            @endforeach
                    </svg>
            </div>
            @endforeach
        </div>

    </div>
@endsection
<script>

</script>