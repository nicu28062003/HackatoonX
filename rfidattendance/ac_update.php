<?php 
session_start();
require('connectDB.php');

if (isset($_POST['update'])) {

    $useremail = $_SESSION['Admin-email'];

    $up_name = $_POST['up_name'];
    $up_email = $_POST['up_email'];
    $up_password =$_POST['up_pwd'];

    if (empty($up_name) || empty($up_email)) {
        header("location: index.php?error=emptyfields");
        exit();
    }
    elseif (!filter_var($up_email,FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Z 0-9]*$/", $up_name)) {
        header("location: index.php?error=invalidEN&UN=".$up_name);
        exit();
    }
    elseif (!filter_var($up_email,FILTER_VALIDATE_EMAIL)) {
        header("location: index.php?error=invalidEN&UN=".$up_name);
        exit();
    }
    elseif (!preg_match("/^[a-zA-Z 0-9]*$/", $up_name)) {
        header("location: index.php?error=invalidName&E=".$up_email);
        exit();
    }
    else{
        $sql = "SELECT * FROM users WHERE email=?";
        $result = mysqli_stmt_init($conn);
        if ( !mysqli_stmt_prepare($result, $sql)){
            header("location: index.php?error=sqlerror1");
            exit();
        }
        else{
            mysqli_stmt_bind_param($result, "s", $useremail);
            mysqli_stmt_execute($result);
            $resultl = mysqli_stmt_get_result($result);
            if ($row = mysqli_fetch_assoc($resultl)) {
                $pwdCheck = password_verify($up_password, $row['password']);
                if ($pwdCheck == false) {
                    header("location: index.php?error=wrongpasswordup");
                    exit();
                }
                else if ($pwdCheck == true) {
                  if ($useremail == $up_email) {
                    $sql = "UPDATE users SET username=?, user_type=? WHERE email=?";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                      header("location: index.php?error=sqlerror");
                      exit();
                    }
                    else{
                      mysqli_stmt_bind_param($stmt, "sss", $up_name, $user_type, $useremail);
                      mysqli_stmt_execute($stmt);
                      $_SESSION['Admin-name'] = $up_name;
                      $_SESSION['user_type'] = $user_type; // Add user_type to session
                      header("location: index.php?success=updated");
                      exit();
                    }
                  }
                  else{
                    $sql = "SELECT admin_email, user_type FROM users WHERE email=?";
                    $result = mysqli_stmt_init($conn);
                    if ( !mysqli_stmt_prepare($result, $sql)){
                      header("location: index.php?error=sqlerror1");
                      exit();
                    }
                    else{
                      mysqli_stmt_bind_param($result, "s", $up_email);
                      mysqli_stmt_execute($result);
                      $resultl = mysqli_stmt_get_result($result);
                      if ($row = mysqli_fetch_assoc($resultl)) {
                        $user_type = $row['user_type']; // Get user_type from database
                        $sql = "UPDATE users SET username=?, email=?, user_type=? WHERE email=?";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                          header("location: index.php?error=sqlerror");
                          exit();
                        }
                        else{
                          mysqli_stmt_bind_param($stmt, "ssss", $up_name, $up_email, $user_type, $useremail);
                          mysqli_stmt_execute($stmt);
                          $_SESSION['Admin-name'] = $up_name;
                          $_SESSION['Admin-email'] = $up_email;
                          $_SESSION['user_type'] = $user_type; // Add user_type to session
                          header("location: index.php?success=updated");
                          exit();
                        }
                      }
                      else{
                        header("location: index.php?error=nouser2");
                        exit();
                      }
                    }
                  }
                }
            }
            else{
                header("location: index.php?error=nouser1");
                exit();
            }
        }
    }
}
else{
    header("location: index.php");
    exit();
}
//*********************************************************************************
?>