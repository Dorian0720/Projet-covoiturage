<?php
session_start();
$isLoggedIn = isset($_SESSION['email']);
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "covoiturage");

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Récupération des infos utilisateur (exemple)
$stmt = $conn->prepare("
SELECT * FROM utilisateur WHERE email = ?");

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$nom = $user['nom'];
$prenom = $user['prenom'];
$email = $user['email'];
$telephone = $user['telephone'];
$role_id = $user['role_id'];
$photo_profil = $user['photo_profil'] ?? null;

// Gestion des crédits via cookie
if (!isset($_COOKIE['credits'])) {
    setcookie('credits', 100, time() + 3600*24*365, "/");
    $credits = 100;
} else {
    $credits = (int)$_COOKIE['credits'];
}

// Traitement modification infos utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_infos'])) {
    // ...traitement de la modification...
    $nom = ($_POST['nom']);
    $prenom = ($_POST['prenom']);
    $telephone = ($_POST['telephone']);
    $role_id = ($_POST['role_id']);

    
}

// Traitement proposition de trajet (pour chauffeur)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proposer_trajet'])) {
    $lieux_depart = trim($_POST['lieux_depart']);
    $lieux_arriver = trim($_POST['lieux_arriver']);
    $prix_personne = (int)$_POST['prix_personne'];
    $date_depart = $_POST['date_depart'];
    $nb_place = (int)$_POST['nb_place'];

    // Gestion du véhicule
    if ($_POST['vehicule'] === 'nouveau') {
        $nouveau_modele = trim($_POST['nouveau_modele']);
        $nouvelle_immatriculation = trim($_POST['nouvelle_immatriculation']);
        $stmt_nv = $conn->prepare("INSERT INTO voiture (utilisateur_id, modele, immatriculation) VALUES (?, ?, ?)");
        $stmt_nv->bind_param("iss", $user['utilisateur_id'], $nouveau_modele, $nouvelle_immatriculation);
        $stmt_nv->execute();
        $vehicule_id = $conn->insert_id;
        $stmt_nv->close();
    } else {
        $vehicule_id = (int)$_POST['vehicule'];
    }

    $stmt_trajet = $conn->prepare("INSERT INTO covoiturage (utilisateur_id, lieux_depart, lieux_arriver, date_depart, nb_place, prix_personne, vehicule) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_trajet->bind_param("isssiii", $user['utilisateur_id'], $lieux_depart, $lieux_arriver, $date_depart, $nb_place, $prix_personne, $vehicule_id);
    if ($stmt_trajet->execute()) {
        echo "<p style='color:green;'>Trajet publié avec succès !</p>";
    } else {
        echo "<p style='color:red;'>Erreur lors de la publication du trajet.</p>";
    }
    $stmt_trajet->close();
}

