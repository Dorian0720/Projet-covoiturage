<?php
session_start();
// Vérifier si l'utilisateur est connecté header
$isLoggedIn = isset($_SESSION['email']); // Supposons que $_SESSION['user'] contient les infos du user
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Trajet</title> 
    <link rel="stylesheet" href="global.css" />
    <link rel="stylesheet" href="stylecss.css" />
    <link rel="stylesheet" href="index.css" />
    <style>    
        .container {
            width: 80%;
            height: 100%;
            flex-direction: column;
            justify-content: center;
            margin: auto;
            padding: 20px;
            text-align: center;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .info {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .back-btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
<div class="examples-home-page">
        <header class="header">
            <div class="navigation-pill-list">
                <div class="navigation-pill">
                <div class="title"><a href="index.php">Retour vers la page d’accueil</a></div>
            </div>
            <div class="title-wrapper"><div class="text-wrapper"><a href="#">Accès aux covoiturages</a></div></div>
                
            <div class="title-wrapper"><div class="text-wrapper"><a href="index.html">Contact</a></div></div>
                
            <div class="title-wrapper"><div class="text-wrapper">
                    <?php if ($isLoggedIn): ?>
                        <a href="compte.php">Compte</a>
                    <?php else: ?>
                        <a href="./Formulaire/inscription/Inscription.php">Connexion</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </div>
    </div>
</header>
    <div class="container">
        <?php
        // Connexion à la base de données
        $host = "localhost"; // Ou ton serveur MySQL
        $user = "root"; // Ton utilisateur MySQL
        $password = ""; // Ton mot de passe MySQL
        $dbname = "covoiturage"; // Nom de ta base de données

        $conn = new mysqli($host, $user, $password, $dbname);
        if ($conn->connect_error) {
            die("Connexion échouée : " . $conn->connect_error);
        }
        
        // Vérification de l'ID du trajet passé en paramètre
        if(isset($_GET['id'])) {
            $trajet_id = $_GET['id'];
        
            // Requête pour récupérer tous les détails du trajet
            
            $sql = "SELECT c.*, 
                    FROM covoiturage
                    JOIN vehicules v ON 
                    JOIN utilisateur u ON 
                    WHERE u.id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $trajet_id);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                
                echo "<h1>Détails du trajet</h1>";
                echo "<p><strong>Départ :</strong> " . $row["depart"] . "</p>";
                echo "<p><strong>Destination :</strong> " . $row["destination"] . "</p>";
                echo "<p><strong>Prix :</strong> " . $row["prix"] . " credits</p>";
                echo "<p><strong>Date :</strong> " . $row["date_depart"] . "</p>";
                echo "<p><strong>Conducteur :</strong> " . $row["nom"] . "</p>";
                echo "<p><strong>Note du Conducteur :</strong> " . $row["note_conducteur"] . "/5</p>";
                echo "<p><strong>Places disponibles :</strong> " . $row["Places"] . "</p>";
                
                // Informations supplémentaires
                echo "<h2>Informations supplémentaires</h2>";
                echo "<p><strong>Avis du Conducteur :</strong> " . $row["avis"] . "</p>";
                echo "<p><strong>Véhicule :</strong> " . $row["marque"] . " " . $row["modele"] . " (" . $row["energie"] . ")</p>";
                echo "<p><strong>Préférences du Conducteur :</strong> " . $row["preferences"] . "</p>";
        
            } else {
                echo "<p>Trajet non trouvé.</p>";
            }
        } else {
            echo "<p>Paramètre ID manquant.</p>";
        }
        ?>

        <!-- Boutons -->
        <a href="index.php" class="back-btn">Retour</a>
        <form method="post" style="display: inline;">
            <button type="submit" name="participer" class="back-btn">Participer</button>
        </form>

        <?php
        // Gestion de la participation
        if (isset($_POST['participer'])) {
            if (isset($_SESSION['email'])) {
                // L'utilisateur est connecté, enregistrer la participation
                $email = $_SESSION['email'];
$stmt_user = $conn->prepare("SELECT utilisateur_id FROM utilisateurs WHERE email = ?");
$stmt_user->bind_param("s", $email);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $utilisateur_id = $user['utilisateur_id'];
} else {
    echo "<p style='color: red;'>Utilisateur non trouvé.</p>";
    exit();
}

                // Exemple d'enregistrement dans une table "participations"
                $stmt = $conn->prepare("INSERT INTO reservations (trajet_id, utilisateur_id) VALUES (?, ?)");
                $stmt->bind_param("is", $trajet_id, $utilisateur_id);

                if ($stmt->execute()) {
                    echo "<p style='color: green;'>Participation confirmée, rendez vous dans la section covoiturage !</p>";
                } else {
                    echo "<p style='color: red;'>Erreur lors de l'enregistrement de la participation.</p>";
                }
            } else {
                // L'utilisateur n'est pas connecté, rediriger vers la page de connexion
                header("Location: Formulaire/conexion.php");
                exit();
            }
        }
        ?>
    </div>
</body>
</html>