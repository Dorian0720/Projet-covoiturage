<?php
session_start();
$error = ''; // Pour éviter l'erreur "undefined"
$conn = new mysqli("localhost", "root", "", "covoiturage");
if ($conn->connect_error) die("Erreur connexion DB");?>

 // Bloc de vérification admin AVANT toute action (SANS base de données)
  <?php  $admin_email = "admin@admin.com";
    $admin_password = "LKPSds_ixwfK8r";

 if ($admin_email == "dorian.mutel@gmail.com" || $admin_password == "LKPSds_ixwfK8r"): ?>
    <h1>Bienvenue dans l'administration</h1>
    <p>Vous êtes connecté en tant que <?= htmlspecialchars($admin_email == "admin@admin.com" ? 'Administrateur' : 'Utilisateur') ?>.</p>
    <?php endif; // Fin de la vérification admin ?>

   
   
 <?php
if (!isset($_POST['admin_email']) || !isset($_POST['admin_password'])) {
    // Si l'utilisateur n'est pas connecté, on affiche le formulaire de connexion
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_email'], $_POST['admin_password'])) {
        $email = trim($_POST['admin_email']);
        $password = $_POST['admin_password'];
        if ($email === $admin_email && $password === $admin_password) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $email;
            header("Location: admin.php");
            exit;
        } else {
            $error = "Identifiants invalides.";
        }
    }?>


// Suppression utilisateur 
<?php
if (isset($_GET['del_user'])) {
    $id = (int)$_GET['del_user'];
    $conn->query("DELETE FROM utilisateur WHERE utilisateur_id=$id");
}
// Suppression trajet
if (isset($_GET['del_trajet'])) {
    $id = (int)$_GET['del_trajet'];
    $conn->query("DELETE FROM covoiturage WHERE covoiturage_id=$id");
}
// Suppression véhicule
if (isset($_GET['del_voiture'])) {
    $id = (int)$_GET['del_voiture'];
    $conn->query("DELETE FROM voiture WHERE voiture_id=$id");
}}
// Récupération des covoiturages par jour
$data1 = [];
$result1 = $conn->query("SELECT DATE(date_depart) AS jour, COUNT(*) AS total FROM covoiturage GROUP BY DATE(date_depart) ORDER BY DATE(date_depart);");
while ($row = $result1->fetch_assoc()) {
    $data1['labels'][] = $row['jour'];
    $data1['values'][] = $row['total'];
}
// Récupération des crédits dépensés par jour
$data2 = [];
$result2 = $conn->query("SELECT DATE(date_depart) AS jour, SUM(prix_personne) AS total FROM covoiturage GROUP BY DATE(date_depart) ORDER BY DATE(date_depart);");
while ($row = $result2->fetch_assoc()) {
    $data2['labels'][] = $row['jour'];
    $data2['values'][] = $row['total'];
}?>


<?php
$sql = "SELECT DATE(date_depart) AS jour, SUM(prix_personne) AS credits_depenses
FROM covoiturage
GROUP BY DATE(date_depart)
ORDER BY DATE(date_depart);";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestion</title>
    <link rel="stylesheet" href="css/stylecss.css">
    <style>
        table {border-collapse: collapse; width: 100%; margin-bottom: 30px;}
        th, td {border: 1px solid #ccc; padding: 8px;}
        th {background: #f0f0f0;}
        .del-btn {color: red; text-decoration: none;}
    </style>
</head>
<body>
    <h1>Administration EcoRide</h1>
    <form class="admin-login" method="post">
            <h2>Connexion Admin</h2>
            <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <input type="email" name="email" placeholder="Adresse email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
    <h2>Utilisateurs</h2>
    <table>
        <tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Rôle</th><th>Action</th></tr>
        <?php
        $res = $conn->query("SELECT * FROM utilisateur");
        while ($u = $res->fetch_assoc()) {
            echo "<tr>
                <td>{$u['utilisateur_id']}</td>
                <td>".htmlspecialchars($u['nom'])."</td>
                <td>".htmlspecialchars($u['prenom'])."</td>
                <td>".htmlspecialchars($u['email'])."</td>
                <td>{$u['role_id']}</td>
                <td><a class='del-btn' href='?del_user={$u['utilisateur_id']}' onclick='return confirm(\"Supprimer cet utilisateur ?\")'>Supprimer</a></td>
            </tr>";
        }
        ?>
    </table>

    <h2>Trajets</h2>
    <table>
        <tr><th>ID</th><th>Départ</th><th>Arrivée</th><th>Date</th><th>Places</th><th>Prix</th><th>Chauffeur</th><th>Action</th></tr>
        <?php
        $res = $conn->query("SELECT c.*, u.nom, u.prenom FROM covoiturage c LEFT JOIN utilisateur u ON c.utilisateur_id=u.utilisateur_id");
        while ($t = $res->fetch_assoc()) {
            echo "<tr>
                <td>{$t['covoiturage_id']}</td>
                <td>".htmlspecialchars($t['lieux_depart'])."</td>
                <td>".htmlspecialchars($t['lieux_arriver'])."</td>
                <td>{$t['date_depart']}</td>
                <td>{$t['nb_place']}</td>
                <td>{$t['prix_personne']}</td>
                <td>".htmlspecialchars($t['nom'].' '.$t['prenom'])."</td>
                <td><a class='del-btn' href='?del_trajet={$t['covoiturage_id']}' onclick='return confirm(\"Supprimer ce trajet ?\")'>Supprimer</a></td>
            </tr>";
        }
        ?>
    </table>

    <h2>Véhicules</h2>
    <table>
        <tr><th>ID</th><th>Modèle</th><th>Immatriculation</th><th>Énergie</th><th>Couleur</th><th>Date 1ère immat</th><th>Action</th></tr>
        <?php
        $res = $conn->query("SELECT * FROM voiture");
        while ($v = $res->fetch_assoc()) {
            echo "<tr>
                <td>{$v['voiture_id']}</td>
                <td>".htmlspecialchars($v['modele'])."</td>
                <td>".htmlspecialchars($v['immatriculation'])."</td>
                <td>".htmlspecialchars($v['energie'])."</td>
                <td>".htmlspecialchars($v['couleur'])."</td>
                <td>{$v['date_premiere_immatriculation']}</td>
                <td><a class='del-btn' href='?del_voiture={$v['voiture_id']}' onclick='return confirm(\"Supprimer ce véhicule ?\")'>Supprimer</a></td>
            </tr>";
        }
        ?>
    </table>

    <canvas id="chartCovoiturages"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('chartCovoiturages').getContext('2d');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($data1['labels']) ?>,
        datasets: [{
            label: 'Nombre de covoiturages par jour',
            data: <?= json_encode($data1['values']) ?>,
            backgroundColor: '#66bb6a'
        }]
    }
});
</script>

<canvas id="chartcredits"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('chartcredits').getContext('2d');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($data2['labels']) ?>,
        datasets: [{
            label: 'Nombre de crédits par jour',
            data: <?= json_encode($data2['values']) ?>,
            backgroundColor: '#8ea120ff'
        }]
    }
});
</script>

</div>
</body>
</html>