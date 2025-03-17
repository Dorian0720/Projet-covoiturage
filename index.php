// Description: Page d'accueil du site web
<!-- Modal HTML -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <p id="modalContent"></p>
    </div>
</div>
<h2>hhhhhhhhhhhhhhhhhhhhh</h2>
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
            echo "</div>";
        }
    } else {
        echo "<p>Aucun trajet trouvé.</p>";
    }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="global.css" />
    <link rel="stylesheet" href="stylecss.css" />
    <link rel="stylesheet" href="index.css" />
  </head>
  <body>
    <div class="examples-home-page">
      <header class="header">
        <div class="block"><img class="figma" src="./image/7657567.jpg" /></div>
        <div class="navigation-pill-list">
        <nav>
          <div class="navigation-pill"><div class="title"> <a href="index.php">Retour vers la page d’accueil</a></div></div>
          <div class="title-wrapper"><div class="text-wrapper"><a href="covoiturage.php">Accès aux covoiturages</a></div></div>
          <div class="title-wrapper"><div class="text-wrapper"><a href="index.html">Contact</a></div></div>
          <div class="title-wrapper"><div class="text-wrapper"><a href="./Formulaire/inscription/Inscription.php">Connexion</a></div></div>
        </div>
      </header>
      <div class="frame"></div>
      <div class="hero-actions">
        <div class="text-content-title">
          <div class="text-wrapper-2">EcoRide</div>
          <p class="subtitle">Le covoiturage le plus rentable</p>
        </div>
         <h2>Recherche trajet:</h2>
<div class="input-container">
<input type="text" id="depart" placeholder="Départ">
<input type="text" id="destination" placeholder="Destination">
  <button onclick="rechercher()">Rechercher</button>
</div>
<div class="result-container">
<h2>Résultat:</h2>
<div id="resultats"></div>
</div>

      <img class="section" src="./image/view-3d-car-with-trees.jpg" />
      <div class="card-grid">
        <div class="text-content-heading">
          <div class="text-wrapper-3">Qui somme nous</div>
          <div class="text-wrapper-4">La startup "EcoRide" fraichement crée en France, a pour objectif de réduire l'impact
environnemental des déplacements en encourageant le covoiturage. EcoRide prône une
approche écologique</div>
        </div>
        <div class="card-grid-2">
          <div class="testimonial-card">
            <div class="div-wrapper"><div class="text-heading">“Quote”</div></div>
            <div class="avatar-block">
              <div class="avatar"></div>
              <div class="info">
                <div class="text-wrapper-5">Title</div>
                <div class="text-wrapper-6">Description</div>
              </div>
            </div>
          </div>
          <div class="testimonial-card">
            <div class="div-wrapper"><div class="text-heading">“Quote”</div></div>
            <div class="avatar-block">
              <div class="avatar-2"></div>
              <div class="info">
                <div class="text-wrapper-5">Title</div>
                <div class="text-wrapper-6">Description</div>
              </div>
            </div>
          </div>
          <div class="testimonial-card">
            <div class="div-wrapper"><div class="text-heading">“Quote”</div></div>
            <div class="avatar-block">
              <div class="avatar-3"></div>
              <div class="info">
                <div class="text-wrapper-5">Title</div>
                <div class="text-wrapper-6">Description</div>
              </div>
            </div>
          </div>
          <div class="testimonial-card">
            <div class="div-wrapper"><div class="text-heading">“Quote”</div></div>
            <div class="avatar-block">
              <div class="avatar-4"></div>
              <div class="info">
                <div class="text-wrapper-5">Title</div>
                <div class="text-wrapper-6">Description</div>
              </div>
            </div>
          </div>
          <div class="testimonial-card">
            <div class="div-wrapper"><div class="text-heading">“Quote”</div></div>
            <div class="avatar-block">
              <div class="avatar-5"></div>
              <div class="info">
                <div class="text-wrapper-5">Title</div>
                <div class="text-wrapper-6">Description</div>
              </div>
            </div>
          </div>
          <div class="testimonial-card">
            <div class="div-wrapper"><div class="text-heading">“Quote”</div></div>
            <div class="avatar-block">
              <div class="avatar-6"></div>
              <div class="info">
                <div class="text-wrapper-5">Title</div>
                <div class="text-wrapper-6">Description</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <footer class="footer">
        <div class="title-2">
          <img class="img" src="img/image.svg" />
          <div class="button-list">
            <img class="x-logo" src="img/x-logo.svg" />
            <img class="img-2" src="img/logo-instagram.svg" />
            <img class="img-2" src="img/logo-youtube.svg" />
            <img class="img-2" src="img/linked-in.svg" />
          </div>
        </div>
        <div class="text-link-list">
          <div class="text-strong-wrapper">
            <div class="div-wrapper"><div class="text-strong">Use cases</div></div>
          </div>
          <div class="text-link-list-item"><div class="list-item">UI design</div></div>
          <div class="text-link-list-item"><div class="list-item">UX design</div></div>
          <div class="text-link-list-item"><div class="list-item">Wireframing</div></div>
          <div class="text-link-list-item"><div class="list-item">Diagramming</div></div>
          <div class="text-link-list-item"><div class="list-item">Brainstorming</div></div>
          <div class="text-link-list-item"><div class="list-item">Online whiteboard</div></div>
          <div class="text-link-list-item"><div class="list-item">Team collaboration</div></div>
        </div>
        <div class="text-link-list">
          <div class="text-strong-wrapper">
            <div class="div-wrapper"><div class="text-strong">Explore</div></div>
          </div>
          <div class="text-link-list-item"><div class="list-item">Design</div></div>
          <div class="text-link-list-item"><div class="list-item">Prototyping</div></div>
          <div class="text-link-list-item"><div class="list-item">Development features</div></div>
          <div class="text-link-list-item"><div class="list-item">Design systems</div></div>
          <div class="text-link-list-item"><div class="list-item">Collaboration features</div></div>
          <div class="text-link-list-item"><div class="list-item">Design process</div></div>
          <div class="text-link-list-item"><div class="list-item">FigJam</div></div>
        </div>
        <div class="text-link-list">
          <div class="text-strong-wrapper">
            <div class="div-wrapper"><div class="text-strong">Resources</div></div>
          </div>
          <div class="text-link-list-item"><div class="list-item">Blog</div></div>
          <div class="text-link-list-item"><div class="list-item">Best practices</div></div>
          <div class="text-link-list-item"><div class="list-item">Colors</div></div>
          <div class="text-link-list-item"><div class="list-item">Color wheel</div></div>
          <div class="text-link-list-item"><div class="list-item">Support</div></div>
          <div class="text-link-list-item"><div class="list-item">Developers</div></div>
          <div class="text-link-list-item"><div class="list-item">Resource library</div></div>
        </div>
      </footer>
    </div>
    <script>
        function rechercher() {
            let depart = document.getElementById("depart").value;
            let destination = document.getElementById("destination").value;
            let resultDiv = document.getElementById("resultats");

            if (depart && destination) {
                let xhr = new XMLHttpRequest();
                xhr.open("GET", "search.php?depart=" + depart + "&destination=" + destination, true);
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        resultDiv.innerHTML = xhr.responseText;
                    }
                };
                xhr.send();
            } else {
                resultDiv.innerHTML = "<p>Veuillez remplir les champs.</p>";
            }
        }
    </script>
  </body>
</html>