// Récupération de l'historique des participations depuis le cookie
$historique = [];
if (isset($_COOKIE['participations'])) {
    $historique = json_decode($_COOKIE['participations'], true);
    if (!is_array($historique)) $historique = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon compte</title>
    <link rel="stylesheet" href="css/stylecss.css">
<link rel="stylesheet" href="css/global.css">
<link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="examples-home-page">
        <header class="header">
            <div class="navigation-pill-list">
                <nav class="navigation">
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
    <h1>Bienvenue sur ton compte</h1>
    <p><strong>Email :</strong> <?= htmlspecialchars($email) ?></p>
    <p><strong>Nom :</strong> <?= htmlspecialchars($nom) ?></p>
    <p><strong>Prénom :</strong> <?= htmlspecialchars($prenom) ?></p>
    <p><strong>Crédits :</strong> <span class="credits"><?= htmlspecialchars($credits) ?></span></p>
    <p><strong>Numéro :</strong> <?= htmlspecialchars($telephone) ?></p>
    <p><strong>Rôle :</strong> <?= htmlspecialchars($role_id) ?></p>

    <?php if ($photo_profil): ?>
        <div class="profil-photo-header">
            <img src="/projeteval/upload/<?= htmlspecialchars($photo_profil) ?>" alt="Photo de profil" />
        </div>
    <?php endif; ?>

    <form action="logout.php" method="post">
        <button type="submit" class="button">Se déconnecter</button>
    </form>

    <div class="modifier-section">
        <h2>Modifier mes informations</h2>
        <form method="post" enctype="multipart/form-data">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" required>
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($prenom) ?>" required>
            <label for="telephone">Numéro de téléphone :</label>
            <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($telephone) ?>" required>
            <label for="adresse">Adresse :</label>
            <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($user['adresse'] ?? '') ?>">
            <label for="role_id">Rôle :</label>
            <select id="role_id" name="role_id">
                <option value="1" <?= $role_id == 1 ? 'selected' : '' ?>>Conducteur</option>
                <option value="2" <?= $role_id == 2 ? 'selected' : '' ?>>Passager</option>
                <option value="3" <?= $role_id == 3 ? 'selected' : '' ?>>Les deux</option>
            </select>
            <label for="photo_profil">Photo de profil :</label>
            <input type="file" id="photo_profil" name="photo_profil"accept="image/jpeg, image/png">
            <p style="font-size: 12px; color: #888;">(Taille maximale : 2 Mo)</p>
            <p style="font-size: 12px; color: #888;">(Formats acceptés : JPG, PNG)</p>
            <button type="submit" name="modifier_infos" class="modifier-button">Enregistrer</button>
        </form>
    </div>
    
    <?php if ($role_id == 1 || $role_id == 3): ?>
    <div class="modifier-section">
        <h2>Proposer un nouveau voyage</h2>
        <form method="post">
            <label for="lieux_depart">Adresse de départ :</label>
            <input type="text" id="lieux_depart" name="lieux_depart" required>
            <label for="lieux_arriver">Adresse d’arrivée :</label>
            <input type="text" id="lieux_arriver" name="lieux_arriver" required>
            <label for="vehicule">Véhicule :</label>
            <select id="vehicule" name="vehicule" required>
                <?php
                $stmt_veh = $conn->prepare("SELECT voiture_id, modele FROM voiture WHERE voiture_id = ?");
                $stmt_veh->bind_param("i", $user['utilisateur_id']);
                $stmt_veh->execute();
                $res_veh = $stmt_veh->get_result();
                while ($v = $res_veh->fetch_assoc()) {
                    echo '<option value="' . $v['voiture_id'] . '">' . htmlspecialchars($v['modele']) . '</option>';
                }
                ?>
                <option value="nouveau">Ajouter un nouveau véhicule</option>
            </select>
            <div id="nouveauVehicule" style="display:none;">
                <label for="nouveau_modele">Nouveau modèle :</label>
                <input type="text" id="nouveau_modele" name="nouveau_modele">
                <label for="nouvelle_immatriculation">Nouvelle immatriculation :</label>
                <input type="text" id="nouvelle_immatriculation" name="nouvelle_immatriculation">
            </div>
            <label for="prix_personne">Prix par personne (crédits) :</label>
            <input type="number" id="prix_personne" name="prix_personne" min="3" required>
            <small>2 crédits seront prélevés par la plateforme sur chaque réservation.</small><br><br>
            <label for="date_depart">Date de départ :</label>
            <input type="date" id="date_depart" name="date_depart" required>
            <label for="nb_place">Nombre de places :</label>
            <input type="number" id="nb_place" name="nb_place" min="1" required>
            <button type="submit" name="proposer_trajet" class="modifier-button">Publier le trajet</button>
        </form>
    </div>
    <script>
    document.getElementById('vehicule').addEventListener('change', function() {
        document.getElementById('nouveauVehicule').style.display = (this.value === 'nouveau') ? 'block' : 'none';
    });
    </script>
    <?php endif; ?>

    <!-- Historique des participations en fin de page -->
    <div class="modifier-section">
        <h2>Historique de mes participations</h2>
        <?php if (count($historique) > 0): ?>
            <ul>
            <?php foreach ($historique as $id): ?>
                <?php
            $stmt = $conn->prepare("SELECT lieux_depart, lieux_arriver, date_depart FROM covoiturage WHERE covoiturage_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()):
            ?>
            
                <li>
                    Départ : <?= htmlspecialchars($row['lieux_depart']) ?> |
                    Arrivée : <?= htmlspecialchars($row['lieux_arriver']) ?> |
                    Date : <?= htmlspecialchars($row['date_depart']) ?>
                </li>
            <?php
            endif;
            $stmt->close();
            ?>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucune participation enregistrée.</p>
    <?php endif; ?>
    </div>
</div>
</body>
</html>