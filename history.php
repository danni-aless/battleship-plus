<?php
    session_start();
    include 'connection.php';
    $username = $_SESSION['username'];
    $query = "select * from partite where giocatore='$username'";
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
                    echo "<tr>";
                    echo "<td>" . $row['data'] . "</td>";
                    echo "<td>" . $row['avversario'] . "</td>";
                    echo "<td>" . $row['risultato'] . "</td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
</div>