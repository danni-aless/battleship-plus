<?php
     /* stabilisce la connessione con il db : 
        username_default=root 
        password_default=root 
        database_login = nome datbase su mySQL
        porta default di xamp 3306
        */
    $db_conn = new mysqli("localhost", "root", "root", "database_login", 3306)
    or  die("Connection failed".$conn->connect_error);
?>