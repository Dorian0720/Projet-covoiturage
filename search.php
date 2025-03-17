<?php
include "config.php";

if(isset($_GET['depart']) && isset($_GET['destination'])) {
    $depart = $_GET['depart'];
    $destination = $_GET['destination'];

    $sql = "SELECT * FROM trajets WHERE depart = ? AND destination = ?";
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
            echo "<p><strong>Conducteur :</strong> " . $row["conducteur"] . "</p>";
            echo "<p><strong>Note du Conducteur :</strong> " . $row["note_conducteur"] . "/5</p>";
            echo "<button onclick='showDetails(" . json_encode($row) . ")'>Détails</button>";
            echo "</div>";
        }
    } else {
        echo "<p>Aucun trajet trouvé.</p>";
    }
}
?>
<!-- Modal HTML -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <p id="modalContent"></p>
    </div>
</div>

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
function initMap(lat, lng) {
    const location = { lat: parseFloat(lat), lng: parseFloat(lng) };
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 15,
        center: location,
    });
    const marker = new google.maps.Marker({
        position: location,
        map: map,
    });
}

function reserver() {
    alert("Réservation effectuée !");
}
}
</script>

<style>
/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style>

<!-- Include the Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>
