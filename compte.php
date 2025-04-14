<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
    header("Location: index.php"); // Redirige vers l'accueil si pas connecté
    exit();
}

// Tu peux récupérer toutes les infos stockées au moment du login
$email = $_SESSION['email'];
$nom = $_SESSION['nom'] ?? 'Nom inconnu';
$numero = $_SESSION['numero'] ?? 'Numéro non renseigné';
$credits = $_SESSION['credits'] ?? 0;
$photo_profil = $_SESSION['photo_profil'] ?? null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Compte - EcoRide</title>
</head>
<body>
    <h1>Bienvenue sur ton compte</h1>
    <p><strong>Email :</strong> <?= htmlspecialchars($email) ?></p>
    <p><strong>Nom :</strong> <?= htmlspecialchars($nom) ?></p>
    <p><strong>Numéro :</strong> <?= htmlspecialchars($numero) ?></p>
    <p><strong>Crédits disponibles :</strong> <?= htmlspecialchars($credits) ?> credits</p>

    <?php if ($photo_profil): ?>
        <img src="<?= htmlspecialchars($photo_profil) ?>" alt="Photo de profil" width="100">
    <?php endif; ?>

    <form action="logout.php" method="post">
        <button type="submit">Se déconnecter</button>
    </form>
</body>
</html>
