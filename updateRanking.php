<?php
    session_start();
    include "connection.php";

    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {        
        $username = $_SESSION['username'];
        $avversario = $_POST['opponent'];

        $query = "update login set partite_giocate = partite_giocate + 1 where username='$username'";
        mysqli_query($db_conn, $query);
        
        if($_POST['result']==="win") {
            $query = "update login set partite_vinte = partite_vinte + 1 where username='$username'";
            mysqli_query($db_conn, $query);
            $query = "insert into partite (giocatore, avversario, risultato, data) values ('$username', '$avversario', 'Vittoria', NOW())";
            mysqli_query($db_conn, $query);
        } else if($_POST['result']==="lost") {
            $query = "insert into partite (giocatore, avversario, risultato, data) values ('$username', '$avversario', 'Sconfitta', NOW())";
            mysqli_query($db_conn, $query);
        }
    }
?>