<?php
    session_start();
    include "connection.php";

    //assicura che il login avvenga solo se è stato premuto il bottone
    if(isset($_POST['login'])) {  
        //salva i campi 
        $emailusername = $_POST["inputEmailUsername"];
        $password = $_POST["inputPassword"];

        //query che prende dal db la riga in cui trova e-mail o username giusti e password corrispondente
        $query = "select * from login where (email='$emailusername' or username='$emailusername') and password='$password'"; 
        
        //prendo risultato query e metto in un array
        $result = mysqli_query($db_conn,$query);

        if($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] =  $row['username'];
            $_SESSION['email'] =  $row['email'];
            $_SESSION['password'] =  $row['password'];
            $_SESSION['image'] =  $row['image'];
        } else {
            $_SESSION['login_err_msg'] =  "Email o Password Errati!";
        }
        header("location:index.php");
    }
?>