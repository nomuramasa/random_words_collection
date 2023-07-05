<?php
$ch = curl_init('https://www.weblio.jp/WeblioRandomSelectServlet');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
curl_close($ch);
$encordedWord = str_replace('https://www.weblio.jp/content/', '', $redirectedUrl);
$word = urldecode($encordedWord);
echo $word;