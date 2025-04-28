
<?php
session_start();

// Vérifier si l'utilisateur est connecté header
$isLoggedIn = isset($_SESSION['email']); // Supposons que $_SESSION['user'] contient les infos du user
// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
    header("Location: index.php"); // Redirige vers l'accueil si pas connecté
    exit();
}

$email = $_SESSION['email'];

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "covoiturage");
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Récupérer les informations de l'utilisateur et son rôle
$stmt = $conn->prepare("
    SELECT utilisateurs.nom, utilisateurs.prenom, utilisateurs.telephone, utilisateurs.adresse, utilisateurs.email, utilisateurs.photo_profil, utilisateurs.credits, utilisateurs.role_id, roles.libelle 
FROM utilisateurs 
JOIN roles ON utilisateurs.role_id = roles.id 
WHERE utilisateurs.email = ?
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $nom = $user['nom'];
    $prenom = $user['prenom'];
    $telephone = $user['telephone'];
    $adresse = $user['adresse'];
    $photo_profil = $user['photo_profil'];
    $credits = $user['credits'];
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
$nouveau_telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
$nouveau_adresse = filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_STRING);
$nouveau_role = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);

   // Validation du type de fichier
$allowed_types = ['image/jpeg', 'image/png'];
if (!in_array($_FILES['photo_profil']['type'], $allowed_types)) {
    echo "<p style='color: red;'>Format de fichier non valide. Seuls les fichiers JPG et PNG sont acceptés.</p>";
    exit();
}

// Validation de la taille du fichier
$max_size = 2 * 1024 * 1024; // 2 Mo
if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === UPLOAD_ERR_OK) {
    // Vérification du type de fichier
    $allowed_types = ['image/jpeg', 'image/png'];
    if (!in_array($_FILES['photo_profil']['type'], $allowed_types)) {
        echo "<p style='color: red;'>Format de fichier non valide. Seuls les fichiers JPG et PNG sont acceptés.</p>";
        exit();
    }

    // Vérification de la taille du fichier
    $max_size = 2 * 1024 * 1024; // 2 Mo
    if ($_FILES['photo_profil']['size'] > $max_size) {
        echo "<p style='color: red;'>Le fichier est trop volumineux. Taille maximale : 2 Mo.</p>";
        exit();
    }

    // Vérification des erreurs de téléchargement
    if ($_FILES['photo_profil']['error'] !== UPLOAD_ERR_OK) {
        echo "<p style='color: red;'>Erreur lors du téléchargement du fichier. Code d'erreur : " . $_FILES['photo_profil']['error'] . "</p>";
        exit();
    }

    // Générer un nom de fichier unique
    $photo_name = uniqid() . "_" . basename($_FILES['photo_profil']['name']);
    $photo_path = __DIR__ . "/uploads/" . $photo_name; // Utiliser un chemin absolu

    // Déplacer le fichier téléchargé
    if (move_uploaded_file($_FILES['photo_profil']['tmp_name'], $photo_path)) {
        $nouveau_photo = $photo_path;
    } else {
        echo "<p style='color: red;'>Erreur lors du déplacement du fichier. Vérifiez les permissions du répertoire et le chemin du fichier.</p>";
        $nouveau_photo = $photo_profil; // Conserver l'ancienne photo si le téléchargement échoue
    }
} else {
    $nouveau_photo = $photo_profil; // Conserver l'ancienne photo si aucune nouvelle n'est téléchargée
}

}


    // Mettre à jour les informations dans la base de données
    $stmt = $conn->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, telephone = ?, adresse = ?, photo_profil = ?, role_id = ? WHERE email = ?");
    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }

    $stmt->bind_param("sssssis", $nouveau_nom, $nouveau_prenom, $nouveau_telephone, $nouveau_adresse, $nouveau_photo, $nouveau_role, $email);

    if ($stmt->execute()) {
        // Mettre à jour les informations dans la session
        $_SESSION['nom'] = $nouveau_nom;
        $_SESSION['prenom'] = $nouveau_prenom;
        $_SESSION['telephone'] = $nouveau_telephone;
        $_SESSION['adresse'] = $nouveau_adresse;
        $_SESSION['photo_profil'] = $nouveau_photo;
        $_SESSION['role_id'] = $nouveau_role;

        echo "<p style='color: green; text-align: center;'>Informations mises à jour avec succès !</p>";
    } else {
        echo "<p style='color: red; text-align: center;'>Erreur lors de la mise à jour des informations.</p>";
    }

    $stmt->close();
