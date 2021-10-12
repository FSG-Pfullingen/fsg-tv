<?php include_once(__DIR__.'/../functions.php');

session_start();

$_SESSION['loggedin'] = false;
session_destroy();

$url = '/admin/login.php';
if(isset($_GET['auto'])){
  $url .= '?auto';
}

header('Location: '.$url);
die();
