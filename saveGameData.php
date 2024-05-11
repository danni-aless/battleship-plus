<?php
    session_start();
    $settings = new stdClass();
    if(isset($_GET['mode']) && $_GET['mode']==='create') { 
        $settings->mode = 'create';
        $settings->width = intval($_POST['grandezzaCampo']);

        $navi = new stdClass();
        $navi->nave1 = intval($_POST['nave1']);
        $navi->nave2 = intval($_POST['nave2']);
        $navi->nave3 = intval($_POST['nave3']);
        $navi->nave4 = intval($_POST['nave4']);
        $navi->nave5 = intval($_POST['nave5']);
        $navi->nave6 = intval($_POST['nave6']);
        $settings->navi = $navi;

        $powerUps = new stdClass();
        $powerUps->powerupriga = intval($_POST['powerup-riga']);
        $powerUps->powerupcolonna = intval($_POST['powerup-colonna']);
        $powerUps->powerupbomba = intval($_POST['powerup-bomba']);
        $settings->powerUps = $powerUps;
    } elseif(isset($_GET['mode']) && $_GET['mode']==='join') { 
        $settings->mode = 'join';
        $settings->width = 10;

        $navi = new stdClass();
        $navi->nave1 = 1;
        $navi->nave2 = 1;
        $navi->nave3 = 1;
        $navi->nave4 = 1;
        $navi->nave5 = 1;
        $navi->nave6 = 0;
        $settings->navi = $navi;

        $powerUps = new stdClass();
        $powerUps->powerupriga = 1;
        $powerUps->powerupcolonna = 1;
        $powerUps->powerupbomba = 1;
        $settings->powerUps = $powerUps;

    } else { 
        header('Location: index.html');
    }
    $gameData = new stdClass();
    $gameData->username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    $gameData->settings = $settings;
    $_SESSION['gameData'] = json_encode($gameData);
    header('Location: game.php');
?>