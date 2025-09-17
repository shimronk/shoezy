<?php
session_start();
$_SESSION = [];
session_destroy();

$doc  = rtrim(str_replace('\\','/', $_SERVER['DOCUMENT_ROOT']), '/');
$dir  = rtrim(str_replace('\\','/', realpath(__DIR__)), '/');
$base = str_replace($doc, '', $dir);
if ($base === '') $base = '';

header('Location: ' . $base . '/index.php');
exit;
