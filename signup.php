<?php
        session_start();
        include "connection.php";

        if(isset($_POST['signup'])){  
        
            //salva i campi 
            $email = $_POST["inputEmail"];
            $password = $_POST["inputPassword"];
            $username = $_POST["inputUsername"];
    
            //query che controlla se l'email è gia presa
            $query = "select * from login where email = '$email' or username='$username'";
            $result = mysqli_query($db_conn,$query);
            if (mysqli_num_rows($result) == 0) {
                $query_insert = "insert into login (`username`,`email`, `password`) values ('$username', '$email', '$password')";
                $result = mysqli_query($db_conn,$query_insert);
                $_SESSION['logged_in'] = true;
                $_SESSION['username'] =  $username;
                $_SESSION['email'] =  $email;
                $_SESSION['password'] =  $password;
            }else{
                $_SESSION['signup_err_msg'] =  "Email o Username Non Disponibili!";
            }
            header("location:index.php");
        } 
?>