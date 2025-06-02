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

// Récupérer les informations de l'utilisateur
$stmt = $conn->prepare("
    SELECT utilisateur.*, role.libelle
    FROM utilisateur
    JOIN role ON utilisateur.role_id = role.role_id
    WHERE utilisateur.email = ?
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $nom = $user['nom'];
    $prenom = $user['prenom'];
    $pseudo = $user['pseudo'];
    $telephone = $user['telephone'];
    $adresse = $user['adresse'];
    $photo_profil = $user['photo'];
    $role_id = $user['role_id'];
     $role = $user['libelle'];
} else {
    echo "<p style='color: red;'>Utilisateur non trouvé.</p>";
    exit();
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_infos'])) {
    $nouveau_nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $nouveau_prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
    $nouveau_pseudo = filter_input(INPUT_POST, 'pseudo', FILTER_SANITIZE_STRING);
    $nouveau_telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
    $nouveau_adresse = filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_STRING);
    $nouveau_role_id = filter_input(INPUT_POST, 'role_id', FILTER_SANITIZE_NUMBER_INT);
    // vérification du véhicule et de l'immatriculation si le rôle est conducteur
    $nouveau_vehicule = isset($_POST['vehicule']) ? trim($_POST['vehicule']) : null;
    $nouvelle_immatriculation = isset($_POST['immatriculation']) ? trim($_POST['immatriculation']) : null;

    $utilisateur_id = $user['utilisateur_id'];

