window.onload = function () {
    getBookMarkList();
    localStorage.setItem("renderTrendFlg", false);
};

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
        var flg = false;
        Object.keys(BookList).map(key => {
            if (BookList[key].url == url) {
                flg = true;
                return;
            }
        })
        if (!flg) {
            BookList.push({
                url: url,
                title: title
            })
            localStorage.setItem("BookList", JSON.stringify(BookList));
        }
    } else {
        BookList = {
            url: url,
            title: title
        }
        localStorage.setItem("BookList", JSON.stringify([BookList]));
    }
    getBookMarkList();
}

function getBookMarkList() {
    var ul = document.getElementById('BookMarkList');
    var BookList = JSON.parse(localStorage.getItem("BookList"));
    if (BookList) {
        BookList.map(data => {
            if (!document.getElementsByClassName(data.url).length) {
                var li = document.createElement('li');
                li.setAttribute('class', data.url);
                var a = document.createElement('a');
                a.setAttribute('href', data.url);
                a.setAttribute('target', '_blank');
                var text = document.createTextNode(data.title);
                a.appendChild(text);
                li.appendChild(a);
                ul.appendChild(li);
            }
        })
    }
}

function getTrend() {
    fetch(location.href + 'api/getTrends', {
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
        console.log(json)
        return json;
    })
}

function renderGraph(check_val) {
    if(check_val){
        
    } else {

    }
    var trendData = '';
    var renderTrendFlg = localStorage.getItem("renderTrendFlg");
    if(renderTrendFlg == 'false'){
        trendData = getTrend();
        localStorage.setItem("renderTrendFlg", true);
    }
    console.log(trendData)
    var ctx = document.getElementById("myLineChart");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['8月1日', '8月2日', '8月3日', '8月4日', '8月5日', '8月6日', '8月7日'],
            datasets: [
                {
                    label: '最高気温(度）',
                    data: [35, 34, 37, 35, 34, 35, 34, 25],
                    borderColor: "rgba(255,0,0,1)",
                    backgroundColor: "rgba(0,0,0,0)"
                },
                {
                    label: '最低気温(度）',
                    data: [25, 27, 27, 25, 26, 27, 25, 21],
                    borderColor: "rgba(0,0,255,1)",
                    backgroundColor: "rgba(0,0,0,0)"
                }
            ],
        },
        options: {
            title: {
                display: true,
                text: '気温（8月1日~8月7日）'
            },
            scales: {
                yAxes: [{
                    ticks: {
                        suggestedMax: 40,
                        suggestedMin: 0,
                        stepSize: 10,
                        callback: function (value, index, values) {
                            return value + '度'
                        }
                    }
                }]
            },
        }
    });
}