<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "covoiturage";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

if (isset($_GET['lieux_depart']) && isset($_GET['lieux_arriver'])) {
    $depart = filter_input(INPUT_GET, 'lieux_depart', FILTER_SANITIZE_STRING);
    $destination = filter_input(INPUT_GET, 'lieux_arriver', FILTER_SANITIZE_STRING);
    $date_depart = $_GET['date_depart'] ?? '';
    $places = isset($_GET['nb_place']) && $_GET['nb_place'] == 1;
    $ecolo = isset($_GET['ecolo']) && $_GET['ecolo'] == 1;

    // Construction dynamique de la requête
    $sql = "SELECT covoiturage.*, utilisateur.nom AS conducteur_nom
            FROM covoiturage
            JOIN utilisateur ON covoiturage.utilisateur_id = utilisateur.utilisateur_id
            WHERE lieux_depart = ? AND lieux_arriver = ? AND date_depart >= CURDATE()";
    $params = [$depart, $destination];
    $types = "ss";

    if (!empty($date_depart)) {
        $sql .= " AND date_depart = ?";
        $params[] = $date_depart;
        $types .= "s";
    }
    if ($places) {
        $sql .= " AND nb_place > 0";
    }
    if ($ecolo) {
        $sql .= " AND vehicule_ecolo = 1";
    }
    $sql .= " ORDER BY date_depart ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<div class='result-card'>";
            echo "<p><strong>Départ :</strong> " . htmlspecialchars($row["lieux_depart"]) . "</p>";
            echo "<p><strong>Destination :</strong> " . htmlspecialchars($row["lieux_arriver"]) . "</p>";
            echo "<p><strong>Prix :</strong> " . htmlspecialchars($row["prix_personne"]) . " credits</p>";
            echo "<p><strong>Date :</strong> " . htmlspecialchars($row["date_depart"]) . "</p>";
            echo "<p><strong>Places disponibles :</strong> " . htmlspecialchars($row["nb_place"]) . "</p>";
            echo "<p><strong>Conducteur :</strong> " . htmlspecialchars($row["conducteur_nom"]) . "</p>";
            if (isset($row["note_conducteur"])) {
                echo "<p><strong>Note du Conducteur :</strong> " . htmlspecialchars($row["note_conducteur"]) . "/5</p>";
            }
            echo "<a href='detail.php?id=" . $row["covoiturage_id"] . "' class='details-button'>Voir Détails</a><br><br>";
            echo "</div>";
        }
    } else {
        echo "<p>Aucun trajet trouvé.</p>";
    }
}
?>