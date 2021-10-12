<?php include_once(__DIR__.'/../functions.php');

session_start();

if(!isset($_SESSION['loggedin'])){
  header('Location: /admin/login.php');
  die();
}

if(isset($_POST['save'])){ // Einstellungs-formular wurde abgeschickt (gespeichert)
  $config['last_updated'] = time();
  if($_POST['mode'] == 'auto' || $_POST['mode'] == 'poster' || $_POST['mode'] == 'home'){
    $config['mode'] = strtolower($_POST['mode']);
  }
  $config['posters'] = [];
  foreach($_POST['active'] as $poster){
    if(!empty($poster) && file_exists($posters_dir.$poster)){
      $config['posters'][] = $poster;
    }
  }
  if(!empty($_POST['permanent']) && file_exists($posters_dir.$_POST['permanent'])){
    $config['permanent_poster'] = $_POST['permanent'];
  }

  if(is_numeric($_POST['home_duration']) && $_POST['home_duration'] >= 5 && $_POST['home_duration'] <= 600){
    $config['home_duration'] = (int)$_POST['home_duration'];
  }
  if(is_numeric($_POST['poster_duration']) && $_POST['poster_duration'] >= 5 && $_POST['poster_duration'] <= 600){
    $config['poster_duration'] = (int)$_POST['poster_duration'];
  }

  $success = 'Einstellungen erfolgreich gespeichert!';

  file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT));
}


if(isset($_POST['submit'])){ // plakat upload form wurde abgeschickt
  if(isset($_FILES["upload"])){ // plakat hochladen
    $target_file = $posters_dir.basename($_FILES["upload"]["name"]);
    $ex = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if($ex != 'jpg' && $ex != 'jpeg' && $ex != 'png' && $ex != 'gif'){
      $error = 'Falsches Dateiformat. Bitte nur JPG, PNG und GIF Dateien verwenden.';
    }else{
      $check = getimagesize($_FILES["upload"]["tmp_name"]);
      if(!$check){
        $error = 'Dies ist kein Bild - Bitte nur JPG, PNG und GIF Dateien verwenden.';
      }else{
        if(file_exists($target_file)){
          $error = 'Plakat mit diesem Namen existiert bereits. Bitte Dateiname ändern und erneut versuchen.';
        }else{
          if($_FILES["upload"]["size"] > 10000000){ // file is bigger than 10 MB
            $error = 'Date zu groß. Bitte maximal 10MB Dateien verwenden.';
          }else{
            move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file);
            $success = 'Plakat erfolgreich hinzugefügt. Aktivieren nicht vergessen!';
          }
        }
      }
    }
  }
}


// get system uptime (https://stackoverflow.com/a/38933089)
$str   = @file_get_contents('/proc/uptime');
$num   = floatval($str);
$secs  = fmod($num, 60); $num = (int)($num / 60);
$mins  = $num % 60;      $num = (int)($num / 60);
$hours = $num % 24;      $num = (int)($num / 24);
$days  = $num;

// get os version (bottom of https://www.cyberciti.biz/faq/how-to-check-os-version-in-linux-command-line/)
$os = trim(str_replace('\l', '', str_replace('\n', '', shell_exec('cat /etc/issue'))));

// get all posters from the data/poster/ folder
$posters = getPosters();

header("Refresh: 300; URL=/admin/logout.php?auto"); // auto logout after 5 minutes (300 seconds)

include __DIR__.'/parts/top.php';
?>
<div class="navbar fixed top-0 left-0 right-0 w-100 mb-2 shadow-lg bg-base-content text-neutral-content">
  <div class="flex-1 px-2 mx-2">
    <span class="text-lg font-bold">
     FSG-TV Admin
    </span>
  </div>
  <div class="flex-none">
    <a href="/admin/logout.php">
      <button class="btn btn-primary">LOG OUT</button>
    </a>
  </div>
</div>

