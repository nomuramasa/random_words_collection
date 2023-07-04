<?php
include_once('./RandomWord.php');

$randomWord = new RandomWord();
$word = $randomWord->get();

header('Content-Type: application/json');
echo json_encode($word);
