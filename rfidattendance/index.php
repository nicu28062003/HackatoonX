<?php

session_start();

function LogErrorToFile($msg, $filename = null)
{
  if (empty($filename))
    $filename = LOG_FILE;

  $file = fopen($filename, 'a+');
  if ($file !== false)
  {
    $str = "[".date('Y-m-d H:i:s').'] '. var_export($msg, true);
    $r = fwrite($file, $str."\n");
    fclose($file);
  }
}

LogErrorToFile($_SESSION, "/home/vladlen/Documents/www/fest-project/admin/log/test.log");


if (!isset($_SESSION['Admin-name'])) {
  header("location: login.php");
}
if ($_SESSION['User-type'] !== 'admin')
  header("location: user_files.php");
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studenți</title>
    <link rel="icon" type="image/png" href="images/favicon.png">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/Users.css">
</head>
<body>
<header>
  <?php include 'header.php'; ?> 
</header>
<main>
<section>
  <div class="container">
    <div class="row">
      <div class="col-lg-12 text-center">
        <h1 class="display-4 mb-5">Studenții UTM</h1>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="thead-dark">
              <tr style="background-color: rgba(0,0,0,0.22);">
                <th>ID | Nume</th>
                <th>Numărul Serial</th>
                <th>Sexul</th>
                <th>UID Card</th>
                <th>Data</th>
                <th>Dispozitiv</th>
              </tr>
            </thead>
            <tbody>
              <?php
                //Conectare la baza de date
                require 'connectDB.php';

                $sql = "SELECT * FROM users WHERE add_card=1 ORDER BY id DESC";
                $result = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($result, $sql)) {
                    echo '<p class="error">Eroare SQL</p>';
                } else {
                    mysqli_stmt_execute($result);
                    $resultl = mysqli_stmt_get_result($result);
                    if (mysqli_num_rows($resultl) > 0) {
                        while ($row = mysqli_fetch_assoc($resultl)) {
              ?>
              <tr style="background-color: rgba(0,0,0,0.22);">
                <td><?php echo $row['id']; echo" | "; echo $row['username'];?></td>
                <td><?php echo $row['serialnumber'];?></td>
                <td><?php echo $row['gender'];?></td>
                <td><?php echo $row['card_uid'];?></td>
                <td><?php echo $row['user_date'];?></td>
                <td><?php echo $row['device_dep'];?></td>
              </tr>
              <?php
                        }
                    }
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
</main>
</body>
</html>
