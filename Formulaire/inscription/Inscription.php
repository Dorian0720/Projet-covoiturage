<?php
$host = "localhost";
$user = "root"; // Nom d'utilisateur phpMyAdmin
$pass = ""; // Mot de passe (laisser vide par défaut)
$dbname = "covoiturage"; // Nom de la base de données

$conn = new mysqli($host, $user, $pass, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérifier si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST["nom"]);
    $email = htmlspecialchars($_POST["email"]);
    $mot_de_passe = password_hash($_POST["mot_de_passe"], PASSWORD_DEFAULT);

    // Gestion de l'upload de la photo
    $dossier_upload = "uploads/";
    if (!file_exists($dossier_upload)) {
        mkdir($dossier_upload, 0777, true); // Créer le dossier s'il n'existe pas
    }

    $photo_nom = basename($_FILES["photo"]["name"]);
    $photo_chemin = $dossier_upload . time() . "_" . $photo_nom; // Renommer pour éviter les doublons
    $extension = strtolower(pathinfo($photo_chemin, PATHINFO_EXTENSION));

    // Vérifier le type de fichier
    $extensions_autorisees = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($extension, $extensions_autorisees)) {
        die("Erreur : Seules les images JPG, JPEG, PNG et GIF sont autorisées.");
    }

    // Déplacer l'image téléchargée vers le dossier
    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $photo_chemin)) {
        // Insérer les données dans la base
        $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, photo_profil) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nom, $email, $mot_de_passe, $photo_chemin);

        if ($stmt->execute()) {
            echo "Inscription réussie ! <a href='../conexion.php'>Connectez-vous ici</a>";
        } else {
            echo "Erreur : " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Erreur lors de l'upload de l'image.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>
<body>
    <h2>Inscription</h2>
    <form method="post" enctype="multipart/form-data">
        <label>Nom :</label>
        <input type="text" name="nom" required><br>

        <label>Email :</label>
        <input type="email" name="email" required><br>

        <label>Mot de passe :</label>
        <input type="password" name="mot_de_passe" required><br>

        <label>Photo de profil :</label>
        <input type="file" name="photo" accept="image/*" required><br>

        <button type="submit">S'inscrire</button>
    </form>
    <p>Vous s'avec deja un compte ? <a href="../conexion.php">conexion</a></p>


    <style>
 /* Styles généraux */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    text-align: center;
    margin: 0;
    padding: 0;
}

/* Conteneur du formulaire */
.container {
    width: 40%;
    margin: 50px auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

/* Titre */
h2 {
    font-size: 32px;
    color: #333;
    font-weight: bold;
}

/* Champs du formulaire */
.form-group {
    margin: 15px 0;
    text-align: left;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

.form-group input {
    width: 95%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

/* Bouton */
button {
    background: #333;
    color: white;
    border: none;
    padding: 10px;
    width: 100%;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #555;
}

/* Message d'erreur/succès */
.message {
    padding: 10px;
    border-radius: 5px;
    text-align: center;
    font-weight: bold;
}

.message a {
    color: #3498db;
    text-decoration: none;
}

    </style>
</body>
</html>