?>
<?php

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte - EcoRide</title>
    <link rel="stylesheet" href="global.css" />
    <link rel="stylesheet" href="stylecss.css" />
    <link rel="stylesheet" href="index.css" />
</head>
    <style>

        /* CSS pour le style de la page compte.php */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            font_color: #333;
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            
        }

        h1 {
            text-align: center;
            color: #333;
        }

        p {
            font-size: 18px;
            color: #555;
            margin: 10px 0;
        }

        img {
            display: block;
            margin: 20px auto;
            border-radius: 50%;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .logout-button, .modifier-button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            margin-top: 20px;
        }

        .logout-button:hover, .modifier-button:hover {
            background-color: #0056b3;
        }

        .credits {
            font-weight: bold;
            color: #28a745;
        }

        form {
            margin-top: 20px;
            font_color: #333;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            cursor: pointer;
        }
        .modifier-section {
        color: #555; /* Changez cette couleur selon vos besoins */
        text-align: center;
    }
    
    </style>
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
        <p><strong>Rôle :</strong> <?= htmlspecialchars($role) ?></p>
        <p><strong>Crédits disponibles :</strong> <span class="credits"><?= htmlspecialchars($credits) ?> crédits</span></p>

        <?php if ($photo_profil): ?>
            <img src="<?= htmlspecialchars($photo_profil) ?>" alt="Photo_de_profil" width="16">
        <?php endif; ?>

        <form action="logout.php" method="post">
            <button type="submit" class="logout-button">Se déconnecter</button>
        </form>

        <h2>Modifier mes informations</h2>
        <?php
if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === UPLOAD_ERR_OK) {
    $photo_tmp = $_FILES['photo_profil']['tmp_name'];
    $photo_name = uniqid() . "_" . basename($_FILES['photo_profil']['name']);
    $photo_path = "uploads/" . $photo_name;

    // Validation du type de fichier
    $allowed_types = ['image/jpg', 'image/png'];
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
        $nouveau_photo = $photo_profil; // Conserver l'ancienne photo si le téléchargement échoue
    }
} else {
    $nouveau_photo = $photo_profil; // Conserver l'ancienne photo si aucune nouvelle n'est téléchargée
}?>
        <div class="modifier-section">
        <form method="post" enctype="multipart/form-data">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" required>

            <label id="" for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($prenom) ?>" required>

            <label for="telephone">Numéro de téléphone :</label>
            <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($telephone) ?>" required>

            <label for="adresse">Adresse :</label>
            <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($adresse) ?>" required>

            <label for="photo_profil">Photo de profil :</label>
            <input type="file" id="photo_profil" name="photo_profil" accept="image/JPG" style="padding: 5px;" required>
            <p style="font-size: 12px; color: #888;">(Taille maximale : 2 Mo)</p>
            <p style="font-size: 12px; color: #888;">(Formats acceptés : JPG)</p>

            <label for="role_id">Rôle :</label>
            <select id="role_id" name="role_id" required>
                <option value="1" <?= $role_id == 1 ? 'selected' : '' ?>>Conducteur</option>
                <option value="2" <?= $role_id == 2 ? 'selected' : '' ?>>Passager</option>
                <option value="3" <?= $role_id == 3 ? 'selected' : '' ?>>Les deux</option>
            </select>

            <button type="submit" name="modifier_infos" class="modifier-button" style="background-color: #28a745;">Enregistrer les modifications</button></form>
        </form>
        </div>
    </div>
</body>
</html>