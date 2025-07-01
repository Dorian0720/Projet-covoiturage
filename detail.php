<?php
session_start();
// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['email']); // Supposons que $_SESSION['email'] contient l'email de l'utilisateur connecté
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Trajet</title> 
    <link rel="stylesheet" href="css/global.css" />
    <link rel="stylesheet" href="css/stylecss.css" />
    <link rel="stylesheet" href="css/index.css" />
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
    </header>
</div>

<div class="container">
    <?php
    // Connexion à la base de données
    $host = "localhost"; // Serveur MySQL
    $user = "root"; // Utilisateur MySQL
    $password = ""; // Mot de passe MySQL
    $dbname = "covoiturage"; // Nom de la base de données

    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) {
        die("Connexion échouée : " . $conn->connect_error);
    }

    // Vérification de l'ID du trajet passé en paramètre
    if (isset($_GET['id'])) {
        $covoiturage_id = intval($_GET['id']); // Sécurisation de l'ID

        // Requête pour récupérer tous les détails du trajet
        $sql = "SELECT covoiturage.*, utilisateur.nom AS conducteur_nom, avis.note AS note_conducteur,
               voiture.modele, voiture.energie, marque.libelle AS marque
                FROM covoiturage
                JOIN utilisateur ON covoiturage.utilisateur_id = utilisateur.utilisateur_id
                LEFT JOIN avis ON avis.avis_id = covoiturage.covoiturage_id
                LEFT JOIN voiture ON voiture.voiture_id = covoiturage.covoiturage_id
                LEFT JOIN marque ON marque.marque_id = voiture.voiture_id
                WHERE covoiturage.covoiturage_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $covoiturage_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            echo "<h1>Détails du trajet</h1>";
            echo "<p><strong>Départ :</strong> " . htmlspecialchars($row["lieux_depart"]) . "</p>";
            echo "<p><strong>Destination :</strong> " . htmlspecialchars($row["lieux_arriver"]) . "</p>";
            echo "<p><strong>Prix :</strong> " . htmlspecialchars($row["prix_personne"]) . " crédits</p>";
            echo "<p><strong>Date :</strong> " . htmlspecialchars($row["date_depart"]) . "</p>";
            echo "<p><strong>Places disponibles :</strong> " . htmlspecialchars($row["nb_place"]) . "</p>";
            echo "<p><strong>Conducteur :</strong> " . htmlspecialchars($row["conducteur_nom"]) . "</p>";
            echo "<p><strong>Note du Conducteur :</strong> " . htmlspecialchars($row["note_conducteur"]) . "/5</p>";

            // Informations supplémentaires
            echo "<h2><br>Informations supplémentaires :</h2>";
            echo "<p><strong>Véhicule :</strong> " . htmlspecialchars($row["marque"]) . " " . htmlspecialchars($row["modele"]) . " (" . htmlspecialchars($row["energie"]) . ")</p>";
            echo "<p><strong>Préférences du Conducteur :</strong> " . htmlspecialchars($row["statut"]) . "</p>";
        } else {
            echo "<p>Trajet non trouvé.</p>";
        }
    } else {
        echo "<p>Paramètre ID manquant.</p>";
    }
    ?>

    <!-- Boutons -->
    <a href="index.php" class="back-btn">Retour</a>
    <?php if ($isLoggedIn): ?>
        <form method="post" style="display: inline;">
         <button type="submit" name="participer" class="back-btn" onclick="return confirm('Êtes-vous sûr de vouloir participer à ce trajet ?');">Participer</button>
            <input type="hidden" name="covoiturage_id" value="<?php echo $covoiturage_id; ?>">
        </form>
        
       <?php else: ?>
    <p style="color: red; margin-top: 20px;">Connectez-vous pour participer à ce trajet.</p>
<?php endif; ?>
        
    <?php
    // Gestion de la participation
    if (isset($_POST['participer'])) {
        if ($isLoggedIn) {
            // Vérifier si l'utilisateur a déjà participé à ce trajet
            if (in_array($covoiturage_id, $_SESSION['participations'] ?? [])) {
                echo "<p style='color: orange;'>Vous avez déjà réservé ce trajet.</p>";
            } else {
                // Ajouter le trajet à la liste des participations
               // Ajoute le covoiturage à la liste des participations dans la session
if (!isset($_SESSION['participations'])) {
    $_SESSION['participations'] = [];
}}
if (in_array($covoiturage_id, $_SESSION['participations'])) {
    echo "<p style='color: orange;'>Vous avez déjà réservé ce trajet.</p>";
} else {
    // Ajouter le covoiturage à la liste des participations dans la session
    $_SESSION['participations'][] = $covoiturage_id;
}
    // Récupérer les crédits depuis le cookie
$credits = isset($_COOKIE['credits']) ? (int)$_COOKIE['credits'] : 20; // Valeur par défaut si le cookie n'existe pas
$prix = (int)$row['prix_personne']; // prix du trajet

if ($credits >= $prix) {
    $credits -= $prix;
    setcookie('credits', $credits, time() + 3600*24*365, "/");
    // ...valider la participation...
    echo "<p style='color: green;'>Participation confirmée, il vous reste $credits crédits.</p>";
} else {
    echo "<p style='color: red;'>Crédits insuffisants pour participer à ce trajet.</p>";
}

                // Enregistrer l'historique des participations dans un cookie   

// Récupérer l'historique depuis le cookie (ou tableau vide)
$historique = [];
if (isset($_COOKIE['participations'])) {
    $historique = json_decode($_COOKIE['participations'], true);
    if (!is_array($historique)) $historique = [];
}

// Ajouter la nouvelle participation si elle n'existe pas déjà
if (!in_array($covoiturage_id, $historique)) {
    $historique[] = $covoiturage_id;
    setcookie('participations', json_encode($historique), time() + 3600*24*365, "/");
}

                // Rediriger vers la page de compte ou afficher un message de succès
                // header("Location: compte.php");
                // exit();
            }
        } 
    ?>
</div>
</body>
</html>