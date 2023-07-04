// 新ワード追加ボタン
const getWord = function () {

  document.getElementById('make_word').innerHTML = '<i class="fa fa-spinner fa-spin"></i>　ワード 生成中　<i class="fa fa-spinner fa-spin"></i>' // loadingマーク
  fetch('api/get_word.php')
    .then(response => response.json())
    .then(data => {
      // ストレージに保存する値をセット
      let key = 'random_word_collection' + generateUniqueStr(); // 一意な文字列
      let name = data; // ランダムワード
      let star = 0; // スターなし
      let visit = 0; // リンク未訪問
      saveStorage(key, name, star, visit);
      document.getElementById('make_word').innerHTML = 'ランダムワード生成 ＋' // ボタンの文字を元に戻しておく
    })
    .catch(error => {
      console.error('通信に失敗しました', error);
      document.getElementById('make_word').innerHTML = 'ランダムワード生成 ＋' // ボタンの文字を元に戻しておく
    });
}

// ローカルストレージのデータを表に出力
const viewStorage = function () {
  const tb = document.getElementById('tb') // tbody

  // テーブルの初期化
  while (tb.firstChild) { tb.removeChild(tb.firstChild) }

  // テーブルの出力
  for (let i = localStorage.length - 1; i >= 0; i--) {

    let id = localStorage.key(i);
    let getjson = localStorage.getItem(id);
    let obj = JSON.parse(getjson); // JSON → オブジェクト

    // 行
    let tr = document.createElement('tr');
    tr.classList.add('row');
    tb.appendChild(tr);

    //// New
    let td1 = document.createElement('td');
    td1.classList.add('d-none', 'd-sm-block', 'col-2', 'col-lg-1');
    tr.appendChild(td1);
    let deco;
    if (obj.visit == 0) { deco = 'NEW' } else { deco = '' }
    td1.innerHTML = '<div class="new">' + deco; + '</div>'

    //// 検索ワード
    let td2 = document.createElement('td');
    td2.classList.add('col-9', 'col-sm-8', 'col-lg-9', 'word');
    tr.appendChild(td2);

    // サイトURLをセット（Google・Youtube・Twitter）
    if (session == 'google') {
      dest_site_url = 'https://www.google.com/search?q='
    } else if (session == 'youtube') {
      dest_site_url = 'https://www.youtube.com/results?search_query='
    } else if (session == 'twitter') {
      dest_site_url = 'https://twitter.com/search?q='
    }

    td2.innerHTML = '<a onclick="changeStorage(\'' + id + '\',\'visit\')" href="' + dest_site_url + '' + obj.name + '" target="_blank" class="d-block">' + obj.name + '</a>';

    //// スター
    let td3 = document.createElement('td');
    td3.classList.add('col-1_half', 'col-sm-1');
    tr.appendChild(td3);
    let color;
    if (obj.star == 0) { color = 'nostar' } else { color = 'star' }
    td3.innerHTML = '<a onclick="changeStorage(\'' + id + '\',\'star\')" class=" ' + color + '"><i class="material-icons">star</i></a>';

    //// ごみ箱
    let td4 = document.createElement('td');
    td4.classList.add('col-1_half', 'col-sm-1');
    tr.appendChild(td4);

    td4.innerHTML = '<a onclick="removeStorage(\'' + id + '\')" class="trash"><i class="material-icons">delete</i></a>';
  }
}

// ストレージに追加
const saveStorage = function (key, name, star, visit) {
  let value = { name: name, star: star, visit: visit }; // オブジェクトを作る
  let setjson = JSON.stringify(value); //JSON形式にエンコード
  if (key && value.name && value) {
    localStorage.setItem(key, setjson); //ストレージへ追加
  }
  key = ''; value = []; // 初期化  これでif(value)に引っ掛かるのか心配
  viewStorage();
  document.getElementById('only_once').classList.remove('d-block'); //　最初の吹き出しを消す
}

// ストレージの値を更新
const changeStorage = function (id, purpose) {
  let getjson = localStorage.getItem(id); // 受け取ったidの行を選択
  let obj = JSON.parse(getjson); // JSONをオブジェクトに（中身を見る為）
  let name = obj.name; // 単語名
  let star = obj.star; // スター
  let visit = obj.visit; // 訪問

  if (purpose == 'star') { // スター目的の場合
    if (obj.star == 0) { star = 1 } else { star = 0 }  // スター変更
  }
  if (purpose == 'visit') { // 訪問チェック目的の場合
    visit = 1
  }
  saveStorage(id, name, star, visit);
}

// 特定のワードを削除
const removeStorage = function (id) {
  localStorage.removeItem(id);
  id = '';
  viewStorage();
  document.getElementById('only_once').classList.remove('d-block'); //最初の吹き出しを消す
};

// 全て削除
const clearStorage = function () {
  localStorage.clear();
  viewStorage();
};

// ユニークなIDを生成
const generateUniqueStr = function (myStrong) {
  let strong = 1000;
  if (myStrong) strong = myStrong;
  return new Date().getTime().toString(16) + Math.floor(strong * Math.random()).toString(16)
}

// ワード押下時の遷移先のサイト変更
const changeDestSite = function (destSite) {
  document.cookie = `dest_site=${destSite}`
  window.location.reload();
}

// ページ読み込み完了時 
window.onload = function () {
  viewStorage();
}

// 最初の吹き出し
document.addEventListener('DOMContentLoaded', function(event) { 
  if (sessionStorage.getItem('visited') == null) { // sessionにvisitedがまだ無いとき（つまりこのタブで最初のアクセスのとき）
      document.getElementById('only_once').classList.add('d-block'); // 表示クラスを付ける
      sessionStorage.setItem('visited', 1) // フラグを立てる
  }
});