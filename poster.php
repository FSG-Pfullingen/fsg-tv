<?php include_once(__DIR__.'/functions.php');

// get permanent poster
$images_dir = 'data/poster/';
$poster = $config['permanent_poster'];

if($config['mode'] == 'home'){ // Dauer "Home" Ansicht
  header("Location: home.php"); // redirect
  die();
}elseif($config['mode'] == 'poster'){ // Dauer "Poster/plakat" Ansicht
  header("Refresh: 120"); // repload every 2 mins to check if the mode changed and get new data
}else{ // Default: "Auto-Modus": Home -> random plakat -> home -> usw. ...
  header("Location: home.php"); // redirect
  die();
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Poster | FSG-TV</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
    html,body{
      margin:0;
      height:100%;
      width: 100%;
    }
    body{
      background: black;
      overflow: hidden;
    }
    #intro{
      display: block;
      width:100%; height:100%;
      object-fit: cover;
    }
    #photo{
      width: 100%;
      height: 100%;
      background: url('<?php echo $images_dir.$poster; ?>');
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
    }
    </style>
      </head>
      <body>

    <?php if(date('m') == 12){ ?>
    <script src="fallingsnow.js"></script>
    <div id="snowflakeContainer">
      <p class="snowflake">*</p>
    </div>
    <?php } ?>

    <img id="intro" src="data/intern/intro3.gif" alt="FEHLER :(">
    <div id="photo"></div>

    <script>
    window.setTimeout(function(){
      document.getElementById('intro').style.display = 'none';
    }, 5000);
    </script>

  </body>
</html>
