<?php
$host = "localhost"; // Ou ton serveur MySQL
$user = "root"; // Ton utilisateur MySQL
$password = ""; // Ton mot de passe MySQL
$dbname = "covoiturage"; // Nom de ta base de données

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}


if (isset($_GET['lieux_depart']) && isset($_GET['lieux_arriver'])) {
    $depart = filter_input(INPUT_GET, 'lieux_depart', FILTER_SANITIZE_STRING);
    $destination = filter_input(INPUT_GET, 'lieux_arriver', FILTER_SANITIZE_STRING);

    if (isset($_GET['lieux_depart']) && isset($_GET['lieux_arriver'])) {
        $depart = filter_input(INPUT_GET, 'lieux_depart', FILTER_SANITIZE_STRING);
        $destination = filter_input(INPUT_GET, 'lieux_arriver', FILTER_SANITIZE_STRING);
    }
    
    // filtre 

$places = isset($_GET['nb_place']) && $_GET['nb_place'] == 1;
$ecolo = isset($_GET['ecolo']) && $_GET['ecolo'] == 1;
// filtre date
$date_depart = $_GET['date_depart'] ?? '';
// Construis dynamiquement la requête SQL selon les filtres cochés
  $sql = "SELECT covoiturage.*, utilisateur.nom AS conducteur_nom
        FROM covoiturage
        JOIN utilisateur ON covoiturage.utilisateur_id = utilisateur.utilisateur_id
         WHERE lieux_depart = ? AND lieux_arriver = ? AND nb_place > 0"
        . " AND date_depart >= CURDATE() ORDER BY date_depart ASC";
    $params = [$depart, $destination];
    $types = "ss";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if (!empty($date_depart)) {
    $sql .= " AND date_depart >= ?";
    $params[] = $date_depart;
    $types .= "s";
    $sql .= " ORDER BY ABS(DATEDIFF(date_depart, ?)) ASC";
    $params[] = $date_depart;
    $types .= "s";
}

if ($places) {
    $sql .= " AND nb_place > 0";
}

if ($ecolo) {
    $sql .= " AND vehicule_ecolo = 1";
}
    
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<div class='result-card'>";
            echo "<p><strong>Départ :</strong> " . $row["lieux_depart"] . "</p>";
            echo "<p><strong>Destination :</strong> " . $row["lieux_arriver"] . "</p>";
            echo "<p><strong>Prix :</strong> " . $row["prix_personne"] . " credits</p>";
            echo "<p><strong>Date :</strong> " . $row["date_depart"] . "</p>";
            echo "<p><strong>Places disponibles :</strong> " . $row["nb_place"] . "</p>";
            echo "<p><strong>Conducteur :</strong> " . $row["conducteur_nom"] . "</p>";
            echo "<p><strong>Note du Conducteur :</strong> " . $row["note_conducteur"] . "/5</p>";
            echo "<a href='detail.php?id=" . $row["covoiturage_id"] . "' class='details-button'>Voir Détails</a><br><br>";
            echo "</div>";
        }
    } else {
        echo "<p>Aucun trajet trouvé.</p>";
    }
}
?>
