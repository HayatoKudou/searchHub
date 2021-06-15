<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Search Hub</title>
    <link href="css/app.css" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
</head>

<body class="antialiased">
    <div>
        <form action="{{ url('/')}}" method="POST">
            @csrf
            <div class="header">
                <nav class="nav01" style="margin-right: 10px;">
                    <ul>
                        <li>
                            <a href="#">BookMark List</a>
                            <ul id="BookMarkList">
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
            <div style="text-align: center; padding: 40px;">
                <h3 style="font-weight: bold;">Search Hub</h3>
                <input type="search" name="search_word" value="{{ old('search_word', isset($defaultSearchWord) ? $defaultSearchWord : '') }}" placeholder="キーワードを入力" style="width: 40%;">
            </div>
        </form>

        <div>
            <div style="float: left; width: 100%;">
                @if(isset($search_flg))
                <div>
                    <div style="text-align: center;">
                        <label>
                            <input type="radio" name="showResultPlatform" value="google" onclick="changeShowResult(this.value)" checked>
                            <span>Google</span>
                        </label>
                        <label>
                            <input type="radio" name="showResultPlatform" value="twitter" onclick="changeShowResult(this.value)">
                            <span>Twitter</span>
                        </label>
                        <label>
                            <!-- <input type="checkbox" onclick="showTrend(this.value)"> -->
                            <input type="checkbox" onclick="renderGraph(this.value)">
                            <span>トレンド表示</span>
                        </label>
                    </div>
                </div>
                @endif

                <div class="result_form" style="display: flex;">
                    <div id="google_result">
                        @if(isset($googlTotalResults) && isset($googleSearchTime))
                        <p style="text-align: center; font-size: 15px;">約{{$googlTotalResults}}件 ({{$googleSearchTime}}秒)</p>
                        @endif

                        @if(isset($googleItems) && count($googleItems) > 0)
                        @foreach($googleItems as $googleItem)
                        <blockquote class="wp-block-quote">
                            <a class="block-a" href="{{$googleItem['link']}}" target='_blank'>{{$googleItem['title']}}</a>
                            <button value="{{$googleItem['link']}}" title="{{$googleItem['title']}}" onclick="setBook(this.value, this.title)">ブックマーク</button>
                            <p class="block-p">{{$googleItem['snippet']}}</p>
                        </blockquote>
                        @endforeach
                        @elseif(isset($search_flg))
                        <p>no result</p>
                        @endif
                    </div>

                    <div id="twitter_result"></div>

                    <canvas id="myLineChart"></canvas>
                </div>

            </div>

            <div style="float: left; width: 20%;">
            </div>
        </div>
    </div>
    <script src="{{ asset('/js/main.js') }}"></script>
</body>

</html>