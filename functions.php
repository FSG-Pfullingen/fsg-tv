<?php

/*
//DEBUGGING:
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

// get data from config file
$config_file = __DIR__.'/config.json';
if(!file_exists($config_file)){
  $config = [
    'pass' => NULL,
    'last_updated' => time(),
    'mode' => 'auto',
    'home_duration' => 60,
    'poster_duration' => 25,
    'posters' => [],
    'permanent_poster' => ''
  ];
  file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT));
}else{
  $config = json_decode(file_get_contents($config_file), true);
}

$posters_dir = __DIR__.'/data/poster/';

// WEATHER - Rufe das wetter von der (internen) API desWetter-widgets von pfullingen.de ab und cache es für 1 Stunde
function getWeather(){
  $weather = [];
  $cachefile = __DIR__.'/.cache/weather.cache';
  if(file_exists($cachefile) && filemtime($cachefile) >= strtotime('-2 hour')){ //cache file existiert und ist jünger als 2 stunen
    $weather = json_decode(file_get_contents($cachefile), true);
  }else{
    //$get = file_get_contents('https://wetter.ceasy.de/pub/index.php?projectKey=stadtpfullingen&format=json&_='.urlencode(time()));

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://wetter.ceasy.de/pub/index.php?projectKey=stadtpfullingen&format=json&_=1634037366');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $headers = [
      'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:93.0) Gecko/20100101 Firefox/93.0',
      'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
      'Accept-Language: de,en-US;q=0.7,en;q=0.3'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $get = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($get, true);
    $days = $json['data']['days'];

    $today = $days['d0'];
    $tomorrow = $days['d1'];
    $datomorrow = $days['d2']; // übermorgen (day-after-tomorrow)

    if(!empty($today['text'])){
      $weather = [
        'today' => [
          'name' => str_replace(' ', '<br>', htmlentities($today['text'])),
          'temp' => round($today['currentTemp']),
          'img' => 'https://wetter.ceasy.de/pub/resources//weatherIcons/big/'.$today['imgId']
        ],
        'tomorrow' => [
          'name' => str_replace(' ', '<br>', htmlentities($tomorrow['text'])),
          'temp' => round(($tomorrow['minTemp']+$tomorrow['maxTemp'])/2),
          'img' => 'https://wetter.ceasy.de/pub/resources//weatherIcons/big/'.$tomorrow['imgId']
        ],
        'datomorrow' => [
          'name' => str_replace(' ', '<br>', htmlentities($datomorrow['text'])),
          'temp' => round(($datomorrow['minTemp']+$datomorrow['maxTemp'])/2),
          'img' => 'https://wetter.ceasy.de/pub/resources//weatherIcons/big/'.$datomorrow['imgId']
        ]
      ];

      file_put_contents($cachefile, json_encode($weather));
    }
  }
  return $weather;
}


// NEWS - rufe den RSS Feed der tagesschau ab und cache die heatlines/titel es für 1 Stunde
function getNews(){
  $cachefile = __DIR__.'/.cache/tagesschau.cache';
  if(file_exists($cachefile) && filemtime($cachefile) >= strtotime('-1 hour')){ //cache file existiert und ist jünger als 1 stunde
    $headlines = json_decode(file_get_contents($cachefile), true);
  }else{
    $headlines = [];
    $xml = simplexml_load_string(file_get_contents('http://www.tagesschau.de/xml/rss2'));
    $entries = $xml->channel->item;
    foreach($entries as $entry){
      $headlines[] = trim(strip_tags($entry->title));
    }
    file_put_contents($cachefile, json_encode($headlines));
  }
  return $headlines;
}

function getPosters(){
  global $posters_dir;
  $posters = [];
  $paths = glob($posters_dir.'*.{jpg,jpeg,png,gif}', GLOB_BRACE);
  foreach($paths as $path){
    $posters[] = basename($path);
  }
  return $posters;
}

function getDateText(){
  $trans = array(
      'Monday'    => 'Montag',
      'Tuesday'   => 'Dienstag',
      'Wednesday' => 'Mittwoch',
      'Thursday'  => 'Donnerstag',
      'Friday'    => 'Freitag',
      'Saturday'  => 'Samstag',
      'Sunday'    => 'Sonntag',
      'Mon'       => 'Mo',
      'Tue'       => 'Di',
      'Wed'       => 'Mi',
      'Thu'       => 'Do',
      'Fri'       => 'Fr',
      'Sat'       => 'Sa',
      'Sun'       => 'So',
      'January'   => 'Januar',
      'February'  => 'Februar',
      'March'     => 'März',
      'May'       => 'Mai',
      'June'      => 'Juni',
      'July'      => 'Juli',
      'October'   => 'Oktober',
      'December'  => 'Dezember'
  );
  $datum = date("l, j. F Y");
  $datum = strtr($datum, $trans);
  return $datum;
}
