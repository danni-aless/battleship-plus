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

    <!-- socket.io library -->
    <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

    <!-- fontawesome kit -->
    <script src="https://kit.fontawesome.com/977cc8c5e1.js" crossorigin="anonymous" defer></script>

    <!-- project's resources -->
    <link rel="icon" href="assets/cruise.png">
    <link rel="stylesheet" href="style.css">
    <script>
        function showPass(inputId) {
            var x = document.getElementById(inputId);
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
        $(document).ready(function() {
            <?php if ((isset($_SESSION['err_image']) && $_SESSION['err_image'] != "")) { ?>
                openPopUp('popup');
                $("#editImageBtn").click();
            <?php } ?>
            <?php if (isset($_SESSION['edit_err_msg']) && $_SESSION['edit_err_msg'] === "Email Non Disponibile!") { ?>
                openPopUp('popup');
                $("#editMailBtn").click();
            <?php } ?>
            <?php if (isset($_SESSION['edit_err_msg']) && $_SESSION['edit_err_msg'] === "Username Non Disponibile!") { ?>
                openPopUp('popup');
                $("#editUsernameBtn").click();
            <?php } ?>
            <?php if (isset($_SESSION['edit_err_msg']) && $_SESSION['edit_err_msg'] === "La nuova password deve essere diversa dalla password attuale!") { ?>
                openPopUp('popup');
                $("#editPasswordBtn").click();
            <?php } ?>
            <?php if (isset($_SESSION['edit_err_msg']) && $_SESSION['edit_err_msg'] === "Password Errata!") { ?>
                openPopUp('popup');
                $("#deleteUserBtn").click();
            <?php } ?>
            <?php if (isset($_SESSION['login_err_msg']) && $_SESSION['login_err_msg'] != "") { ?>
                $("#login").click();
            <?php } ?>
            <?php if (isset($_SESSION['signup_err_msg']) && $_SESSION['signup_err_msg'] != "") { ?>
                $("#signup").click();
            <?php } ?>
            $('#regole').click(function(e) {
                e.preventDefault(); 
                $.ajax({
                    type: 'GET',
                    url: 'rules.php',
                    success: function(data) {
                        $('#webpage-body').html(data);
                    },
                });
            });
            $('#classifica').click(function(e) {
                e.preventDefault(); 
                $.ajax({
                    type: 'GET',
                    url: 'ranking.php',
                    success: function(data) {
                        $('#webpage-body').html(data);
                    }
                });
            });
            $('#partite-giocate').click(function(e) {
                e.preventDefault(); 
                $.ajax({
                    type: 'GET',
                    url: 'history.php',
                    success: function(data) {
                        $('#webpage-body').html(data);
                    }
                });
            });
            $(".nav-link").click(function() {
                $(".nav-link").removeClass("active");
                $(this).addClass("active");
            });
        });
        function openPopUp(PopUpId) {
            document.getElementById(PopUpId).style.display = 'block';
        }
        function closePopUp(PopUpId) {
            document.getElementById(PopUpId).style.display = 'none';
        }
    </script>

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
                            <a id="home" class="nav-link active" href="index.php">Home</a>
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
                <div class="d-flex flex-row justify-content-center">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) { ?>
                                <?php if(isset($_SESSION['image']) && $_SESSION['image'] != "default.jpg") { ?>
                                    <button type="button" class="btn btn-circle " onclick="openPopUp('popup')">
                                        <img class="rounded-img" src="img/<?php echo $_SESSION['image']; ?>">
                                    </button>
                                <?php } else { ?>
                                    <button type="button" class="btn btn-circle btn-green" onclick="openPopUp('popup')">
                                        <?php echo "<div class=\"profile-text\">" . substr($_SESSION['username'], 0, 1) . "</div>"; ?>
                                    </button>
                                <?php } ?>
                                <a class="btn btn-blue" href="logout.php">Logout</a>
                            <?php } else { ?>
                                <button id="signup" type="button" class="btn btn-blue" data-bs-toggle="modal" data-bs-target="#signupModal">
                                    Iscriviti
                                </button>
                                <button id="login" type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#loginModal">
                                    Login
                                </button>
                            <?php } ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- profile popup -->
    <div id="popup" class="popup">
        <div class="popup-content">
            <span class="closebtn" onclick="closePopUp('popup')">&times;</span>
            <div class="d-flex justify-content-center">
                <?php if(isset($_SESSION['image']) && $_SESSION['image'] != "default.jpg") { ?>
                    <img class="rounded-img" src="img/<?php echo $_SESSION['image']; ?>">
                <?php } else { ?>
                    <div class="profile-image">
                        <?php echo substr($_SESSION['username'], 0, 1); ?>
                    </div>
                <?php } ?>
            </div>
            <div class="text-center m-2">
                <?php
                    include "getRanking.php";
                    $pos = getRanking($_SESSION['username']);
                    echo "<span class='badge text-bg-primary'>";
                    echo $_SESSION['username'];
                    if($pos <= 3)
                        echo " <i class='fa-solid fa-trophy'></i>";
                    echo "</span>";
                    echo " <span class='badge text-bg-warning'> {$pos}° </span>";
                ?>
            </div>
            <div class="d-grid">
                <button id="editImageBtn" type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#editImage">Cambia Immagine Profilo</button>
                <button id = "editMailBtn" type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#editEmail">Cambia E-mail</button>
                <button id = "editUsernameBtn" type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#editUsername">Cambia Username</button>
                <button id = "editPasswordBtn" type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#editPassword">Cambia Password</button>
                <button id = "deleteUserBtn" type="button" class="btn btn-red" data-bs-toggle="modal" data-bs-target="#deleteUser">Elimina Profilo</button>
            </div>
        </div>
    </div>

    <!-- webpage body -->
    <div id="webpage-body">
        <h1>Battleship+</h1>
        <div class="d-flex justify-content-center gap-2">
            <button id="bottone-crea-partita" type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#createGameModal">
                Crea Partita
            </button>
            <a class="btn btn-green" href="saveGameData.php?mode=join">Unisciti alla Partita</a>
        </div>
        <div id="area-immagine" class="sx">
            <img src="assets/warship1.png" alt="warship1">
        </div>
        <div id="area-immagine" class="dx">
            <img src="assets/warship2.png" alt="warship2">
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
    <div class="modal fade" id="editImage">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambia l'immagine di profilo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-center">
                        <?php if(isset($_SESSION['image']) && $_SESSION['image'] != "default.jpg") { ?>
                            <img class="rounded-img" src="img/<?php echo $_SESSION['image']; ?>">
                        <?php } else { ?>
                            <div class="profile-image mb-2">
                                <?php echo substr($_SESSION['username'], 0, 1); ?>
                            </div>
                        <?php } ?>
                    </div>
                    <form id="formImage" action="updatePhoto.php" method="post" enctype="multipart/form-data">
                        <input type="file" name="fileToUpload" id="fileToUpload" accept=".jpg, .jpeg, .png">
                    </form>
                    <script type="text/javascript">
                        document.getElementById("fileToUpload").onchange = function() {
                            document.getElementById("formImage").submit();
                        };
                    </script>
                    <?php if(isset($_SESSION['err_image']) && $_SESSION['err_image'] != "") { ?>
                        <div class="alert alert-danger" role="alert"><?php echo $_SESSION['err_image']; ?></div>
                        <?php $_SESSION['err_image'] = ""; ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editEmail">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambia l'e-mail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form name="editForm" action="edituser.php" method="POST">
                        <div class="mb-3">
                            <input type="email" name="inputEmail" class="form-control" placeholder="Nuovo indirizzo e-mail"
                                required>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-green" name="editEmail">Aggiorna</button>
                            </div>
                            <div class="col-md-9">
                                <?php if(isset($_SESSION['edit_err_msg']) && $_SESSION['edit_err_msg'] === "Email Non Disponibile!") { ?>
                                    <div class="alert alert-danger" role="alert"><?php echo $_SESSION['edit_err_msg']; ?></div>
                                    <?php $_SESSION['edit_err_msg'] = ""; ?>
                                <?php } ?>
                            </div>
                        </div> 
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editUsername">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambia l'username</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form name="editForm" action="edituser.php" method="POST">
                        <div class="mb-3">
                            <input type="text" name="inputUsername" class="form-control" placeholder="Nuovo username"
                                required>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-green" name="editUsername">Aggiorna</button>
                            </div>
                            <div class="col-md-9">
                                <?php if(isset($_SESSION['edit_err_msg']) && $_SESSION['edit_err_msg'] === "Username Non Disponibile!") { ?>
                                    <div class="alert alert-danger" role="alert"><?php echo $_SESSION['edit_err_msg']; ?></div>
                                    <?php $_SESSION['edit_err_msg'] = ""; ?>
                                <?php } ?>
                            </div>
                        </div> 
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editPassword">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambia la password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form name="editForm" action="edituser.php" method="POST">
                        <div class="mb-3">
                            <input type="password" id="newpass" name="inputPassword" class="form-control" placeholder="Nuova password"
                                required>
                        </div>
                        <div class="mb-3">
                            <input type="checkbox" name="showPassword" onclick="showPass('newpass')"> Show Password
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-green" name="editPassword">Aggiorna</button>
                            </div>
                            <div class="col-md-9">
                                <?php if(isset($_SESSION['edit_err_msg']) && $_SESSION['edit_err_msg'] === "La nuova password deve essere diversa dalla password attuale!") { ?>
                                    <div class="alert alert-danger" role="alert"><?php echo $_SESSION['edit_err_msg']; ?></div>
                                    <?php $_SESSION['edit_err_msg'] = ""; ?>
                                <?php } ?>
                            </div>
                        </div> 
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteUser">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Elimina il profilo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form name="editForm" action="edituser.php" method="POST">
                        <div class="mb-3">
                            <input type="password" id="deletepass" name="inputPassword" class="form-control" placeholder="Conferma password"
                                required>
                        </div>
                        <div class="mb-3">
                            <input type="checkbox" name="showPassword" onclick="showPass('deletepass')"> Show Password
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-red" name="deleteUser">Elimina</button>
                            </div>
                            <div class="col-md-9">
                                <?php if(isset($_SESSION['edit_err_msg']) && $_SESSION['edit_err_msg'] === "Password Errata!") { ?>
                                    <div class="alert alert-danger" role="alert"><?php echo $_SESSION['edit_err_msg']; ?></div>
                                    <?php $_SESSION['edit_err_msg'] = ""; ?>
                                <?php } ?>
                            </div>
                        </div> 
                    </form>
                </div>
            </div>
        </div>
    </div>
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
                            <input type="password" id = "signuppass" name="inputPassword" class="form-control" placeholder="Password"
                                required>
                        </div>
                        <div class="mb-3">
                            <input type="checkbox" name="showPassword" onclick="showPass('signuppass')"> Show Password
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-green" name="signup">Iscriviti</button>
                            </div>
                            <div class="col-md-9">
                                <?php if(isset($_SESSION['signup_err_msg']) && $_SESSION['signup_err_msg'] != "") { ?>
                                    <div class="alert alert-danger" role="alert"><?php echo $_SESSION['signup_err_msg']; ?></div>
                                    <?php $_SESSION['signup_err_msg'] = ""; ?>
                                <?php } ?>
                            </div>
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
                            <input type="text" name="inputEmailUsername" class="form-control" placeholder="Username o Indirizzo e-mail"
                            required>
                        </div>
                        <div class="mb-3">
                            <input type="password" id="loginpass" name="inputPassword" class="form-control" placeholder="Password"
                            required>
                        </div>
                        <div class="mb-3">
                            <input type="checkbox" name="showPassword" onclick="showPass('loginpass')"> Show Password
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-green" name="login">Login</button>
                            </div>
                            <div class="col-md-9">
                                <?php if(isset($_SESSION['login_err_msg']) && $_SESSION['login_err_msg'] != "") { ?>
                                    <div class="alert alert-danger" role="alert"><?php echo $_SESSION['login_err_msg']; ?></div>
                                    <?php $_SESSION['login_err_msg'] = ""; ?>
                                <?php } ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createGameModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crea la partita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-crea-partita" action="saveGameData.php?mode=create" method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label for="rangeCampo" class="form-label">Grandezza campo</label>
                            <div class="d-flex gap-2">
                                <input type="range" name="grandezzaCampo" id="rangeCampo" class="form-range" min="6" max="20" value="10"
                                    oninput="this.nextElementSibling.value = this.value">
                                <output>10</output>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="rangeNave1" class="form-label">Navi da 1</label>
                                    <div class="d-flex gap-2">
                                        <input type="range" name="nave1" id="rangeNave1" class="form-range" min="0" max="4" value="1"
                                            oninput="this.nextElementSibling.value = this.value">
                                        <output>1</output>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="rangeNave2" class="form-label">Navi da 2</label>
                                    <div class="d-flex gap-2">
                                        <input type="range" name="nave2" id="rangeNave2" class="form-range" min="0" max="4" value="1"
                                            oninput="this.nextElementSibling.value = this.value">
                                        <output>1</output>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="rangeNave3" class="form-label">Navi da 3</label>
                                    <div class="d-flex gap-2">
                                        <input type="range" name="nave3" id="rangeNave3" class="form-range" min="0" max="4" value="1"
                                            oninput="this.nextElementSibling.value = this.value">
                                        <output>1</output>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="rangeNave4" class="form-label">Navi da 4</label>
                                    <div class="d-flex gap-2">
                                        <input type="range" name="nave4" id="rangeNave4" class="form-range" min="0" max="4" value="1"
                                            oninput="this.nextElementSibling.value = this.value">
                                        <output>1</output>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="rangeNave5" class="form-label">Navi da 5</label>
                                    <div class="d-flex gap-2">
                                        <input type="range" name="nave5" id="rangeNave5" class="form-range" min="0" max="4" value="1"
                                            oninput="this.nextElementSibling.value = this.value">
                                        <output>1</output>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="rangePowerUpRiga" class="form-label">Powerup Riga</label>
                                    <div class="d-flex gap-2">
                                        <input type="range" name="powerup-riga" id="rangePowerUpRiga" class="form-range" min="0" max="4" value="1"
                                            oninput="this.nextElementSibling.value = this.value">
                                        <output>1</output>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="rangePowerUpColonna" class="form-label">Powerup Colonna</label>
                                    <div class="d-flex gap-2">
                                        <input type="range" name="powerup-colonna" id="rangePowerUpColonna" class="form-range" min="0" max="4" value="1"
                                            oninput="this.nextElementSibling.value = this.value">
                                        <output>1</output>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="rangePowerUpBomba" class="form-label">Powerup Bomba</label>
                                    <div class="d-flex gap-2">
                                        <input type="range" name="powerup-bomba" id="rangePowerUpBomba" class="form-range" min="0" max="4" value="1"
                                            oninput="this.nextElementSibling.value = this.value">
                                        <output>1</output>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-green" name="crea">Crea Partita</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>