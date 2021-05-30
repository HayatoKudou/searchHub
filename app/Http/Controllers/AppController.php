<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Log;

class AppController extends Controller
{
    public function index()
    {
        return view('main');
    }

    public function show(Request $request)
    {
        $googleItems = array();
        $googleSearchTime = 0;
        $googlTotalResults = 0;

        list($googleItems, $googleSearchTime, $googlTotalResults) = $this->serachGoogle($request);

        return view('main')->with([
            'googleItems' => $googleItems,
            'googleSearchTime' => $googleSearchTime,
            'googlTotalResults' => $googlTotalResults,
            'search_flg' => true,
            'defaultSearchWord' => $request->search_word
        ]);
    }

    function serachGoogle($request)
    {
        $items = array();
        $api_key = Config::get('token.google_api_key');
        $engine_id = Config::get('token.google_engine_id');
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

        $searchTime = substr($response['searchInformation']['searchTime'], 0, 6);
        $totalResults = $response['searchInformation']['formattedTotalResults'];

        if(isset($response['items'])){
            $items = $response['items'];
        }
        return [$items, $searchTime, $totalResults];
    }

    function searchTwitter(Request $request)
    {
        $consumer_key = Config::get('token.consumer_key');
        $consumer_key_sercret = Config::get('token.consumer_key_sercret');
        $access_token = Config::get('token.access_token');
        $access_token_secret = Config::get('token.access_token_secret');

        $searchTime = microtime(true);

        $connection = new TwitterOAuth($consumer_key, $consumer_key_sercret, $access_token, $access_token_secret);
        $tweets = $connection->get('search/tweets', [
            'q' => $request->search_word,
            'count' => 50
        ]);

        $totalResults = isset($tweets->statuses) ? count($tweets->statuses) : 0;
        $searchTime = microtime(true) - $searchTime;

        return [
            'twitterItems' => isset($tweets->statuses) ? $tweets->statuses : [],
            'twitterSearchTime' => substr($searchTime, 0, 6), 
            'twitterTotalResults' => $totalResults,
        ];
    }

    function getTrends()
    {
        $searchFilter = (new SearchFilter())
            ->withCategory(0) //All categories
            ->withSearchTerm('google')
            ->withLocation('JP')
            ->considerWebSearch()
            ->withinInterval(
                new \DateTimeImmutable('now -7 days'),
                new \DateTimeImmutable('now')
            )
            ->withTopMetrics()
            ->withRisingMetrics();

        $result = (new RelatedQueriesSearch())
            ->search($searchFilter)
            ->jsonSerialize();

        Log::debug(print_r($result, true));
    }
}
