<?php

class RandomWord
{
  private const WIKIPEDIA_RANDOM_WORD_API_URL = 'https://ja.wikipedia.org/w/api.php?action=query&format=json&list=random&rnlimit=1';

  public function get(): string
  {
      // パターン2: WikipediaのAPIからワード取得
      return $this->getFromWikipedia();
  }

  private function getFromWikipedia() :string
  {
    while (true) {
      $json = file_get_contents(self::WIKIPEDIA_RANDOM_WORD_API_URL);
      $result = json_decode($json, true);
      $word = $result['query']['random'][0]['title'];

      // ワード取得のやり直し: 日本語が含まれない or 特定の単語が含まれる or IPアドレス の場合
      if (
        !preg_match('/(?:\p{Hiragana}|\p{Katakana}|[一-龠々])/', $word)
        || strpos($word, '利用者') !== false
        || strpos($word, '出典を必要とする記事') !== false
        || strpos($word, 'ファイル') !== false
        || strpos($word, 'Wikipedia') !== false
        || strpos($word, 'の話題') !== false
        || strpos($word, 'カテゴリ') !== false
        || strpos($word, 'テンプレート') !== false
        || strpos($word, '今日は何の日') !== false
        || strpos($word, 'メチ') !== false
        || strpos($word, '依頼') !== false
        || strpos($word, '削除') !== false
        || strpos($word, 'ログ') !== false
        || strpos($word, '/') !== false
        || substr_count($word, '.') == 3
      ) {
        continue;
      } else {
        break;
      }
    }

    // 不要な文字列を削除
    if (strpos($word, ':') !== false) {
      $word = substr($word, strpos($word, ':') + 1);
    }
    if (strpos($word, '(') !== false) {
      $word = substr($word, 0, strpos($word, '('));
    }
    $dusts = ['~', '/', '-', 'jawiki', 'doc', '良質ピックアップ', '最近の出来事', '関連一覧'];
    foreach ($dusts as $dust) {
      if (strpos($word, $word) !== false) {
        $word = str_replace($dust, '', $word);
      }
    }

    return $word;
  }
}
