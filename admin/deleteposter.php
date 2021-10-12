<?php include_once(__DIR__.'/../functions.php');

session_start();

if(!isset($_SESSION['loggedin'])){
  header('Location: /admin/login.php');
  die();
}

$poster = basename($_GET['poster']);

if(empty($poster)){
  die('Missing file');
}

$config_changed = false;
foreach($config['posters'] as $i=>$p){
  if($p == $poster){ // poster is active, remove it from the list
    unset($config['posters'][$i]);
    $config_changed = true;
  }
}
if($config['permanent_poster'] == $poster){
  $config['permanent_poster'] = NULL;
  $config_changed = true;
}
if($config_changed){
  file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT));
}

$file = $posters_dir.$poster;

if(file_exists($file)){
  // delete file
  unlink($file);
}else{
  die('Error: File not found');
}

header('Location: /admin/');
die();
