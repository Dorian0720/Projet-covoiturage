-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 19 juil. 2025 à 13:41
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `covoiturage`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `avis_id` int(11) NOT NULL,
  `commentaire` varchar(50) DEFAULT NULL,
  `note` varchar(50) DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`avis_id`, `commentaire`, `note`, `statut`) VALUES
(1, 'Très bon conducteur, trajet agréable et ponctuel.', '5', 'validé'),
(2, 'Trajet correct mais un peu de retard au départ.', '3', 'validé'),
(3, 'Voiture propre et confortable, conducteur sympathi', '4', 'validé'),
(4, 'Annulation de dernière minute, très déçu.', '1', 'rejeté'),
(5, 'Trajet parfait, je recommande sans hésitation.', '5', 'validé'),
(6, 'Le conducteur ne respectait pas les règles de sécu', '2', 'en attente'),
(7, 'Bonne expérience, mais peu de communication.', '3', 'validé');

-- --------------------------------------------------------

--
-- Structure de la table `config`
--

CREATE TABLE `config` (
  `id_config` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `covoiturage`
--

CREATE TABLE `covoiturage` (
  `covoiturage_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `lieux_depart` varchar(100) NOT NULL,
  `lieux_arriver` varchar(100) NOT NULL,
  `date_depart` datetime NOT NULL,
  `nb_place` int(11) NOT NULL,
  `prix_personne` decimal(6,2) NOT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `vehicule` varchar(100) DEFAULT NULL,
  `note_conducteur` decimal(2,1) DEFAULT NULL,
  `energie` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `covoiturage`
--

INSERT INTO `covoiturage` (`covoiturage_id`, `utilisateur_id`, `lieux_depart`, `lieux_arriver`, `date_depart`, `nb_place`, `prix_personne`, `statut`, `vehicule`, `note_conducteur`, `energie`) VALUES
(3, 4, 'Bordeaux', 'Toulouse', '2025-06-03 00:00:00', 4, 12.50, 'complet', 'Citroën C3', 5.0, 'Électrique'),
(5, 2, 'Strasbourg', 'Mulhouse', '2025-06-05 00:00:00', 2, 7.50, 'actif', 'Fiat 500', 4.7, 'Essence'),
(6, 1, 'Lille', 'Bruxelles', '2025-06-06 00:00:00', 3, 11.00, 'actif', 'Toyota Yaris', 4.3, 'Hybride'),
(7, 2, 'Paris', 'Lyon', '2025-06-10 00:00:00', 3, 25.00, 'ouvert', 'Tesla Model 3', 4.8, 'électrique'),
(8, 3, 'Marseille', 'Nice', '2025-06-12 00:00:00', 2, 15.00, 'ouvert', 'Renault Zoe', 4.5, 'électrique'),
(9, 4, 'Bordeaux', 'Toulouse', '2025-06-09 00:00:00', 4, 18.00, 'ouvert', 'Peugeot 208', 4.2, 'essence'),
(10, 2, 'Paris', 'Lille', '2025-06-11 00:00:00', 1, 30.00, 'ouvert', 'Nissan Leaf', 4.9, 'électrique'),
(11, 5, 'Lyon', 'Grenoble', '2025-06-08 00:00:00', 3, 12.00, 'ouvert', 'Citroën C3', 3.9, 'diesel'),
(12, 3, 'Nice', 'Marseille', '2025-06-13 00:00:00', 2, 17.00, 'ouvert', 'BMW i3', 4.7, 'électrique'),
(13, 6, 'Toulouse', 'Bordeaux', '2025-06-14 00:00:00', 4, 20.00, 'ouvert', 'Toyota Prius', 4.3, 'hybride'),
(14, 2, 'Paris', 'Lyon', '2025-06-15 00:00:00', 2, 22.00, 'ouvert', 'Tesla Model Y', 4.6, 'électrique'),
(15, 2, 'Paris', 'Roeun', '2025-06-30 12:30:18', 4, 2.00, 'actif', 'Nissan GTR', 4.5, 'Essence'),
(16, 2, 'Rouen', 'Paris', '2025-06-16 18:20:00', 3, 2.00, 'actif', 'Nissan GTR', 4.5, 'Essence'),
(17, 2, 'Yvetot', 'Paris', '2025-06-27 00:00:00', 4, 3.00, NULL, '2', NULL, NULL),
(18, 2, 'Yvetot', 'Paris', '2025-08-11 15:28:06', 4, 2.00, 'actif', 'Toyota', 4.5, 'Essence'),
(19, 2, 'Yvetot', 'Paris', '2025-07-25 00:00:00', 4, 3.00, NULL, '7', NULL, NULL),
(20, 2, 'Paris', 'Rouen', '2007-09-12 00:00:00', 2, 3.00, NULL, '8', NULL, NULL),
(21, 9, 'Dieppe', 'Paris', '2025-07-12 00:00:00', 4, 3.00, NULL, '9', NULL, NULL),
(22, 2, 'Paris', 'Paris', '2025-07-23 00:00:00', 2, 3.00, NULL, '2', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `marque`
--

CREATE TABLE `marque` (
  `marque_id` int(11) NOT NULL,
  `libelle` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `parametre`
--

CREATE TABLE `parametre` (
  `parametre_id` int(11) NOT NULL,
  `propriete` varchar(50) DEFAULT NULL,
  `valeur` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`role_id`, `libelle`) VALUES
(1, 'Conducteur'),
(2, 'Passager'),
(3, 'Les deux');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `utilisateur_id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `pseudo` varchar(50) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `role_id` int(11) NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`utilisateur_id`, `nom`, `prenom`, `email`, `password`, `photo`, `pseudo`, `telephone`, `adresse`, `role_id`) VALUES
(1, 'Dupont', 'Jean', 'jean.dupont@example.com', 'hashed_mdp1', NULL, 'jdupont', '0600000001', '12 rue de Paris, Paris', 1),
(2, 'mutel', 'dorian', 'dorian.mutel@gmail.com', '$2y$10$qq9oCk.Eute0nQZqdNDq9eQT5RNG9RyURevbI1sT10gMx7Tu6znq6', 'uploads/6836f6fadf6bf_4c68ae34de502d96fdc9283daec1a14a.jpg', 'dowars49', '08 82 88 90', '34rue pannarche', 2),
(3, 'Martin', 'Luc', 'luc.martin@example.com', 'hashed_mdp3', NULL, 'lmartin', '0600000003', '33 boulevard Haussmann, Paris', 1),
(4, 'Petit', 'Emma', 'emma.petit@example.com', 'hashed_mdp4', NULL, 'epetit', '0600000004', '88 rue Alsace, Strasbourg', 2),
(5, 'Lemoine', 'Paul', 'paul.lemoine@example.com', 'hashed_mdp5', NULL, 'plemoine', '0600000005', '10 place Bellecour, Lyon', 1),
(6, 'Moreau', 'Julie', 'julie.moreau@example.com', 'hashed_mdp6', NULL, 'jmoreau', '0600000006', '5 quai de la Loire, Nantes', 2),
(8, 'orton', 'randy', 'randy.orton@gmail.com', '$2y$10$19jJ6LSkljempdH7DHUrqOzMCYVYeBRVIToxmMLiDelHm9TMOQl56', 'uploads/1750248522_OSK.jpg', 'rko', '', '', 2),
(9, 'azerty', 'azerty', 'azertyu@gmail.com', '$2y$10$KtSGKrqD0vH5981Pf5z6FusyCZxfUal47Zdd.rA/xXBOdqWLYRghm', 'uploads/1752247783_explorer_uQ7KMuSqu4.png', 'Admin2', '02111119', '', 1),
(10, 'Venom', 'Snake', 'Snake23@gmail.com', '$2y$10$Zsc7DqXguFYP70ZZ6mi1AuDOVIo3/Vm9h2fJLu5lqp3okrYq.yI8q', 'uploads/1752581390_explorer_uQ7KMuSqu4.png', 'Phontom', '08 82 88 92', '', 1);

-- --------------------------------------------------------

--
-- Structure de la table `voiture`
--

CREATE TABLE `voiture` (
  `voiture_id` int(11) NOT NULL,
  `modele` varchar(50) DEFAULT NULL,
  `immatriculation` varchar(50) DEFAULT NULL,
  `energie` varchar(50) DEFAULT NULL,
  `couleur` varchar(50) DEFAULT NULL,
  `date_premiere_immatriculation` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `voiture`
--

INSERT INTO `voiture` (`voiture_id`, `modele`, `immatriculation`, `energie`, `couleur`, `date_premiere_immatriculation`) VALUES
(1, 'Peugeot 208', 'AB-123-CD', 'Essence', 'Rouge', '2019-05-14'),
(2, 'toyota', 'AA-123-ZZZ', 'Diesel', 'Gris', '2020-03-22'),
(3, 'Tesla Model 3', 'IJ-789-KL', 'Électrique', 'Blanc', '2022-11-01'),
(4, 'Citroën C3', 'MN-321-OP', 'Essence', 'Bleu', '2018-07-30'),
(5, 'Dacia Sandero', 'QR-654-ST', 'GPL', 'Noir', '2021-06-10'),
(6, 'Volkswagen Golf', 'UV-987-WX', 'Hybride', 'Vert foncé', '2023-01-15'),
(7, 'toyota', '12345', NULL, NULL, NULL),
(8, 'toyota Z1', '12346', 'Essence', 'Rose', '2009-09-13'),
(9, 'Peugeot', '123456', 'Essence', 'Rose', '2009-09-12');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`avis_id`),
  ADD UNIQUE KEY `avis_id` (`avis_id`);

--
-- Index pour la table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id_config`),
  ADD UNIQUE KEY `id_config` (`id_config`);

--
-- Index pour la table `covoiturage`
--
ALTER TABLE `covoiturage`
  ADD PRIMARY KEY (`covoiturage_id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `marque`
--
ALTER TABLE `marque`
  ADD PRIMARY KEY (`marque_id`),
  ADD UNIQUE KEY `marque_id` (`marque_id`);

--
-- Index pour la table `parametre`
--
ALTER TABLE `parametre`
  ADD PRIMARY KEY (`parametre_id`),
  ADD UNIQUE KEY `parametre_id` (`parametre_id`);

--
-- Index pour la table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`utilisateur_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Index pour la table `voiture`
--
ALTER TABLE `voiture`
  ADD PRIMARY KEY (`voiture_id`),
  ADD UNIQUE KEY `voiture_id` (`voiture_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `avis_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `config`
--
ALTER TABLE `config`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `covoiturage`
--
ALTER TABLE `covoiturage`
  MODIFY `covoiturage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `marque`
--
ALTER TABLE `marque`
  MODIFY `marque_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `parametre`
--
ALTER TABLE `parametre`
  MODIFY `parametre_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `utilisateur_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `voiture`
--
ALTER TABLE `voiture`
  MODIFY `voiture_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `covoiturage`
--
ALTER TABLE `covoiturage`
  ADD CONSTRAINT `covoiturage_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`);

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
