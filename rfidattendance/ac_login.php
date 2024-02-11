<?php
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



if (isset($_POST['login'])) {

  $nume_fisier = '/home/vladlen/Desktop/XXX/HackatoonX/rfidattendance/data.json';

  $json_data = file_get_contents($nume_fisier);

  $data = json_decode($json_data, true);

  $uid =  $data['card_uid'];



	require 'connectDB.php';

	$UserUID = $data['card_uid'];;
	$Userpass = $_POST['pwd'];

	if (empty($UserUID) || empty($Userpass) ) {
		header("location: login.php?error=emptyfields");
  		exit();
	}
  else {
    $sql = "SELECT u.user_type, u.username, u.email, u.password FROM users AS u WHERE u.card_uid = ?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
      header("location: login.php?error=sqlerror");
      exit();
    } else {
      mysqli_stmt_bind_param($stmt, "s", $UserUID);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      $row = mysqli_fetch_assoc($result);

      if ($row) {
        $pwdCheck = password_verify($Userpass, $row['password']);
        if ($pwdCheck == false) {
          header("location: login.php?error=wrongpassword");
          exit();
        } else if ($pwdCheck == true) {
          session_start();
          $_SESSION['Admin-name'] = $row['username'];
          $_SESSION['Admin-email'] = $row['email'];
          $_SESSION['User-type'] = $row['user_type']; // Salvăm tipul de utilizator în sesiune

          if ($row['user_type'] == 'admin') {
            header("location: index.php?login=success");
          } else {
            header("location: user_files.php");
          }
          exit();
        }
      } else {
        header("location: login.php?error=nouser");
        exit();
      }
    }
  }

  mysqli_stmt_close($result);
mysqli_close($conn);
}
else{
  header("location: login.php");
  exit();
}
?>