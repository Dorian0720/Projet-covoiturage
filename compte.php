<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
$isLoggedIn = isset($_SESSION['email']);
$email = $_SESSION['email'];
$conn = new mysqli("localhost", "root", "", "covoiturage");
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Récupérer les informations de l'utilisateur
$stmt = $conn->prepare("
    SELECT utilisateur.*, role.libelle
    FROM utilisateur
    JOIN role ON utilisateur.role_id = role.role_id
    WHERE utilisateur.email = ?
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


$nom = $user['nom'];
$prenom = $user['prenom'];
$email = $user['email'];
$telephone = $user['telephone'];
$role_id = $user['role_id'];
$photo_profil = $user['photo'] ?? null;

// Récupérer les véhicules de l'utilisateur (correction ici)
$vehicules = [];
$stmt_veh = $conn->prepare("SELECT voiture_id, modele, immatriculation FROM voiture WHERE voiture_id = ?");
$stmt_veh->bind_param("i", $user['utilisateur_id']);
$stmt_veh->execute();
$result_veh = $stmt_veh->get_result();
while ($row = $result_veh->fetch_assoc()) {
    $vehicules[] = $row;
}
$stmt_veh->close();

// Gestion des crédits via cookie
if (!isset($_COOKIE['credits'])) {
    setcookie('credits', 100, time() + 3600*24*365, "/");
    $credits = 100;
} else {
    $credits = (int)$_COOKIE['credits'];
}

// Traitement modification infos utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_infos'])) {
    $nouveau_nom = trim($_POST['nom']);
    $nouveau_prenom = trim($_POST['prenom']);
    $nouveau_telephone = trim($_POST['telephone']);
    $nouveau_adresse = trim($_POST['adresse']);
    $nouveau_role_id = (int)$_POST['role_id'];

    // Gestion de la photo de profil
    if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === UPLOAD_ERR_OK) {
        $photo_tmp = $_FILES['photo_profil']['tmp_name'];
        $photo_name = uniqid() . "_" . basename($_FILES['photo_profil']['name']);
        $photo_path = "uploads/" . $photo_name;

        $allowed_types = ['image/jpeg', 'image/png'];
        if (!in_array($_FILES['photo_profil']['type'], $allowed_types)) {
            echo "<p style='color: red;'>Format de fichier non valide. Seuls les fichiers JPG et PNG sont acceptés.</p>";
            exit();
        }
        $max_size = 2 * 1024 * 1024; // 2 Mo
        if ($_FILES['photo_profil']['size'] > $max_size) {
            echo "<p style='color: red;'>Le fichier est trop volumineux. Taille maximale : 2 Mo.</p>";
            exit();
        }
        if ($photo_profil && file_exists($photo_profil)) {
            unlink($photo_profil);
        }
        if (move_uploaded_file($photo_tmp, $photo_path)) {
            $nouveau_photo = $photo_path;
        } else {
            echo "<p style='color: red;'>Erreur lors du téléchargement de la photo.</p>";
            $nouveau_photo = $photo_profil;
        }
    } else {
        $nouveau_photo = $photo_profil;
    }

    // Mettre à jour les informations dans la base de données
    $stmt = $conn->prepare("UPDATE utilisateur SET nom = ?, prenom = ?, telephone = ?, adresse = ?, photo = ?, role_id = ? WHERE email = ?");
    $stmt->bind_param("sssssis", $nouveau_nom, $nouveau_prenom, $nouveau_telephone, $nouveau_adresse, $nouveau_photo, $nouveau_role_id, $email);

    if ($stmt->execute()) {
        $_SESSION['nom'] = $nouveau_nom;
        $_SESSION['prenom'] = $nouveau_prenom;
        $_SESSION['telephone'] = $nouveau_telephone;
        $_SESSION['adresse'] = $nouveau_adresse;
        $_SESSION['photo'] = $nouveau_photo;
        echo "<p style='color: green; text-align: center;'>Informations mises à jour avec succès !</p>";
        $nom = $nouveau_nom;
        $prenom = $nouveau_prenom;
        $telephone = $nouveau_telephone;
        $role_id = $nouveau_role_id;
        $photo_profil = $nouveau_photo;
    } else {
        echo "<p style='color:red;'>Erreur lors de la mise à jour des informations.</p>";
    }
    $stmt->close();
}

