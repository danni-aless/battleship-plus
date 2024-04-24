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

    <!-- project's resources -->
    <link rel="icon" href="assets/cruise.png">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- navigation bar -->
    <nav class="navbar navbar-expand-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="assets/cruise.png" alt="Battleship+" width="30" height="30">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="d-flex me-auto">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="rules.html">Regole</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="ranking.php">Classifica</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- webpage body -->
    <div id="area-di-gioco">
        <div id="area-navi"></div>
        <div id="area-giocatore">
            <div id="griglia-giocatore" class="griglia"></div>
            <div id="area-bottone-navi">
                <button type="button" class="btn btn-orange" draggable="false">Inizia la partita</button>
            </div>  
        </div>
        <div id="area-avversario">
            <div id="griglia-avversario" class="griglia"></div>
            <div id="area-powerup">
                <div class="row"></div>
            </div> 
        </div>
        <div id="area-chat"></div>
    </div>

    <!-- script to be loaded after page rendering -->
    <script src="app.js" type="application/javascript"></script>
</body>

</html>