// Si conducteur ou les deux, on gère la voiture
if ($nouveau_role_id == 1 || $nouveau_role_id == 3) {
    // Vérifie si une voiture existe déjà pour cet utilisateur
    $stmt_voiture = $conn->prepare("SELECT * FROM voiture WHERE voiture_id = ?");
    $stmt_voiture->bind_param("i", $utilisateur_id);
    $stmt_voiture->execute();
    $result_voiture = $stmt_voiture->get_result();

    if ($result_voiture->num_rows > 0) {
        // Mise à jour
        $stmt_update = $conn->prepare("UPDATE voiture SET modele = ?, immatriculation = ? WHERE voiture_id = ?");
        $stmt_update->bind_param("ssi", $nouveau_vehicule, $nouvelle_immatriculation, $utilisateur_id);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        // Insertion
        $stmt_insert = $conn->prepare("INSERT INTO voiture (utilisateur_id, vehicule, immatriculation) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("iss", $utilisateur_id, $nouveau_vehicule, $nouvelle_immatriculation);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
    $stmt_voiture->close();
} else {
    // Si l'utilisateur n'est plus conducteur, on peut supprimer la voiture associée (optionnel)
    $stmt_delete = $conn->prepare("DELETE FROM voiture WHERE utilisateur_id = ?");
    $stmt_delete->bind_param("i", $utilisateur_id);
    $stmt_delete->execute();
    $stmt_delete->close();
}
    // Gestion de la photo de profil
    if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === UPLOAD_ERR_OK) {
        $photo_tmp = $_FILES['photo_profil']['tmp_name'];
        $photo_name = uniqid() . "_" . basename($_FILES['photo_profil']['name']);
        $photo_path = "uploads/" . $photo_name;

        // Validation du type de fichier
        $allowed_types = ['image/jpeg', 'image/png'];
        if (!in_array($_FILES['photo_profil']['type'], $allowed_types)) {
            echo "<p style='color: red;'>Format de fichier non valide. Seuls les fichiers JPG et PNG sont acceptés.</p>";
            exit();
        }

        // Validation de la taille du fichier
        $max_size = 2 * 1024 * 1024; // 2 Mo
        if ($_FILES['photo_profil']['size'] > $max_size) {
            echo "<p style='color: red;'>Le fichier est trop volumineux. Taille maximale : 2 Mo.</p>";
            exit();
        }

        // Supprimer l'ancienne photo si elle existe
        if ($photo_profil && file_exists($photo_profil)) {
            unlink($photo_profil);
        }

        // Déplacer le fichier téléchargé
        if (move_uploaded_file($photo_tmp, $photo_path)) {
            $nouveau_photo = $photo_path;
        } else {
            echo "<p style='color: red;'>Erreur lors du téléchargement de la photo.</p>";
            $nouveau_photo = $photo_profil;
        }
    } else {
        $nouveau_photo = $photo_profil;
    }

    // Mettre à jour les informations dans la base de données
    $stmt = $conn->prepare("UPDATE utilisateur SET nom = ?, prenom = ?, telephone = ?, adresse = ?, photo = ?, role_id = ? WHERE email = ?");
if ($stmt === false) {
    die("Erreur de préparation de la requête : " . $conn->error);
}
$stmt->bind_param("sssssis", $nouveau_nom, $nouveau_prenom, $nouveau_telephone, $nouveau_adresse, $nouveau_photo, $nouveau_role_id, $email);

    if ($stmt->execute()) {
        $_SESSION['nom'] = $nouveau_nom;
        $_SESSION['prenom'] = $nouveau_prenom;
        $_SESSION['telephone'] = $nouveau_telephone;
        $_SESSION['adresse'] = $nouveau_adresse;
        $_SESSION['photo'] = $nouveau_photo;

        echo "<p style='color: green; text-align: center;'>Informations mises à jour avec succès !</p>";
    } else {
        echo "<p style='color: red; text-align: center;'>Erreur lors de la mise à jour des informations.</p>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte - EcoRide</title>
    <link rel="stylesheet" href="css/global.css" />
    <link rel="stylesheet" href="css/stylecss.css" />
    <link rel="stylesheet" href="css/index.css" />
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
        <p><strong>Numéro :</strong> <?= htmlspecialchars($telephone) ?></p>
        <p><strong>Rôle :</strong> <?= htmlspecialchars($role_id) ?></p>
        <!-- <p><strong>Crédits disponibles :</strong> <span class="credits"><?= htmlspecialchars($credits) ?> crédits</span></p> -->

        <?php if ($photo_profil): ?>
    <div class="profil-photo-header">
        <img src="<?= htmlspecialchars($photo_profil) ?>" alt="Photo de profil" />
    </div>
<?php endif; ?>

        <form action="logout.php" method="post">
            <button type="submit" class="button">Se déconnecter</button>
        </form>

        
        <div class="modifier-section">
            <h2>Modifier mes informations</h2><br>
        <form method="post" enctype="multipart/form-data">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" required>

            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($prenom) ?>" required>

            <label for="telephone">Numéro de téléphone :</label>
            <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($telephone) ?>" required>

            <label for="adresse">Adresse :</label>
            <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($adresse) ?>" required>

            <label for="photo_profil">Photo de profil :</label>
            <input type="file" id="photo_profil" name="photo_profil" accept="image/jpeg, image/png" style="padding: 5px;">
            <p style="font-size: 12px; color: #888;">(Taille maximale : 2 Mo)</p>
            <p style="font-size: 12px; color: #888;">(Formats acceptés : JPG, PNG)</p>

            <label for="role_id">Rôle :</label>
            <select id="role_id" name="role_id" required onchange="toggleConducteurForm()">
                <option value="1" <?= $role_id == 1 ? 'selected' : '' ?>>Conducteur</option>
                <option value="2" <?= $role_id == 2 ? 'selected' : '' ?>>Passager</option>
                <option value="3" <?= $role_id == 3 ? 'selected' : '' ?>>Les deux</option>
            </select>

            <div id="conducteurForm" style="display: none; margin-top: 15px;" required>
    <label for="vehicule">Véhicule :</label>
    <input type="text" id="vehicule" name="vehicule" placeholder="Modèle du véhicule" required>

    <label for="immatriculation">Immatriculation :</label>
    <input type="text" id="immatriculation" name="immatriculation" placeholder="AA-123-BB" required>
</div>
            
            <button type="submit" name="modifier_infos" class="modifier-button">Enregistrer les modifications</button>
        </form>
        </div>
    </div>
    <script>
function toggleConducteurForm() {
    var select = document.getElementById('role_id');
    var form = document.getElementById('conducteurForm');
    if (select.value == "1" || select.value == "3") {
        form.style.display = "block";
    } else {
        form.style.display = "none";
    }
}
// Pour afficher au chargement si besoin
window.onload = toggleConducteurForm;
</script>
<style>
    .container {
    position: relative;
}
    #conducteurForm label {
    display: block;
    margin-top: 8px;
    font-weight: 500;
}
#conducteurForm input {
    width: 100%;
    padding: 6px;
    margin-bottom: 8px;
    border-radius: 4px;
    border: 1px solid #bbb;
}

.profil-photo-header {
    position: absolute;
    top: 20px;
    right: 30px;
    z-index: 100;
}
.profil-photo-header img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 10%;
    border: 2px solid #28a745;
    background: #fff;
}
</style>
</body>
</html>