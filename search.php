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
    

    $sql = "SELECT covoiturage.*
        FROM covoiturage WHERE lieux_depart = ? AND lieux_arriver = ? AND nb_place > 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $depart, $destination);
    
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<div class='result-card'>";
            echo "<p><strong>Départ :</strong> " . $row["lieux_depart"] . "</p>";
            echo "<p><strong>Destination :</strong> " . $row["lieux_arriver"] . "</p>";
            echo "<p><strong>Prix :</strong> " . $row["prix_personne"] . " €</p>";
            echo "<p><strong>Date :</strong> " . $row["date_depart"] . "</p>";
            echo "<p><strong>Places disponibles :</strong> " . $row["nb_place"] . "</p>";
            echo "<p><strong>Conducteur :</strong> " . $row["id"] . "</p>";
            echo "<p><strong>Note du Conducteur :</strong> " . $row["statut"] . "/5</p>";
            echo "<a href='detail.php?id=" . $row["id"] . "' class='details-button'>Voir Détails</a><br><br>";
            echo "</div>";
        }
    } else {
        echo "<p>Aucun trajet trouvé.</p>";
    }
}
?>

<script>
function showDetails(trajet) {
    const modalContent = `
        <strong>Point de rendez-vous :</strong> ${covoiturage.lieux_depart}<br>
        <strong>Temps de trajet :</strong> ${covoiturage.date_arriver} minutes<br>
        <strong>Nombre de places disponibles :</strong> ${covoiturage.nb_place}<br>
        <strong>Avis du Conducteur :</strong> ${avis.commentaire}<br>
        <strong>Modèle du Véhicule :</strong> ${voiture.modele}<br>
        <strong>Couleur du Véhicule :</strong> ${voiture.couleur}<br>
        <strong>Énergie utilisée :</strong> ${voiture.energie}<br>
        <strong>Préférences du Conducteur :</strong> ${covoiturage.}
        <button onclick="reserver()">Réserver</button>
    `;
    document.getElementById('modalContent').innerHTML = modalContent;
    document.getElementById('detailsModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('detailsModal').style.display = 'none';
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('detailsModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
    // Initialize and add the map