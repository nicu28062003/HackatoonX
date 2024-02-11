<?php
//Connect to database
require 'connectDB.php';
date_default_timezone_set('Asia/Damascus');
$d = date("Y-m-d");
$t = date("H:i:sa");

$data = $_GET;

// Transformă array-ul într-o reprezentare JSON
$json_data = json_encode($data);

// Specifică calea și numele fișierului JSON
$nume_fisier = '/home/vladlen/Desktop/XXX/HackatoonX/rfidattendance/data.json';

// Scrie conținutul JSON în fișier
file_put_contents($nume_fisier, $json_data);

function LogErrorToFile($msg, $filename = null)
{
  if (empty($filename))
    $filename = LOG_FILE;

  $file = fopen($filename, 'a+');
  if ($file !== false) {
    $str = "[" . date('Y-m-d H:i:s') . '] ' . var_export($msg, true);
    $r = fwrite($file, $str . "\n");
    fclose($file);
  }
}

$last_card_uid = ""; // Variabilă pentru a stoca ultimul card_uid

if (isset($_GET['card_uid']) && isset($_GET['device_token'])) {

  $card_uid = $_GET['card_uid'];
  $device_uid = $_GET['device_token'];

  $sql = "SELECT * FROM devices WHERE device_uid=?";
  $result = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($result, $sql)) {
    echo "SQL_Error_Select_device";
    exit();
  } else {
    mysqli_stmt_bind_param($result, "s", $device_uid);
    mysqli_stmt_execute($result);
    $resultl = mysqli_stmt_get_result($result);
    if ($row = mysqli_fetch_assoc($resultl)) {
      $device_mode = $row['device_mode'];
      $device_dep = $row['device_dep'];
      if ($device_mode == 1) {
        $sql = "SELECT * FROM users WHERE card_uid=?";
        $result = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($result, $sql)) {
          echo "SQL_Error_Select_card";
          exit();
        } else {
          mysqli_stmt_bind_param($result, "s", $card_uid);
          mysqli_stmt_execute($result);
          $resultl = mysqli_stmt_get_result($result);

          if ($row = mysqli_fetch_assoc($resultl)) {
            //*****************************************************
            //An existed Card has been detected for Login or Logout

            LogErrorToFile($row['add_card'], "/home/vladlen/Documents/www/fest-project/admin/log/test.log");

            if ($row['add_card'] == 1) {
              $last_card_uid = $card_uid; // Actualizează ultimul card_uid

              if ($row['device_uid'] == $device_uid || $row['device_uid'] == 0) {
                $Uname = $row['username'];
                $Number = $row['serialnumber'];
                $sql = "SELECT * FROM users_logs WHERE card_uid=? AND checkindate=? AND card_out=0";
                $result = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($result, $sql)) {
                  echo "SQL_Error_Select_logs";
                  exit();
                } else {
                  mysqli_stmt_bind_param($result, "ss", $card_uid, $d);
                  mysqli_stmt_execute($result);
                  $resultl = mysqli_stmt_get_result($result);
                  //*****************************************************
                  // Verificare dacă utilizatorul este deja înregistrat și nu a ieșit încă
                  if ($row = mysqli_fetch_assoc($resultl)) {
                    if (!empty($row['timein']) && empty($row['timeout'])) {
                      // Utilizatorul este deja înregistrat și nu a ieșit încă
                      echo "Utilizatorul este deja înregistrat și nu a ieșit încă.";
                      exit();
                    }
                  } else {
                    //Login
                    $sql = "INSERT INTO users_logs (username, serialnumber, card_uid, device_uid, device_dep, checkindate, timein, timeout) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $result = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($result, $sql)) {
                      echo "SQL_Error_Select_login1";
                      exit();
                    } else {
                      $timeout = "00:00:00";
                      mysqli_stmt_bind_param($result, "sdssssss", $Uname, $Number, $card_uid, $device_uid, $device_dep, $d, $t, $timeout);
                      mysqli_stmt_execute($result);

                      echo "login" . $Uname;
                      exit();
                    }
                  }
                }
              } else {
                echo "Not Allowed!";
                exit();
              }
            } else if ($row['add_card'] == 0) {
              echo "Not registerd!";
              exit();
            }
          } else {
            echo "Not found!";
            exit();
          }
        }
      } else if ($device_mode == 0) {
        //New Card has been added
        $sql = "SELECT * FROM users WHERE card_uid=?";
        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)) {
          echo "SQL_Error_Select_card";
          exit();
        } else {
          mysqli_stmt_bind_param($result, "s", $card_uid);
          mysqli_stmt_execute($result);
          $resultl = mysqli_stmt_get_result($result);
          //The Card is available
          if ($row = mysqli_fetch_assoc($resultl)) {
            $sql = "SELECT card_select FROM users WHERE card_select=1";
            $result = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($result, $sql)) {
              echo "SQL_Error_Select";
              exit();
            } else {
              mysqli_stmt_execute($result);
              $resultl = mysqli_stmt_get_result($result);

              if ($row = mysqli_fetch_assoc($resultl)) {
                $sql = "UPDATE users SET card_select=0";
                $result = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($result, $sql)) {
                  echo "SQL_Error_insert";
                  exit();
                } else {
                  mysqli_stmt_execute($result);

                  $sql = "UPDATE users SET card_select=1 WHERE card_uid=?";
                  $result = mysqli_stmt_init($conn);
                  if (!mysqli_stmt_prepare($result, $sql)) {
                    echo "SQL_Error_insert_An_available_card";
                    exit();
                  } else {
                    mysqli_stmt_bind_param($result, "s", $card_uid);
                    mysqli_stmt_execute($result);

                    echo "available";
                    exit();
                  }
                }
              } else {
                $sql = "UPDATE users SET card_select=1 WHERE card_uid=?";
                $result = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($result, $sql)) {
                  echo "SQL_Error_insert_An_available_card";
                  exit();
                } else {
                  mysqli_stmt_bind_param($result, "s", $card_uid);
                  mysqli_stmt_execute($result);

                  echo "available";
                  exit();
                }
              }
            }
          } //The Card is new
          else {
            $sql = "UPDATE users SET card_select=0";
            $result = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($result, $sql)) {
              echo "SQL_Error_insert";
              exit();
            } else {
              mysqli_stmt_execute($result);
              $sql = "INSERT INTO users (card_uid, card_select, device_uid, device_dep, user_date) VALUES (?, 1, ?, ?, CURDATE())";
              $result = mysqli_stmt_init($conn);
              if (!mysqli_stmt_prepare($result, $sql)) {
                echo "SQL_Error_Select_add";
                exit();
              } else {
                mysqli_stmt_bind_param($result, "sss", $card_uid, $device_uid, $device_dep);
                mysqli_stmt_execute($result);

                echo "succesful";
                exit();
              }
            }
          }
        }
      }
    } else {
      echo "Invalid Device!";
      exit();
    }
  }
}

// Verifică dacă ultimul card_uid nu mai primește valoarea 1 pentru "add_card" și efectuează logout
if (!empty($last_card_uid)) {
  $sql = "SELECT add_card FROM users WHERE card_uid=?";
  $result = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($result, $sql)) {
    echo "SQL_Error_Select_last_card";
    exit();
  } else {
    mysqli_stmt_bind_param($result, "s", $last_card_uid);
    mysqli_stmt_execute($result);
    $resultl = mysqli_stmt_get_result($result);

    if ($row = mysqli_fetch_assoc($resultl)) {
      if ($row['add_card'] != 1) {
        // Realizează operațiunea de logout
        $sql = "UPDATE users_logs SET timeout=?, card_out=1 WHERE card_uid=? AND checkindate=? AND card_out=0";
        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)) {
          echo "SQL_Error_insert_logout1";
          exit();
        } else {
          mysqli_stmt_bind_param($result, "sss", $t, $last_card_uid, $d);
          mysqli_stmt_execute($result);

          echo "logout" . $Uname;
          exit();
        }
      }
    }
  }
}
