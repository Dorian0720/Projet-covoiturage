<?php
session_start();

$host = "localhost"; // Ou ton serveur MySQL
$user = "root"; // Ton utilisateur MySQL
$password = ""; // Ton mot de passe MySQL
$dbname = "covoiturage"; // Nom de ta base de données

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

$erreur = "";

if (isset($_POST['button-valider'])) {
    if (isset($_POST['email']) && isset($_POST['mot_de_passe'])) {
        $email = $_POST['email'];
        $mot_de_passe = $_POST['mot_de_passe'];

        // Requête sécurisée avec requêtes préparées
        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Vérification du mot de passe haché
            if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
                $_SESSION['email'] = $email;
                header("location: ../index.php");
                exit();
            } else {
                $erreur = "Mot de passe incorrect !";
            }
        } else {
            $erreur = "Adresse email introuvable !";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de connexion</title>
</head>
<body>
    <section>
        <?php 
        if (!empty($erreur)) {
            echo "<p style='color: red;'>$erreur</p>";
        }
        ?>
        <div class="container">
            <div class="heading">Sign In</div>
            <form action="" method="POST">
                <input required class="input" type="email" name="email" placeholder="Email">
                <input required class="input" type="password" name="mot_de_passe" placeholder="Mot de passe">
                <input class="login-button" type="submit" value="Se connecter" name="button-valider">
                <div class="footer">Don't have an account? <a href="inscription/Inscription.php">Sign Up</a></div>
            </form>
        </div>
    </section>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 40%;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .heading {
            font-size: 32px;
            color: #333;
            font-weight: bold;
        }

        .input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-button {
            background: #333;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-button:hover {
            background: #555;
        }

        .footer {
            margin-top: 10px;
        }

        .footer a {
            color: #3498db;
            text-decoration: none;
        }
    </style>
</body>
</html>