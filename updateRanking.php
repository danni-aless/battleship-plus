<?php
    session_start();
    include "connection.php";

    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {        
        $username = $_SESSION['username'];

        $query = "update login set partite_giocate = partite_giocate + 1 where username='$username'";
        mysqli_query($db_conn, $query);
        
        if($_POST['type']==="win") {
            $query = "update login set partite_vinte = partite_vinte + 1 where username='$username'";
            mysqli_query($db_conn, $query);
        }
    }
?>