<div class="container max-w-4xl mx-auto mt-20">
  <div class="w-full shadow stats">
    <div class="stat place-items-center place-content-center">
      <div class="stat-title">FSG-TV Version</div>
      <div class="stat-value">1.2.0</div>
    </div>
    <div class="stat place-items-center place-content-center">
      <div class="stat-title">OS Version</div>
      <div class="stat-value text-success"><?php echo htmlentities($os); ?></div>
    </div>
    <div class="stat place-items-center place-content-center">
      <div class="stat-title">System Uptime</div>
      <div class="stat-value text-error"><?php echo htmlentities($days.'d '.$hours.'h '.$mins.'m'); ?></div>
    </div>
  </div>

  <?php
  if(!empty($error)){
  ?>
  <div class="mt-10 alert alert-error" style="justify-content: flex-start !important;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-6 h-6 mx-2 stroke-current">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
    </svg>
    <label><?php echo htmlentities($error); ?></label>
  </div>
  <?php
  }
  if(!empty($success)){
  ?>
  <div class="mt-10 alert alert-success" style="justify-content: flex-start !important;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-6 h-6 mx-2 stroke-current">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <label><?php echo htmlentities($success); ?></label>
  </div>
  <?php
  }
  ?>

  <div class="card w-full mt-10 bg-base-200 p-10 mb-10">
    <h2 class="text-4xl font-bold">Einstellungen</h2>
    <form action="/admin/index.php" method="post">
      <p class="mt-5"><b>Aktiver Modus:</b></p>
      <select name="mode" class="select select-bordered select-info w-full max-w-xs">
        <option disabled="disabled">Modus auswählen</option>
        <option value="auto" <?php if($config['mode'] == 'auto' || empty($config['mode'])){ echo 'selected'; } ?>>Auto-Durchlauf (Home → zufälliges Plakat)</option>
        <option value="poster" <?php if($config['mode'] == 'poster'){ echo 'selected'; } ?>>Dauerhaftes Plakat</option>
        <option value="home" <?php if($config['mode'] == 'home'){ echo 'selected'; } ?>>Dauerhafter Home-screen</option>
      </select>

      <div class="overflow-x-auto">
        <p class="mt-5"><b>Plakate-Manager:</b></p>
        <table class="table w-full">
          <thead>
            <tr>
              <th>Name</th>
              <th class="text-center">Aktiviert</th>
              <th class="text-center">Dauerplakat<br>(wenn Modus aktiviert)</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach($posters as $poster){
              $hash = md5($poster);
            ?>
            <tr>
              <td>
                <div class="flex items-center space-x-3">
                  <div class="avatar">
                    <div class="w-12 h-12 mask mask-square">
                      <img src="/data/poster/<?php echo htmlentities($poster); ?>" alt="Poster" style="border-radius: 5px;">
                    </div>
                  </div>
                  <div>
                    <div class="font-bold" style="word-break: break-word; white-space: pre-line;">
                      <?php echo htmlentities($poster); ?>
                    </div>
                    <div class="text-sm opacity-50">
                      <?php echo htmlentities(date('d.m.Y', filemtime($posters_dir.$poster))); ?>
                    </div>
                  </div>
                </div>
              </td>
              <td class="text-center">
                <label>
                  <input type="checkbox" class="checkbox" name="active[]" value="<?php echo htmlentities($poster); ?>" <?php if(in_array($poster, $config['posters'])){ echo 'checked'; } ?>>
                </label>
              </td>
              <td class="text-center">
                <label>
                  <input type="radio" class="radio" name="permanent" value="<?php echo htmlentities($poster); ?>" <?php if($config['permanent_poster'] == $poster){ echo 'checked'; } ?>>
                </label>
              </td>
              <td>
                <a href="#preview-modal-<?php echo htmlentities($hash); ?>">
                  <button type="button" class="btn btn-ghost btn-xs">Vorschau</button>
                </a>
                <a href="#delete-modal-<?php echo htmlentities($hash); ?>">
                  <button type="button" class="btn btn-ghost btn-xs alert-error" style="vertical-align: -3.5px;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="height:1.4em;width:1.4em;">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </a>
                <div id="preview-modal-<?php echo htmlentities($hash); ?>" class="modal">
                  <div class="modal-box">
                    <img src="/data/poster/<?php echo htmlentities($poster); ?>" alt="Poster">
                    <div class="modal-action">
                      <a href="#" class="btn">Schließen</a>
                    </div>
                  </div>
                </div>
                <div id="delete-modal-<?php echo htmlentities($hash); ?>" class="modal">
                  <div class="modal-box">
                    <h2><b><?php echo htmlentities($poster); ?></b></h2>
                    <p style="white-space: break-spaces;">Willst du dieses Poster wirklich <b>unwiederruflich löschen</b>? Das kann nicht rückgängig gemacht werden.</p>
                    <div class="modal-action">
                      <a href="/admin/deleteposter.php?poster=<?php echo urlencode($poster); ?>" class="btn btn-error">Löschen</a>
                      <a href="#" class="btn">Abbrechen</a>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="mt-10">
        <p><b>Anzeigedauer (bei Auto-Durchlauf):</b></p>
        <div class="grid grid-rows-1 grid-flow-col gap-4">
          <div class="col-span-2">
            <div class="form-control">
              <label class="label">
                <span class="label-text">Home-Screen (in Sekunden):</span>
              </label>
              <input type="number" name="home_duration" placeholder="Empfohlen: 60" class="input input-bordered max-w-xs" value="<?php echo htmlentities($config['home_duration']); ?>">
            </div>
          </div>
          <div class="col-span-2">
            <div class="form-control">
              <label class="label">
                <span class="label-text">Poster-Screen (in Sekunden):</span>
              </label>
              <input type="number" name="poster_duration" placeholder="Empfohlen: 25" class="input input-bordered max-w-xs" value="<?php echo htmlentities($config['poster_duration']); ?>">
            </div>
          </div>
        </div>
      </div>
      <button type="submit" name="save" class="btn btn-primary mt-10">Speichern</button>
    </form>
  </div>

  <div class="card w-full mt-5 bg-base-200 p-10 mb-20">
    <h2 class="text-4xl font-bold">Plakat hinzufügen</h2>
    <form action="/admin/index.php" method="post" enctype="multipart/form-data">

      <input type="file" name="upload">
      <button type="submit" name="submit" class="btn btn-primary mt-10">Plakat hinzufügen</button>
    </form>
  </div>


</div>


<?php

include __DIR__.'/parts/bottom.php';

?>
