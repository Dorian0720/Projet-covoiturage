<?php
session_start(); // Démarrer la session

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['email']); // Supposons que $_SESSION['user'] contient les infos du user
?>



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
    <link rel="stylesheet" href="css/global.css" />
    <link rel="stylesheet" href="css/stylecss.css" />
    <link rel="stylesheet" href="css/index.css" />
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
<input type="date" id="date_depart" placeholder="Date de départ">

  <!-- Filtres -->
<div class="filters-container">
  <label>
    <input type="checkbox" id="places_filter" /> Trajets avec places disponibles
  </label>
  <label>
    <input type="checkbox" id="ecolo_filter" /> Véhicule écologique
  </label>
 <!-- Ajoute d'autres filtres selon tes besoins -->
</div>


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
          
        </div>
        <div class="text-link-list">
          <div class="text-strong-wrapper">
            <div class="div-wrapper"><div class="text-strong">Explore</div></div>
          </div>
          <div class="text-link-list-item"><div class="list-item">Design</div></div>
          <div class="text-link-list-item"><div class="list-item">FigJam</div></div>
        </div>
        <div class="text-link-list">
          <div class="text-strong-wrapper">
            <div class="div-wrapper"><div class="text-strong">Resources</div></div>
          </div>
          <div class="text-link-list-item"><div class="list-item">Blog</div></div>
          
        </div>
      </footer>
    </div>
    <script>
      function rechercher() {
        let depart = document.getElementById("lieux_depart").value;
        let destination = document.getElementById("lieux_arriver").value;
        let date = document.getElementById("date_depart").value;
        let resultDiv = document.getElementById("resultats");

        // Récupère l'état des filtres
    let places = document.getElementById("places_filter").checked ? 1 : 0;
    let ecolo = document.getElementById("ecolo_filter").checked ? 1 : 0;

        if (depart && destination && date) {
          let params = "lieux_depart=" + encodeURIComponent(depart) +
                       "&lieux_arriver=" + encodeURIComponent(destination) +
                       "&date_depart=" + encodeURIComponent(date) +
                       "&nb_place=" + places +
                       "&ecolo=" + ecolo;
          // Envoie une requête AJAX pour récupérer les résultats
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
/*filters */
.filters-container {
    display: flex-block;
    flex-direction: row;
    gap: 20px;
    justify-content: center;
    margin-top: 10px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}
.filters-container label {
    font-size: 1em;
    flex: auto;
    max-width: 200px;
    cursor: pointer;
}
/*fin filters */

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