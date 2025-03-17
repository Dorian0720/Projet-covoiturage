<?php
//demarage de session 
session_start() ;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php 
    //ensuite on affiche le contenue de la session
    echo " Bonjour " . $_SESSION['email'];
    ?>
</body>
</html>