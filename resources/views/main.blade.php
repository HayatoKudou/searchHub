<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Search Hub</title>
    <link href="css/app.css" rel="stylesheet" type="text/css">
</head>

<body class="antialiased">
    <div>
        <form action="{{ url('/')}}" method="POST">
            @csrf
            <div style="text-align: center; padding: 40px;">
                <h3 style="font-weight: bold;">Search Hub</h3>
                <input type="search" name="search_word" value="{{ old('search_word', isset($defaultSearchWord) ? $defaultSearchWord : '') }}" placeholder="キーワードを入力" style="width: 40%;">
            </div>
        </form>

        <div>
            <div style="float: left; width: 100%;">
                <div>
                    <div style="width: 57%; text-align: right; display: inline-block;">
                        <label>
                            <input type="radio" name="showResultPlatform" value="google" onclick="changeShowResult(this.value)" checked>
                            <span>Google</span>
                        </label>
                        <label>
                            <input type="radio" name="showResultPlatform" value="twitter" onclick="changeShowResult(this.value)">
                            <span>Twitter</span>
                        </label>
                    </div>
                    <div style="width: 40%; text-align: center; display: inline-block;">
                        <nav class="nav01">
                            <ul>
                                <li>
                                    <a href="#">Book List</a>
                                    <ul>
                                        <li><a href="#">sub nav</a></li>
                                        <li><a href="#">sub nav</a></li>
                                        <li><a href="#">sub nav</a></li>
                                    </ul>
                                </li>
                                <!-- <li>
                            <a href="#">nav</a>
                            <ul>
                                <li><a href="#">sub nav</a></li>
                                <li>
                                    <a href="#">sub nav</a>
                                    <ul>
                                        <li><a href="#">child</a></li>
                                        <li><a href="#">child</a></li>
                                        <li><a href="#">child</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">sub nav</a>
                                    <ul>
                                        <li><a href="#">child</a></li>
                                        <li><a href="#">child</a></li>
                                        <li><a href="#">child</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li> -->
                            </ul>
                        </nav>
                    </div>
                </div>

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

            </div>

            <div style="float: left; width: 20%;">
            </div>
        </div>
    </div>

    <script language="javascript" type="text/javascript">
        function changeShowResult(showResultPlatform) {
            if (showResultPlatform === 'google') {
                document.getElementById('google_result').style.display = 'block';
                document.getElementById('twitter_result').style.display = 'none';

            } else if (showResultPlatform === 'twitter') {
                fetch(location.href + 'api/searchTwitter', {
                        method: 'POST',
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                        },
                        body: JSON.stringify({
                            "search_word": document.getElementsByName('search_word')[0].value
                        })
                    })
                    .then(response => response.json())
                    .then(json => {
                        document.getElementById('google_result').style.display = 'none';
                        document.getElementById('twitter_result').style.display = 'block';
                        if (json.twitterTotalResults == 0) {
                            document.getElementById('twitter_result').innerHTML = "<p>no result</p>";
                        } else {
                            var html = "<p style='text-align: center; font-size: 15px;'>約" + json.twitterTotalResults + '件' + '(' + json.twitterSearchTime + "秒)</p>"
                            "<p style='text-align: center; font-size: 15px;'>約" + json.twitterTotalResults + '件' + '(' + json.twitterSearchTime + "秒)</p>";

                            var html2 = json.twitterItems.map(data => {
                                if (data.entities.media !== undefined && data.entities.media.expanded_url !== undefined) {
                                    return (
                                        '<blockquote class="wp-block-quote">' +
                                        "<a class='block-a' href=" + data.entities.media.expanded_url + " target='_blank'>" + data.entities.media.expanded_url + "</a>" +
                                        "<p class='block-p'>" + data.text + "</p>" +
                                        '</blockquote>'
                                    )
                                } else {
                                    return (
                                        '<blockquote class="wp-block-quote">' +
                                        "<a class='block-a' href=https://twitter.com/" + data.user.screen_name + "/status/" + data.id_str + " target='_blank'>" + "https://twitter.com/" + data.user.screen_name + "/status/" + data.id_str + "</a>" +
                                        "<p class='block-p'>" + data.text + "</p>" +
                                        '</blockquote>'
                                    )
                                }
                            });
                            document.getElementById('twitter_result').innerHTML = html + html2.join('');
                        }
                    })
            }
        }

        function setBook(url, title) {
            var BookList = JSON.parse(localStorage.getItem("BookList"));
            if (BookList) {
                Object.keys(BookList).map(key => {
                    console.log(BookList[key])
                    if (BookList[key].url == url) {
                        return
                    }
                })
                BookList.push({
                    url: url,
                    title: title
                })
                localStorage.setItem("BookList", JSON.stringify(BookList));
            } else {
                BookList = {
                    url: url,
                    title: title
                }
                localStorage.setItem("BookList", JSON.stringify([BookList]));
            }
        }
    </script>

</body>

</html>