<?php
$host = "localhost"; // Ou ton serveur MySQL
$user = "root"; // Ton utilisateur MySQL
$password = ""; // Ton mot de passe MySQL
$dbname = "covoiturage"; // Nom de ta base de données

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}


if(isset($_GET['depart']) && isset($_GET['destination'])) {
    $depart = $_GET['depart'];
    $destination = $_GET['destination'];
    

    $sql = "SELECT trajets.*, conducteurs.nom AS nom 
        FROM trajets 
        JOIN conducteurs ON trajets.conducteur_id = conducteurs.id 
        WHERE trajets.depart = ? AND trajets.destination = ? AND trajets.Places > 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $depart, $destination);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<div class='result-card'>";
            echo "<p><strong>Départ :</strong> " . $row["depart"] . "</p>";
            echo "<p><strong>Destination :</strong> " . $row["destination"] . "</p>";
            echo "<p><strong>Prix :</strong> " . $row["prix"] . " €</p>";
            echo "<p><strong>Date :</strong> " . $row["date_depart"] . "</p>";
            echo "<p><strong>Places disponibles :</strong> " . $row["Places"] . "</p>";
            echo "<p><strong>Conducteur :</strong> " . $row["nom"] . "</p>";
            echo "<p><strong>Note du Conducteur :</strong> " . $row["note_conducteur"] . "/5</p>";
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
        <strong>Point de rendez-vous :</strong> ${trajet.point_rdv}<br>
        <strong>Temps de trajet :</strong> ${trajet.temps_trajet} minutes<br>
        <strong>Nombre de places disponibles :</strong> ${trajet.places_disponibles}<br>
        <strong>Avis du Conducteur :</strong> ${trajet.avis_conducteur}<br>
        <strong>Modèle du Véhicule :</strong> ${trajet.modele_vehicule}<br>
        <strong>Marque du Véhicule :</strong> ${trajet.marque_vehicule}<br>
        <strong>Énergie utilisée :</strong> ${trajet.energie_vehicule}<br>
        <strong>Préférences du Conducteur :</strong> ${trajet.preferences_conducteur}
         <div id="map" style="width:100%;height:200px;"></div>
    `;
    document.getElementById('modalContent').innerHTML = modalContent;
    document.getElementById('detailsModal').style.display = 'block';
    initMap(trajet.latitude, trajet.longitude);
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