<?php
    session_start();
    include "connection.php";
    $username = $_SESSION['username'];
    $query = "select * from login where username = '$username'";
    $result = mysqli_query($db_conn, $query);
    $row = mysqli_fetch_assoc($result);
    $userid = $row['userid'];
    $query = "select * from partite where giocatore = $userid";
    $result = mysqli_query($db_conn, $query);
?>

<link rel="stylesheet" href="table.css">

<h1>Partite giocate</h1>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Data partita</th>
                <th>Avversario</th>
                <th>Risultato</th>
            </tr>
        </thead>
        <tbody>
            <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    $avversario = $row['avversario'];
                    if(is_numeric($avversario)) {
                        $query2 = "select * from login where userid = $avversario";
                        $result2 = mysqli_query($db_conn, $query2);
                        $row2 = mysqli_fetch_assoc($result2);
                        $avversario = $row2['username'];
                    }
                    echo "<tr>";
                    echo "<td>" . $row['data'] . "</td>";
                    echo "<td>" . $avversario . "</td>";
                    echo "<td>" . $row['risultato'] . "</td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
</div>