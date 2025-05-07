<?php
session_start(); // Démarrer la session

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['email']); // Supposons que $_SESSION['user'] contient les infos du user
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
    // Remplacez les valeurs ci-dessous par celles récupérées de la base de données
    const modalContent = `
        <strong>Point de rendez-vous :</strong> ${lieux_depart}<br>
        <strong>Temps de trajet :</strong> ${date_arriver} minutes<br>
        <strong>Nombre de places disponibles :</strong> ${nb_place}<br>
        <strong>Avis du Conducteur :</strong> ${commentaire}<br>
        <strong>Modèle du Véhicule :</strong> ${modele}<br>
        <strong>Couleur du Véhicule :</strong> ${couleur}<br>
        <strong>Énergie utilisée :</strong> ${energie}<br>
        <strong>Préférences du Conducteur :</strong> ${statut}
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

if (isset($_GET['lieux_depart']) && isset($_GET['lieux_arriver'])) {
    $depart = filter_input(INPUT_GET, 'lieux_depart', FILTER_SANITIZE_STRING);
    $destination = filter_input(INPUT_GET, 'lieux_arriver', FILTER_SANITIZE_STRING);

    $sql = "SELECT * FROM covoiturage WHERE lieux_depart = ? AND lieux_arriver = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Erreur de préparation : ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("ss", $depart, $destination);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Afficher les résultats
        }
    } else {
        echo "<p>Aucun trajet trouvé.</p>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EcoRide</title>
    <link rel="stylesheet" href="global.css" />
    <link rel="stylesheet" href="stylecss.css" />
    <link rel="stylesheet" href="index.css" />
  </head>
  <body>
    <div class="examples-home-page">
      <header class="header">
        <div class="navigation-pill-list">
        <nav>
          <div class="navigation-pill"><div class="title"> <a href="index.php">Retour vers la page d’accueil</a></div></div>
          <div class="title-wrapper"><div class="text-wrapper"><a href="#">Accès aux covoiturages</a></div></div>
          <div class="title-wrapper"><div class="text-wrapper"><a href="index.html">Contact</a></div></div>
          <div class="title-wrapper">
    <div class="text-wrapper">
        <?php if ($isLoggedIn): ?>
            <a href="compte.php">Compte</a> <!-- Redirige vers la page compte -->
        <?php else: ?>
            <a href="./Formulaire/inscription/Inscription.php">Connexion</a> <!-- Garde Connexion si pas connecté -->
        <?php endif; ?>
    </div>
</div>

          
        </div>
      </header>
      <div class="frame"></div>
      <div class="hero-actions">
        <div class="text-content-title">
          <div class="text-wrapper-2">EcoRide</div>
          <p class="subtitle">Le covoiturage le plus rentable</p>
        </div>
         <h2>Recherche un trajet:</h2>
<div class="input-container">
<input type="text" id="lieux_depart" placeholder="Départ">
<input type="text" id="lieux_arriver" placeholder="Destination">
  <button class="rechercher" onclick="rechercher()">Rechercher</button>
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
          <img class="img" src="" />
          <div class="button-list">
            <img class="x-logo" src="" />
            <img class="img-2" src="" />
            <img class="img-2" src="" />
            <img class="img-2" src="" />
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
          <div class="text-link-list-item"><div class="list-item">figma</div></div>
          <div class="text-link-list-item"><div class="list-item">Color wheel</div></div>
          <div class="text-link-list-item"><div class="list-item">Support</div></div>
          <div class="text-link-list-item"><div class="list-item">Developers</div></div>
          <div class="text-link-list-item"><div class="list-item">Resource library</div></div>
        </div>
      </footer>
    </div>
    <script>
      function rechercher() {
        let depart = document.getElementById("lieux_depart").value;
        let destination = document.getElementById("lieux_arriver").value;
        let resultDiv = document.getElementById("resultats");

        if (depart && destination) {
            let xhr = new XMLHttpRequest();
            xhr.open("GET", "search.php?lieux_depart=" + encodeURIComponent(depart) + "&lieux_arriver=" + encodeURIComponent(destination), true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    resultDiv.innerHTML = xhr.responseText;
                } else {
                    resultDiv.innerHTML = "<p>Erreur lors de la recherche.</p>";
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

<style>
  /* Rendre le modal responsive */
.modal-content {
    width: 90%;
    max-width: 600px;
    margin: 10% auto;
    box-sizing: border-box;
}

/* Input + bouton de recherche */
.input-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    margin-bottom: 20px;
}

.input-container input,
.input-container button {
    padding: 10px;
    font-size: 1em;
    flex: 1 1 200px;
    max-width: 300px;
}

.result-container {
    padding: 10px;
    text-align: center;
}

/* Cards résultats */
.result-card {
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 15px;
    margin: 10px auto;
    max-width: 600px;
    background: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* Navigation en responsive */
.navigation-pill-list {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
    padding: 10px;
}

.navigation-pill a {
    display: block;
    padding: 8px 16px;
    background: #f0f0f0;
    border-radius: 20px;
    text-decoration: none;
    color: #333;
}

/* Responsive grid & layout */
.card-grid-2 {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
    padding: 20px;
}

/* Footer responsive */
.footer {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 20px;
    padding: 20px;
    background-color: #f5f5f5;
    text-align: center;
}

.text-link-list {
    flex: 1 1 200px;
}

/* Media queries pour petits écrans */
@media (max-width: 768px) {
    .text-content-title .text-wrapper-2 {
        font-size: 1.8em;
        text-align: center;
    }

    .subtitle {
        text-align: center;
    }

    .navigation-pill-list {
        flex-direction: column;
        align-items: center;
    }
}
</style>