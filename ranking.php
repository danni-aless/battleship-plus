<?php
include 'connection.php';
$query = "select username, partite_giocate, partite_vinte, data_iscrizione from login order by partite_vinte desc";
$result = mysqli_query($db_conn, $query);
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <!-- meta info -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Battleship+</title>

    <!-- bootstrap library -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
        <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th, {
            background-color: #f2f2f2;
        }
    </style>
    <!-- project's resources -->
    <link rel="icon" href="assets/cruise.png">
    <link rel="stylesheet" href="style.css">
    <script src="app.js" type="application/javascript"></script>
</head>

<body>
    <!-- webpage body -->
    <div id="webpage-body">
        <h1>Classifica</h1>
        <table>
            <tr>
                <th>Pos.</th>
                <th>Username</th>
                <th>Partite Giocate</th>
                <th>Partite Vinte</th>
                <th>Percentuale Vittorie</th>
                <th>Data Iscrizione</th>
            </tr>
            <?php
            $pos = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                if($row['partite_giocate']!=0){
                    $percentuale_vittorie = ($row['partite_vinte'] / $row['partite_giocate']) * 100;
                }else{
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
        </table>
    </div>
</body>

</html>