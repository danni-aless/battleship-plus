<?php
    include "connection.php";
    $query = "select username, partite_giocate, partite_vinte, data_iscrizione from login order by partite_vinte desc";
    $result = mysqli_query($db_conn, $query);
?>

<link rel="stylesheet" href="table.css">

<h1>Classifica</h1>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Pos.</th>
                <th>Username</th>
                <th>Partite Giocate</th>
                <th>Partite Vinte</th>
                <th>Percentuale Vittorie</th>
                <th>Data Iscrizione</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $pos = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    if($row['partite_giocate']!=0) {
                        $percentuale_vittorie = ($row['partite_vinte'] / $row['partite_giocate']) * 100;
                    } else {
                        $percentuale_vittorie=0;
                    }
                    echo "<tr>";
                    echo "<td>" . $pos . "</td>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['partite_giocate'] . "</td>";
                    echo "<td>" . $row['partite_vinte'] . "</td>";
                    echo "<td>" . round($percentuale_vittorie, 2) . "%</td>";
                    echo "<td>" . $row['data_iscrizione'] . "</td>";
                    echo "</tr>";
                    $pos++;
                }
            ?>
        </tbody>
    </table>
</div>