<?php session_start(); ?>

<!DOCTYPE html>
<html lang="it">

<head>
    <!-- meta info -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Battleship+</title>

    <!-- bootstrap and jquery library -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- project's resources -->
    <link rel="icon" href="assets/cruise.png">
    <link rel="stylesheet" href="style.css">

    <script>
    $(function() {
        <?php if ($_SESSION['login_err_msg'] != "") { ?>
            $("#login").click();
        <?php } ?>
    });
    </script>
    <script>
    $(function() {
        <?php if ($_SESSION['signup_err_msg'] != "") { ?>
            $("#signup").click();
        <?php } ?>
    });
    </script>
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
                            <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="rules.html">Regole</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="ranking.php">Classifica</a>
                        </li>
                    </ul>
                </div>
                <div class="d-flex justify-content-center">
                    <ul class="navbar-nav">
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {  ?>
                                <a href= "#">
                                <div class="profile-icon">
                                    <?php
                                    echo "<div class=\"profile-initial\">" . substr($_SESSION['username'], 0, 1) . "</div>"; ?>
                                </div>
                                </a>
                                <li>
                                <form name="logoutForm" action="logout.php" method="POST">
                                    <button type="submit" class="btn btn-blue">
                                        <?php echo "Logout"; ?>
                                    </button>
                                </form>
                                </li>
                            <?php } else { ?>
                                <li>
                                    <button id="signup" type="button" class="btn btn-blue" data-bs-toggle="modal" data-bs-target="#signupModal">
                                        Iscriviti
                                    </button>
                                    <button id="login" type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#loginModal">
                                        Login
                                    </button>
                                </li>
                            <?php }  ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- webpage body -->
    <h1>Battleship+</h1>
    <div class="d-flex justify-content-center gap-2">
        <a class="btn btn-green" href="#">Crea Partita</a>
        <a class="btn btn-green" href="game.php">Unisciti alla Partita</a>
    </div>

    <!-- modal windows -->
    <div class="modal fade" id="signupModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Iscriviti al gioco</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form name="signupForm" action="signup.php" method="POST">
                        <div class="mb-3">
                            <input type="text" name="inputUsername" class="form-control" placeholder="Username"
                                required>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="inputEmail" class="form-control" placeholder="Indirizzo e-mail"
                                required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="inputPassword" class="form-control" placeholder="Password"
                                required>
                        </div>
                        <button type="submit" class="btn btn-green" name="signup">Iscriviti</button>
                        <div id="error-message" class="mb-3 text-danger">
                                <?php if(isset($_SESSION['signup_err_msg']) && $_SESSION['signup_err_msg'] != ""){ 
                                    echo $_SESSION['signup_err_msg']; 
                                    $_SESSION['signup_err_msg'] = "";
                                } ?>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loginModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form name="loginForm" action="login.php" method="POST">
                            <div class="mb-3">
                            <!-- ho cambiato il tipo in text per permettere di usare il semplice username per il login
                            non so se crea problemi di sicurezza -->
                            <input type="text" name="inputEmailUsername" class="form-control" placeholder="Indirizzo e-mail o Username"
                            required>
                            </div>
                            <div class="mb-3">
                            <input type="password" name="inputPassword" class="form-control" placeholder="Password"
                            required>
                            </div>
                            <div id="error-message" class="mb-3 text-danger"></div>
                            <button type="submit" class="btn btn-green" name="login">Login</button>
                            <div id="error-message" class="mb-3 text-danger">
                                <?php if(isset($_SESSION['login_err_msg']) && $_SESSION['login_err_msg'] != ""){ 
                                    echo $_SESSION['login_err_msg']; 
                                    $_SESSION['login_err_msg'] = "";
                                } ?>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>