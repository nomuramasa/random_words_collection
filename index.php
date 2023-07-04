<!DOCTYPE html>
<html>

<head>
  <title>ランダム単語コレクション</title>
  <meta name='viewport' content='width=device-width'> <!-- スマホの為のビューポート -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
  <link href='https://fonts.googleapis.com/icon?family=Material+Icons' rel='stylesheet'> <!-- マテリアルアイコン -->
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet"> <!-- Font Awesome -->
  <link rel='stylesheet' href='css/style.css'>
  <link rel="shortcut icon" href="./favicon.ico">
</head>

<body>
  <nav class='navbar navbar-expand-sm bg-dark'>
    <div class='container'>
      <h5><a href='/'>ランダム単語コレクション</a></h5>
    </div>
  </nav>

  <!-- 最初の説明文 -->
  <div id='only_once' class='d-none text-sm-center my-4 balloon rounded rounded-lg p-3 mx-auto col-lg-7'>
    <p class='mb-0 text-sm-center'>
      ボタンを押すたびに、ランダムな単語がリストに追加されます。
      <br class='d-none d-md-block'>
      ブログのネタ探し、暇つぶし、作品のアイデア出しなどにご利用ください！
    </p>
  </div>

  <!-- ランダムワード生成ボタン -->
  <div class='text-center mb-4 btn-div'>
    <span class='px-5 position-relative'>
      <!-- はてなマーク -->
      <?php for ($count = 1; $count <= 30; $count++) : ?>
        <i class='fas fa-question hatena<?php echo $count; ?>'></i>
      <?php endfor; ?>
      <a onclick=getWord() id='make_word' class='btn btn-warning text-center position-relative'>ランダムワード生成 ＋</a>
    </span>
  </div>

  <?php
    // ワード押下時の遷移先のサイト
    if (!isset($_COOKIE['dest_site'])) {
      $_COOKIE['dest_site'] = 'google';
    }
    $destSites = [
      ['name' => 'google', 'color' => 'dark'],
      ['name' => 'youtube', 'color' => 'danger'],
      ['name' => 'twitter', 'color' => 'primary']
    ];
  ?>

  <!-- テーブルヘッダー -->
  <table class='table
    <?php if ($_COOKIE['dest_site'] == 'twitter') : ?>
      blue
    <?php elseif ($_COOKIE['dest_site'] == 'youtube') : ?>
      red
    <?php endif; ?>
  '>
    <thead>
      <tr class='row'>
        <th class='d-none d-sm-block col-2 col-lg-1'></th>
        <th class='col-10 col-sm-8 col-lg-9'>
          <?php foreach ($destSites as $destSite) : ?>
            <a id='request_ajax' onClick="changeDestSite('<?php echo $destSite['name']; ?>')" class='btn btn-<?php echo $destSite['color']; ?> text-light btn-sm <?php if ($_COOKIE['dest_site'] != $destSite['name']) : ?>mute<?php endif; ?>'>
              <?php echo $destSite['name']; ?>
              <?php if ($_COOKIE['dest_site'] == $destSite['name']) : ?>
                で検索
              <?php endif; ?>
            </a>
          <?php endforeach; ?>

        </th>
        <th class='col-1 col-lg-1'><span class='d-none d-md-block'>目印</span></th>
        <th class='col-1 col-lg-1'><span class='d-none d-md-block'>削除</span></th>
      </tr>
    </thead>

    <!-- テーブルボディ -->
    <tbody id='tb'></tbody>
  </table>

  <script>
    let session = '<?php echo $_COOKIE['dest_site']; ?>'
  </script>
  <script src="js/script.js"></script>
</body>
</html>