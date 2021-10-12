<?php include_once(__DIR__.'/functions.php');

// WETTER
$weather = getWeather();

// NEWS
$news = getNews();

// DATUM
$datum = getDateText();

if(empty($config['home_duration']) || !is_numeric($config['home_duration']) || $config['home_duration'] < 5 || $config['home_duration'] > 600){
  $config['home_duration'] = 60; // default
}

if($config['mode'] == 'home'){ // Dauer "Home" Ansicht
  header("Refresh: 240"); // repload every 4 mins to check if the mode changed and get new data
}elseif($config['mode'] == 'poster'){ // Dauer "Poster/plakat" Ansicht
  header("Location: poster.php"); // redirect
  die();
}else{ // Default: "Auto-Modus": Home -> random plakat -> home -> usw. ...
  header("Refresh: ".$config['home_duration']."; URL=posters.php"); // switch to "random posters page" after 1 minute
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Home | FSG-TV</title>
    <link rel="stylesheet" type="text/css" href="style.css?d=<?php echo date('G'); ?>">
    <link href="https://fonts.googleapis.com/css?family=Roboto|Lalezar" rel="stylesheet">
<script>
var today = new Date();
var h = <?php echo date('G'); ?>;
var m = <?php echo date('i'); ?>;
var s = <?php echo date('s'); ?>;
function startTime() {
    s++;

    if(s == 60){
      m++;
      s = 0;
    }
    if(m == 60){
      h++;
      m = 0;
    }
    if(h == 24){
      h = 0;
      m = 0;
      s = 0;
    }

    m = checkTime(m);
    s = checkTime(s);

    document.getElementById('txt').innerHTML =
    h + ":" + m + ":" + s;
    var t = setTimeout(startTime, 1000);
}

function checkTime(i) {
    if (i.toString().length < 2) {i = "0" + i};  // add zero in front of small numbers
    return i;
}
</script>
</head>
<body onload="startTime()">
 <div class="wrapper gradient">
 <div class="inner">

<?php if(date('m') == 12){ ?>
<script src="fallingsnow.js"></script>
<div id="snowflakeContainer">
    <p class="snowflake">*</p>
</div>
<?php } ?>


      <img src="/data/intern/fsg_logo_freigestellt_weiss.png" alt="fsg-logo" class="fsg-logo">

      <div class="left">
        <div class="time-and-date">
          <h1 id="uhrzeit"><div id="txt"></div></h1>
          <h2 id="datum" class="light-text"><?php echo htmlentities($datum); ?></h2>
        </div>

        <?php if(!empty($weather)){ ?>
        <table align="center" style="border-spacing: 20px 0;">
          <tr>
            <td><h2>Heute</h2></td>
            <td><h2>Morgen</h2></td>
            <td><h2>Übermorgen</h2></td>
          </tr>
          <tr>
            <td><img class="weather-img" src="<?php echo $weather['today']['img']; ?>" width="100px"></td>
            <td><img class="weather-img" src="<?php echo $weather['tomorrow']['img']; ?>" width="100px"></td>
            <td><img class="weather-img" src="<?php echo $weather['datomorrow']['img']; ?>" width="100px"></td>
          </tr>
          <tr>
            <td><h2><big><big><b><?php echo $weather['today']['temp']; ?>°C</b></big></big></h2></td>
            <td><h2><big><big><b><?php echo $weather['tomorrow']['temp']; ?>°C</b></big></big></h2></td>
            <td><h2><big><big><b><?php echo $weather['datomorrow']['temp']; ?>°C</b></big></big></h2></td>
          </tr>
          <tr>
            <td><h3 class="light-text"><?php echo $weather['today']['name']; ?></h3></td>
            <td><h3 class="light-text"><?php echo $weather['tomorrow']['name']; ?></h3></td>
            <td><h3 class="light-text"><?php echo $weather['datomorrow']['name']; ?></h3></td>
          </tr>
        </table>
      </div>
      <?php } ?>

      <?php /*
      <div class="right">
        <img class="dauer-home-poster" src="data/dauer_poster/home?time=<?php echo time(); ?>" onerror="this.style.display='none'"> <!-- dauer Home poster speciehrn unter data/dauer_poster/home.jpg oder home.png -->
        <h1><?php echo $text[0]; ?></h1>
        <p><big><big><?php echo $text[1]; ?></big></big></p>
      </div>
      */ ?>

      <div class="newsticker">
        <marquee class="marquee" loop="infinite" direction="left" scrolldelay="30" truespeed="30" scrollamount="3" style="width:100%;">
          <h2>
            <div style="padding: 12px;">
              <img src="data/intern/tagesschau.png" alt="TAGESSCHAU" height="100%" style="float:left;margin: 10 0;">
            </div>
            <?php foreach($news as $headline){ echo ' <font id="p">++++</font> <i>'.htmlentities($headline).'</i> '; } ?>
          </h2>
        </marquee>
      </div>

    </div>
  </div>

<script>
var nvg = new NoisyVerticalGradient(50, 400, ['#112233', '#223344'] );
var png = nvg.render_png();
document.getElementById('xyz').style.backgroundImage = png;
</script>

</body>
</html>
