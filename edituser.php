<?php
session_start();
include "connection.php";

$email = $_SESSION['email'];

if (isset($_POST['editEmail'])) {
    $newEmail = $_POST['inputEmail'];
    $query = "select * from login where email = '$newEmail'";
    $result = mysqli_query($db_conn,$query);
    if (mysqli_num_rows($result) == 0) {
        $query_update = "update login set email = '$newEmail' where email = '$email'";
        $result = mysqli_query($db_conn,$query_update);
        $_SESSION['email'] =  $newEmail;
    }else{
        $_SESSION['edit_err_msg'] =  "Email Non Disponibile!";
    }
} elseif (isset($_POST['editUsername'])) {
    $newUsername = $_POST['inputUsername'];
    $query = "select * from login where username = '$newUsername'";
    $result = mysqli_query($db_conn,$query);
    if (mysqli_num_rows($result) == 0) {
        $query_update = "update login set username = '$newUsername' where email = '$email'";
        $result = mysqli_query($db_conn,$query_update);
        $_SESSION['username'] =  $newUsername;
    }else{
        $_SESSION['edit_err_msg'] =  "Username Non Disponibile!";
    }
} elseif (isset($_POST['editPassword'])) {
    $newPassword = $_POST['inputPassword'];
    $query = "select password from login where email = '$email'";
    $result = mysqli_query($db_conn, $query);
    $row = mysqli_fetch_assoc($result);
    $currentPassword = $row['password'];
    
    if ($newPassword != $currentPassword) {
        $query_update = "update login set password = '$newPassword' where email = '$email'";
        $result = mysqli_query($db_conn, $query_update);
        $_SESSION['password'] =  $newPassword;
    } else {
        $_SESSION['edit_err_msg'] =  "La nuova password deve essere diversa dalla password attuale!";
    }
} elseif (isset($_POST['deleteUser'])) {
    $inputPassword = $_POST['inputPassword'];
    $query = "select * from login where email = '$email'";
    $result = mysqli_query($db_conn, $query);
    $row = mysqli_fetch_assoc($result);
    $currentPassword = $row['password'];
    $imageToDelete = $row['image'];

    if ($inputPassword === $currentPassword) {
        $query_delete = "delete from login where email = '$email'";
        $result = mysqli_query($db_conn, $query_delete);

        $imagePath = "img/" . $imageToDelete;
        unlink($imagePath);
        
        session_destroy();
    } else {
        $_SESSION['edit_err_msg'] =  "Password Errata!";
    }
} 
header("location:index.php");
?>
