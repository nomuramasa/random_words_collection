<?php

class RandomWord
{
  private $word;

  private const WEBLIO_RANDOM_WORD_SITE_URL = 'https://www.weblio.jp/WeblioRandomSelectServlet';
  private const WEBLIO_REDIRECTED_SITE_URL_EXCEPT_WORD = 'https://www.weblio.jp/content/';
  private const WIKIPEDIA_RANDOM_WORD_API_URL = 'https://ja.wikipedia.org/w/api.php?action=query&format=json&list=random&rnlimit=1';

  public function get(): string
  {
    // ワード取得方法を無作為に決定
    $wordGetMethodNum = rand(1, 2);

    if ($wordGetMethodNum === 1) {
      // パターン1: Weblio辞典からワード取得
      $this->getFromWeblio();
    } else {
      // パターン2: WikipediaのAPIからワード取得
      $this->getFromWikipedia();
    }
    return $this->word;
  }

  private function getFromWeblio() :void
  {
    // Weblio辞典のURLからリダイレクト先URLを取得し、URL末尾のワードのみ取得
    $ch = curl_init(self::WEBLIO_RANDOM_WORD_SITE_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    $encordedWord = str_replace(self::WEBLIO_REDIRECTED_SITE_URL_EXCEPT_WORD, '', $redirectedUrl);
    $this->word = urldecode($encordedWord);

    // ワード内の「+」をスペースに変換
    if (strpos($this->word, '+') !== false) {
      $this->word = str_replace('+', ' ', $this->word);
    }
  }

  private function getFromWikipedia() :void
  {
    while (true) {
      $json = file_get_contents(self::WIKIPEDIA_RANDOM_WORD_API_URL);
      $result = json_decode($json, true);
      $this->word = $result['query']['random'][0]['title'];

      // ワード取得のやり直し: 日本語が含まれない or 特定の単語が含まれる or IPアドレス の場合
      if (
        !preg_match('/(?:\p{Hiragana}|\p{Katakana}|[一-龠々])/', $this->word)
        || strpos($this->word, '利用者') !== false
        || strpos($this->word, '出典を必要とする記事') !== false
        || strpos($this->word, 'ファイル') !== false
        || strpos($this->word, 'Wikipedia') !== false
        || strpos($this->word, 'の話題') !== false
        || strpos($this->word, 'カテゴリ') !== false
        || strpos($this->word, 'テンプレート') !== false
        || strpos($this->word, '今日は何の日') !== false
        || strpos($this->word, 'メチ') !== false
        || strpos($this->word, '依頼') !== false
        || strpos($this->word, '削除') !== false
        || strpos($this->word, 'ログ') !== false
        || strpos($this->word, '/') !== false
        || substr_count($this->word, '.') == 3
      ) {
        continue;
      } else {
        break;
      }
    }

    // 不要な文字列を削除
    if (strpos($this->word, ':') !== false) {
      $this->word = substr($this->word, strpos($this->word, ':') + 1);
    }
    if (strpos($this->word, '(') !== false) {
      $this->word = substr($this->word, 0, strpos($this->word, '('));
    }
    $dusts = ['~', '/', '-', 'jawiki', 'doc', '良質ピックアップ', '最近の出来事', '関連一覧'];
    foreach ($dusts as $dust) {
      if (strpos($this->word, $this->word) !== false) {
        $this->word = str_replace($dust, '', $this->word);
      }
    }
  }
}