// Traitement proposition de trajet (pour chauffeur)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proposer_trajet'])) {
    $lieux_depart = isset($_POST['lieux_depart']) ? trim($_POST['lieux_depart']) : '';
    $lieux_arriver = isset($_POST['lieux_arriver']) ? trim($_POST['lieux_arriver']) : '';
    $prix_personne = isset($_POST['prix_personne']) ? (int)$_POST['prix_personne'] : 0;
    $date_depart = isset($_POST['date_depart']) ? $_POST['date_depart'] : '';
    $nb_place = isset($_POST['nb_place']) ? (int)$_POST['nb_place'] : 0;

    // Gestion du véhicule
    if (isset($_POST['vehicule']) && $_POST['vehicule'] === 'nouveau') {
        $nouveau_modele = isset($_POST['nouveau_modele']) ? trim($_POST['nouveau_modele']) : '';
        $nouvelle_immatriculation = isset($_POST['nouvelle_immatriculation']) ? trim($_POST['nouvelle_immatriculation']) : '';
        $nouvelle_energie = isset($_POST['nouvelle_energie']) ? trim($_POST['nouvelle_energie']) : '';
        $nouvelle_couleur = isset($_POST['nouvelle_couleur']) ? trim($_POST['nouvelle_couleur']) : '';
        $nouvelle_date_immat = isset($_POST['nouvelle_date_immat']) ? trim($_POST['nouvelle_date_immat']) : '';
        if (
            empty($nouveau_modele) ||
            empty($nouvelle_immatriculation) ||
            empty($nouvelle_energie) ||
            empty($nouvelle_couleur) ||
            empty($nouvelle_date_immat)
        ) {
            echo "<script>window.onload = function(){document.getElementById('vehicule').value='nouveau';document.getElementById('nouveauVehicule').style.display='block';}</script>";
            echo "<p style='color:red;'>Veuillez renseigner tous les champs du nouveau véhicule.</p>";
            return;
        }
        // Insertion du véhicule
        $stmt_nv = $conn->prepare("INSERT INTO voiture (modele, immatriculation, energie, couleur, date_premiere_immatriculation)
VALUES (?, ?, ?, ?, ?)");
$stmt_nv->bind_param("sssss", $nouveau_modele, $nouvelle_immatriculation, $nouvelle_energie, $nouvelle_couleur, $nouvelle_date_immat);
$stmt_nv->execute();
$vehicule_id = $conn->insert_id;
$stmt_nv->close();
    } elseif (isset($_POST['vehicule']) && $_POST['vehicule'] !== '') {
        $vehicule_id = (int)$_POST['vehicule'];
    } else {
        echo "<p style='color:red;'>Veuillez sélectionner ou ajouter un véhicule.</p>";
        return;
    }

    // Vérification des champs requis
    if (
        empty($lieux_depart) ||
        empty($lieux_arriver) ||
        empty($date_depart) ||
        empty($nb_place) ||
        empty($prix_personne) ||
        empty($vehicule_id)
    ) {
        echo "<p style='color:red;'>Veuillez remplir tous les champs requis.</p>";
        return;
    }

    // Insertion du trajet dans la base de données
    $stmt_trajet = $conn->prepare("INSERT INTO covoiturage (utilisateur_id, lieux_depart, lieux_arriver, date_depart, nb_place, prix_personne, vehicule) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_trajet->bind_param("isssiii", $user['utilisateur_id'], $lieux_depart, $lieux_arriver, $date_depart, $nb_place, $prix_personne, $vehicule_id);
    if ($stmt_trajet->execute()) {
        echo "<p style='color:green;'>Trajet publié avec succès !</p>";
    } else {
        echo "<p style='color:red;'>Erreur lors de la publication du trajet.</p>";
    }
    $stmt_trajet->close();
}

// Récupération de l'historique des participations depuis le cookie
$historique = [];
if (isset($_COOKIE['participations'])) {
    $historique = json_decode($_COOKIE['participations'], true);
    if (!is_array($historique)) $historique = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon compte</title>
    <link rel="stylesheet" href="css/stylecss.css">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="examples-home-page">
        <header class="header">
            <div class="navigation-pill-list">
                <nav class="navigation">
                    <div class="navigation-pill">
                        <div class="title"><a href="index.php">Retour vers la page d’accueil</a></div>
                    </div>
                    <div class="title-wrapper"><div class="text-wrapper"><a href="#">Accès aux covoiturages</a></div></div>
                    <div class="title-wrapper"><div class="text-wrapper"><a href="index.html">Contact</a></div></div>
                    <div class="title-wrapper"><div class="text-wrapper">
                        <?php if ($isLoggedIn): ?>
                            <a href="compte.php">Compte</a>
                        <?php else: ?>
                            <a href="./Formulaire/inscription/Inscription.php">Connexion</a>
                        <?php endif; ?>
                    </div>
                </div>
                </nav>
            </div>
        </header>
        <div class="container" >
            <h1>Bienvenue sur ton compte</h1>
            <p><strong>Email :</strong> <?= htmlspecialchars($email) ?></p>
            <p><strong>Nom :</strong> <?= htmlspecialchars($nom) ?></p>
            <p><strong>Prénom :</strong> <?= htmlspecialchars($prenom) ?></p>
            <p><strong>Numéro :</strong> <?= htmlspecialchars($telephone) ?></p>
            <p><strong>Rôle :</strong>
                <?php
                if ($role_id == 1) echo "Conducteur";
                elseif ($role_id == 2) echo "Passager";
                elseif ($role_id == 3) echo "Les deux";
                else echo htmlspecialchars($role_id);
                ?>
            </p>
            <!-- <p><strong>Crédits disponibles :</strong> <span class="credits"><?= htmlspecialchars($credits) ?> crédits</span></p> -->

            <?php if ($photo_profil): ?>
            <div class="profil-photo-header">
                <img src="<?= htmlspecialchars($photo_profil) ?>" alt="Photo de profil" />
            </div>
            <?php endif; ?>

            <form action="logout.php" method="post">
                <button type="submit" class="button-logout">Se déconnecter</button>
            </form>

            <div class="modifier-section">
                <h2>Modifier mes informations</h2><br>
                <form method="post" enctype="multipart/form-data">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" required>
                    <label for="prenom">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($prenom) ?>" required>
                    <label for="telephone">Numéro de téléphone :</label>
                    <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($telephone) ?>" required>
                    <label for="adresse">Adresse :</label>
                    <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($user['adresse'] ?? '') ?>">
                    <label for="role_id">Rôle :</label>
                    <select id="role_id" name="role_id">
                        <option value="1" <?= $role_id == 1 ? 'selected' : '' ?>>Conducteur</option>
                        <option value="2" <?= $role_id == 2 ? 'selected' : '' ?>>Passager</option>
                        <option value="3" <?= $role_id == 3 ? 'selected' : '' ?>>Les deux</option>
                    </select>
                    <label for="photo_profil">Photo de profil :</label>
                    <input type="file" id="photo_profil" name="photo_profil" accept="image/jpeg, image/png">
                    <p style="font-size: 12px; color: #888;">(Taille maximale : 2 Mo)</p>
                    <p style="font-size: 12px; color: #888;">(Formats acceptés : JPG, PNG)</p>
                    <button type="submit" name="modifier_infos" class="modifier-button">Enregistrer</button>
                </form>
            </div>
            
            <?php if ($role_id == 1 || $role_id == 3): ?>
            <div class="modifier-section">
                <h2>Proposer un nouveau voyage</h2>
                <form method="post" id="formTrajet">
                    <label for="lieux_depart">Adresse de départ :</label>
                    <input type="text" id="lieux_depart" name="lieux_depart" required>
                    <label for="lieux_arriver">Adresse d’arrivée :</label>
                    <input type="text" id="lieux_arriver" name="lieux_arriver" required>
                    <label for="vehicule">Véhicule :</label>
                    <select id="vehicule" name="vehicule" required>
                        <option value="">-- Choisir un véhicule --</option>
                        <?php foreach ($vehicules as $v): ?>
                            <option value="<?= $v['voiture_id'] ?>">
                                <?= htmlspecialchars($v['modele']) ?> (<?= htmlspecialchars($v['immatriculation']) ?>)
                            </option>
                        <?php endforeach; ?>
                        <option value="nouveau">Ajouter un nouveau véhicule</option>
                    </select>
                    <div id="nouveauVehicule" style="display:none; margin-top:10px;">
                        <label for="nouveau_modele">Modèle :</label>
                        <input type="text" id="nouveau_modele" name="nouveau_modele">
                        <label for="nouvelle_immatriculation">Immatriculation :</label>
                        <input type="text" id="nouvelle_immatriculation" name="nouvelle_immatriculation">
                        <label for="nouvelle_energie">Énergie :</label>
                        <input type="text" id="nouvelle_energie" name="nouvelle_energie">
                        <label for="nouvelle_couleur">Couleur :</label>
                        <input type="text" id="nouvelle_couleur" name="nouvelle_couleur">
                        <label for="nouvelle_date_immat">Date d'immatriculation :</label>
                        <input type="date" id="nouvelle_date_immat" name="nouvelle_date_immat">
                    </div>
                    <label for="prix_personne">Prix par personne (crédits) :</label>
                    <input type="number" id="prix_personne" name="prix_personne" min="3" required>
                    <small>2 crédits seront prélevés par la plateforme sur chaque réservation.</small><br><br>
                    <label for="date_depart">Date de départ :</label>
                    <input type="date" id="date_depart" name="date_depart" required>
                    <label for="nb_place">Nombre de places :</label>
                    <input type="number" id="nb_place" name="nb_place" min="1" required>
                    <button type="submit" name="proposer_trajet" class="modifier-button">Publier le trajet</button>
                </form>
            </div>
            <script>
            // Affichage dynamique du formulaire nouveau véhicule
            function toggleNouveauVehicule() {
                var select = document.getElementById('vehicule');
                var div = document.getElementById('nouveauVehicule');
                div.style.display = (select.value === 'nouveau') ? 'block' : 'none';
            }
            document.getElementById('vehicule').addEventListener('change', toggleNouveauVehicule);
            // Affiche le formulaire si erreur après reload
            window.onload = function() {
                if(document.getElementById('vehicule').value === 'nouveau') {
                    document.getElementById('nouveauVehicule').style.display = 'block';
                }
            };
            </script>
            <?php endif; ?>

            <!-- Historique des participations en fin de page -->
            <div class="modifier-section">
                <h2>Historique de mes participations</h2>
                <?php if (count($historique) > 0): ?>
                    <ul>
                    <?php foreach ($historique as $id): ?>
                        <?php
                        $stmt = $conn->prepare("SELECT lieux_depart, lieux_arriver, date_depart FROM covoiturage WHERE covoiturage_id = ?");
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($row = $result->fetch_assoc()):
                        ?>
                        <li>
                            Départ : <?= htmlspecialchars($row['lieux_depart']) ?> |
                            Arrivée : <?= htmlspecialchars($row['lieux_arriver']) ?> |
                            Date : <?= htmlspecialchars($row['date_depart']) ?>
                        </li>
                        <?php
                        endif;
                        $stmt->close();
                        ?>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucune participation enregistrée.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>