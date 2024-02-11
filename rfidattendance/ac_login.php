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

	require 'connectDB.php';

	$Usermail = $_POST['email'];
	$Userpass = $_POST['pwd'];

	if (empty($Usermail) || empty($Userpass) ) {
		header("location: login.php?error=emptyfields");
  		exit();
	}
	else if (!filter_var($Usermail,FILTER_VALIDATE_EMAIL)) {
          header("location: login.php?error=invalidEmail");
          exit();
    }
	else{
		$sql = "SELECT * FROM users WHERE email=?";
		$result = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($result, $sql)) {
			header("location: login.php?error=sqlerror");
  			exit();
		}
		else{
			mysqli_stmt_bind_param($result, "s", $Usermail);
			mysqli_stmt_execute($result);
			$resultl = mysqli_stmt_get_result($result);

			if ($row = mysqli_fetch_assoc($resultl)) {
				$pwdCheck = password_verify($Userpass, $row['password']);
				if ($pwdCheck == false) {
					header("location: login.php?error=wrongpassword");
  					exit();
				}
				else if ($pwdCheck == true) {
	        session_start();
					$_SESSION['Admin-name'] = $row['username'];
					$_SESSION['Admin-email'] = $row['email'];
          $sqla = "SELECT u.user_type FROM users AS u WHERE u.email = '$Usermail'";

          $result = $conn->query($sqla);

          if ($result->num_rows > 0)
            while($row = $result->fetch_assoc())
              if ($row['user_type'] == 'admin')
                header("location: index.php?login=success");
              else
                header("location: user_files.html");

					exit();
				}
			}
			else{
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