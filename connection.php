<?php
    /*  stabilisce la connessione con il db: 
        username_default=root 
        password_default=root 
        nome_database=database
        porta_default=3306
    */
    $db_conn = new mysqli("localhost", "root", "root", "database", 3306)
    or  die("Connection failed".$conn->connect_error);
?>