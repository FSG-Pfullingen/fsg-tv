<?php include_once(__DIR__.'/../functions.php');

session_start();

if(isset($_GET['auto'])){
  $info = 'Du wurdest automatisch nach 5 Minuten Inaktivität ausgeloggt.';
}

if(!empty($_POST)){
  if(empty($config['pass'])){ // first time: save the password!
    if(!empty($_POST['pass'])){
      $config['pass'] = password_hash($_POST['pass'], PASSWORD_DEFAULT);
      file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT));
      $info = 'Passwort gespeichert! Bistte logge dich nun ein.';
    }
  }else{
    if(password_verify($_POST['pass'], $config['pass'])){
      $_SESSION['loggedin'] = true;
      header('Location: /admin');
      die();
    }else{
      $error = 'Falsches Passwort!';
    }
  }
}

if(empty($config['pass'])){
  $info = 'Du hast noch kein Passwort gesetzt! Wähle jetzt ein Passwort';
}

include __DIR__.'/parts/top.php';
?>

<div class="flex h-screen justify-center items-center">
  <div class="container max-w-md mx-auto">
    <?php if(!empty($info)){ ?>
    <div class="alert alert-info mb-5" style="justify-content: flex-start !important;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-6 h-6 mx-2 stroke-current">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
      </svg>
      <label><?php echo $info; ?></label>
    </div>
    <?php } ?>
    <?php if(!empty($error)){ ?>
    <div class="alert alert-error mb-5" style="justify-content: flex-start !important;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-6 h-6 mx-2 stroke-current">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
      </svg>
      <label><?php echo $error; ?></label>
    </div>
    <?php } ?>
    <div class="p-10 card bg-base-200">
      <form action="/admin/login.php" method="post">
        <div class="form-control">
          <label class="label">
            <span class="label-text">
              <b>FSG-TV Admin</b> &mdash; Bitte einloggen:
            </span>
          </label>
          <input type="password" placeholder="Passwort" name="pass" class="input" autofocus required>
          <button class="btn btn-primary mt-5">Einloggen</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php

include __DIR__.'/parts/bottom.php';

?>
