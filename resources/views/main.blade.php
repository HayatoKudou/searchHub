<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
    </head>
    <body class="antialiased">
        <div>
            <form action="{{ url('/')}}" method="POST">
                @csrf
                <div style="text-align: center; padding: 50px 50px 0px 50px;">
                    <div>
                        <label>
                            <input type="checkbox" name="platform" value="google" checked>
                            <span>Google</span>
                        </label>
                        <label>
                            <input type="checkbox" name="platform" value="twitter">
                            <span>Twitter</span>
                        </label>
                    </div>
                    <input type="text" name="search_word" value = "{{ old('search_word') }}" placeholder="キーワードを入力" style="width: 30%;">
                </div>
            </form>
             
            <div>
                <div style="float: left; width: 50%;">
                @if(isset($googlTotalResults) && isset($googleSearchTime))
                    <p style="text-align: center; font-size: 15px;">約{{$googlTotalResults}}件 ({{$googleSearchTime}}秒)</p>
                @endif
                
                @if(isset($googleItems) && count($googleItems) > 0)
                    @foreach($googleItems as $googleItem)
                    <blockquote class="wp-block-quote">
                        <a href="<?php echo $googleItem['link']; ?>"><?php echo $googleItem['title']; ?></a>
                        <p><?php echo $googleItem['snippet']; ?></p>
                    </blockquote>
                    <br>
                    @endforeach
                @elseif(isset($search_flg))
                    <p>no result</p>
                @endif
                </div>

                <div style="float: left; width: 50%;">
                @if(isset($twitterTotalResults) && isset($twitterSearchTime))
                    <p style="text-align: center; font-size: 15px;">約{{$twitterTotalResults}}件 ({{$twitterSearchTime}}秒)</p>
                @endif

                @if(isset($twitterItems) && count($twitterItems) > 0)
                    @foreach($twitterItems as $twitterItem)
                    <blockquote class="wp-block-quote">
                        <?php echo $twitterItem->text; ?>
                    </blockquote>
                    <br>
                    @endforeach
                @elseif(isset($search_flg))
                    <p>no result</p>
                @endif
                </div>
            </div>

        </div>
    </body>
</html>
