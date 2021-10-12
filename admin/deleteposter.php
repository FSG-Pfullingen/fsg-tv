<?php include_once(__DIR__.'/../functions.php');

session_start();

if(!isset($_SESSION['loggedin'])){
  header('Location: /admin/login.php');
  die();
}

$poster = $_GET['poster'];

if(empty($poster)){
  die('Missing file');
}

$file = $posters_dir.basename($poster);

if(file_exists($file)){
  // delete file
  unlink($file);
}else{
  die('Error: File not found');
}

header('Location: /admin/');
die();
