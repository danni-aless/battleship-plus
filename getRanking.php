<?php
    function getRanking($username) {
        include "connection.php";
        $query = "select pos from (select username, rank() over (order by partite_vinte desc) as pos from login) ranking where username='$username'";
        $result = mysqli_query($db_conn, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['pos'];
    }
?>