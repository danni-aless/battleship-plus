<?php session_start(); ?>

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

    <!-- jquery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>

    <!-- socket.io library -->
    <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

    <!-- fontawesome kit -->
    <script src="https://kit.fontawesome.com/977cc8c5e1.js" crossorigin="anonymous" defer></script>

    <!-- project's resources -->
    <link rel="icon" href="assets/cruise.png">
    <link rel="stylesheet" href="style.css">
    <script>
        $(document).ready(function() {
            $('#regole').click(function(e) {
                e.preventDefault(); 
                $.ajax({
                    type: 'GET',
                    url: 'rules.php',
                    success: function(data) {
                        $('#ajax-modal .modal-title').html($($.parseHTML(data)).filter('h1').text());
                        $('#ajax-modal .modal-body').html($($.parseHTML(data)).filter(':not(h1)'));
                        $('#ajax-modal').modal('show');
                    },
                });
            });
            $('#classifica').click(function(e) {
                e.preventDefault(); 
                $.ajax({
                    type: 'GET',
                    url: 'ranking.php',
                    success: function(data) {
                        $('#ajax-modal .modal-title').html($($.parseHTML(data)).filter('h1').text());
                        $('#ajax-modal .modal-body').html($($.parseHTML(data)).filter(':not(h1)'));
                        $('#ajax-modal').modal('show');
                    }
                });
            });
            $('#partite-giocate').click(function(e) {
                e.preventDefault(); 
                $.ajax({
                    type: 'GET',
                    url: 'history.php',
                    success: function(data) {
                        $('#ajax-modal .modal-title').html($($.parseHTML(data)).filter('h1').text());
                        $('#ajax-modal .modal-body').html($($.parseHTML(data)).filter(':not(h1)'));
                        $('#ajax-modal').modal('show');
                    }
                });
            });
        });
    </script>
    <script src="game.js"></script>
    
</head>

<body>
    <!-- navigation bar -->
    <nav class="navbar navbar-expand-md">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
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
                            <a id="home" class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a id="regole" class="nav-link" href="#">Regole</a>
                        </li>
                        <li class="nav-item">
                            <a id="classifica" class="nav-link" href="#">Classifica</a>
                        </li>
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) { ?>
                            <li class="nav-item">
                                <a id="partite-giocate" class="nav-link" href="history.php">Partite giocate</a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- webpage body -->
    <div id="area-gioco">
        <div id="area-laterale">
            <div id="area-navi" class="container"></div>
            <div id="area-timer">
                <i class="fa-solid fa-clock"></i>
                <div id="timer">00:00</div>
            </div>
            <div id="area-chat">
                <div id="storico-messaggi" class="d-flex flex-column-reverse"></div>
                <form id="form-chat" action="" autocomplete="off">
                    <input id="input-chat" type="text" class="form-control" placeholder="Scrivi un messaggio">
                    <button type="submit" class="btn btn-chat">
                    <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
        <div id="area-battaglia" class="row">
            <div id="area-giocatore" class="col">
                <div class="area-titolo">Campo giocatore</div>
                <div class="area-griglia">
                    <div id="griglia-giocatore" class="griglia"></div>
                </div>
                <div id="area-bottone-inizio">
                    <button id="bottone-inizio" type="button" class="btn btn-orange" draggable="false" disabled>Inizia la partita</button>
                </div>  
            </div>
            <div id="area-avversario" class="col">
                <div class="area-titolo">Campo avversario</div>
                <div class="area-griglia">
                    <div id="griglia-avversario" class="griglia"></div>
                </div>
                <div class="area-titolo">Powerup raccolti</div>
                <div id="area-powerup" class="container">
                    <div class="row"></div>
                </div> 
            </div>
        </div>
    </div>

    <!-- footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col">
                    <p>&copy; <?php echo date("Y"); ?> Battleship+ Made with ❤️ by Alessandro and Raniero</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- modal windows -->
    <div class="modal fade" id="wait-modal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="d-flex align-items-center">
                        <h5>In cerca di una partita...</h5>
                        <div class="spinner-border ms-auto"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="win-modal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="mb-3">
                        <h5>Hai vinto!</h5>
                    </div>
                    <div class="d-flex justify-content-center">
                        <a class="btn btn-green" href="index.php">Torna alla Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="lost-modal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="mb-3">
                        <h5>Hai perso!</h5>
                    </div>
                    <div class="d-flex justify-content-center">
                        <a class="btn btn-green" href="index.php">Torna alla Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="disconnect-modal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="mb-3">
                        <h5>L'avversario si è disconnesso :(</h5>
                    </div>
                    <div class="d-flex justify-content-center">
                        <a class="btn btn-green" href="index.php">Torna alla Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="ajax-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
</body>

</html>