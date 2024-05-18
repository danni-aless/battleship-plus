<?php
session_start();
include "connection.php";
if(isset($_FILES['fileToUpload'])){
    $target_dir = "img/"; 

    $_SESSION['err_image'] = "";
    $imageFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));

    $new_image_name = uniqid() . '.' . $imageFileType; 
    $target_file = $target_dir . $new_image_name;

    if ($_FILES["fileToUpload"]["size"] > 500000) {
        $_SESSION['err_image'] = "Immagine troppo grande.";
    }

    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $_SESSION['err_image'] = "Formato Immagine non valido. Permette solo file JPG, JPEG e PNG.";
    }

    if ($_SESSION['err_image'] == "") {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            
            $username = $_SESSION['username'];
            $new_image_name = mysqli_real_escape_string($db_conn, $new_image_name);

            $query = "select * from login where username = '$username'";
            $result = mysqli_query($db_conn, $query);
            $row = mysqli_fetch_assoc($result);
            $imageToDelete = $row['image'];
            if($imageToDelete != "default.jpg") {
                $imagePath = "img/" . $imageToDelete;
                unlink($imagePath);
            }

            $update_query = "update login set image = '$new_image_name' where username = '$username'";
            $result = mysqli_query($db_conn, $update_query);
            
            if ($result==false) {
                $_SESSION['err_image'] = "Database non aggiornato";
            } 

            $_SESSION['image'] = $new_image_name;
        } else {
            $_SESSION['err_image'] = "Errore, l'immagine non è stata caricata.";
        }
    }
    header("location:index.php");
}
?>
