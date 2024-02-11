<?php
session_start();
if (isset($_SESSION['Admin-name']) && $_SESSION['User-type'] == 'admin') {
  header("location: index.php");
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conectare</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
      $(document).ready(function(){
        $('.message a').click(function(){
          $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
          $('h1').animate({height: "toggle", opacity: "toggle"}, "slow");
        });
      });
    </script>
</head>
<body>
<header>
  <?php include 'header.php'; ?>
</header>
<main>
  <h1>Vă rugăm să vă autentificați</h1>
  <h1 id="reset" style="display: none;">Vă rugăm să introduceți adresa de e-mail pentru a trimite linkul de resetare a parolei</h1>
  <section>
    <div class="login-page">
      <div class="form">
        <?php  
          if (isset($_GET['error'])) {
            if ($_GET['error'] == "invalidEmail") {
                echo '<div class="alert alert-danger">Adresa de e-mail este invalidă!</div>';
            }
            elseif ($_GET['error'] == "sqlerror") {
                echo '<div class="alert alert-danger">Eroare de bază de date!</div>';
            }
            elseif ($_GET['error'] == "wrongpassword") {
                echo '<div class="alert alert-danger">Parolă greșită!</div>';
            }
            elseif ($_GET['error'] == "nouser") {
                echo '<div class="alert alert-danger">Această adresă de e-mail nu există!</div>';
            }
          }
          if (isset($_GET['reset'])) {
            if ($_GET['reset'] == "success") {
                echo '<div class="alert alert-success">Verificați-vă adresa de e-mail pentru linkul de resetare a parolei!</div>';
            }
          }
          if (isset($_GET['account'])) {
            if ($_GET['account'] == "activated") {
                echo '<div class="alert alert-success">Vă rugăm să vă conectați!</div>';
            }
          }
          if (isset($_GET['active'])) {
            if ($_GET['active'] == "success") {
                echo '<div class="alert alert-success">Linkul de activare a fost trimis! Verificați-vă adresa de e-mail.</div>';
            }
          }
        ?>
        <div class="alert1"></div>
        <form class="reset-form" action="reset_pass.php" method="post" enctype="multipart/form-data">
          <input type="email" name="email" placeholder="Adresă de e-mail..." required/>
          <button type="submit" name="reset_pass">Resetare</button>
          <p class="message"><a href="#">Conectare</a></p>
        </form>
        <form class="login-form" action="ac_login.php" method="post" enctype="multipart/form-data">
          <input type="password" name="pwd" id="pwd" placeholder="Parolă" required/>
          <button type="submit" name="login" id="login">Conectare</button>
          <p class="message">Ați uitat parola? <a href="#">Resetează parola</a></p>
        </form>
      </div>
    </div>
  </section>
</main>
</body>
</html>