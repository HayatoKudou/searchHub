<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Log;

class AppController extends Controller
{
    public function index()
    {
        return view('main');
    }

    public function search(Request $request)
    {
        Log::debug($request);
        $googleItems = array();
        $googleSearchTime = 0;
        $googlTotalResults = 0;

        // list($googleItems, $googleSearchTime, $googlTotalResults) = $this->serachGoogle($request);
        list($twitterItems, $twitterSearchTime, $twitterTotalResults) = $this->searchTwitter($request);

        return view('main')->with([
            'googleItems' => $googleItems, 
            'googleSearchTime' => $googleSearchTime, 
            'googlTotalResults' => $googlTotalResults,

            'twitterItems' => $twitterItems, 
            'twitterSearchTime' => $twitterSearchTime, 
            'twitterTotalResults' => $twitterTotalResults,

            'search_flg' => true
        ]);
    }

    function serachGoogle($request)
    {
        $api_key = "AIzaSyDSXWnJva0gVrR6rYs3-sq32MzhUOdrvm4";
        $engine_id = "b3edae044d87813e2";
        $search_word = $request->search_word;

        $parm = array(
            'key' => $api_key,
            'cx' => $engine_id,
            'q' => $search_word,
            'alt' => 'json', // JSON形式で取得する
            'start' => 1 // 取得開始順位(検索結果1~10位を取得するを取得する)
        );
        $parm = http_build_query($parm);

        $response = Http::get('https://www.googleapis.com/customsearch/v1/?'.$parm);
        $response = json_decode($response, true);

        $searchTime = $response['searchInformation']['searchTime'];
        $totalResults = $response['searchInformation']['formattedTotalResults'];
        
        return [$response['items'], $searchTime, $totalResults];
    }

    function searchTwitter($request)
    {
        $consumer_key = 'dCuSs0sAbGTq1gGeLFPw8fsE4';
        $consumer_key_sercret = 'HQgAyK43WeZ1MXHU2EGpCO0IXW86rRQMUV307Xx0IWvLOJPWtj';
        $access_token = '905201668995784705-Uk8zwsxYV7aicz2dTQiH0RWOz5SwPLg';
        $access_token_secret = 'OL79qI5VyPKeyyWTIlRAM40ZVptulF7vwXecy94IPQO6B';

        $totalResults = 0;
        $searchTime = microtime(true);

        $connection = new TwitterOAuth($consumer_key, $consumer_key_sercret, $access_token, $access_token_secret);
        $tweets = $connection->get('search/tweets', [
            'q' => $request->search_word, 
            'count' => 10
        ]);

        $totalResults = count($tweets->statuses);
        $searchTime = microtime(true) - $searchTime;
        
        return [$tweets->statuses, $searchTime, $totalResults];
    }
}
