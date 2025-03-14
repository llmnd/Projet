
DROP TABLE IF EXISTS `arbitres`;
CREATE TABLE IF NOT EXISTS `arbitres` (
  `id` int NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `arbitres`
--

INSERT INTO `arbitres` (`id`, `username`, `mot_de_passe`) VALUES
(1, 'Arbitre 1', NULL),
(2, 'Arbitre 2', NULL),
(3, 'Arbitre 3', NULL),
(4, 'arbitre', 'passer');

-- --------------------------------------------------------

--
-- Structure de la table `boxeurs`
--

DROP TABLE IF EXISTS `boxeurs`;
CREATE TABLE IF NOT EXISTS `boxeurs` (
  `id` int NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `pays` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `age` int NOT NULL,
  `classement` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `boxeurs`
--

INSERT INTO `boxeurs` (`id`, `nom`, `pays`, `age`, `classement`) VALUES
(1, 'Canelo Alvarez', 'Mexique', 33, 0),
(2, 'Oleksandr Usyk', 'Ukraine', 37, 0),
(3, 'Tyson Fury', 'Royaume-Uni', 35, 0),
(4, 'Naoya Inoue', 'Japon', 30, 0),
(5, 'Gervonta Davis', 'USA', 29, 0),
(6, 'Dmitry Bivol', 'Russie', 33, 0),
(7, 'Artur Beterbiev', 'Canada', 39, 0),
(8, 'Teofimo Lopez', 'USA', 26, 0);

-- --------------------------------------------------------

--
-- Structure de la table `matchs`
--

DROP TABLE IF EXISTS `matchs`;
CREATE TABLE IF NOT EXISTS `matchs` (
  `id` int NOT NULL,
  `boxeur1` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `boxeur2` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `date_match` date NOT NULL,
  `gagnant_id` int DEFAULT NULL,
  `termine` tinyint(1) DEFAULT '0',
  `statut` enum('en_cours','termine') COLLATE utf8mb4_general_ci DEFAULT 'en_cours',
  PRIMARY KEY (`id`),
  KEY `gagnant_id` (`gagnant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rounds`
--

DROP TABLE IF EXISTS `rounds`;
CREATE TABLE IF NOT EXISTS `rounds` (
  `id` int NOT NULL,
  `match_id` int NOT NULL,
  `round_number` int NOT NULL,
  `winner_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`),
  KEY `winner_id` (`winner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `stats`
--

DROP TABLE IF EXISTS `stats`;
CREATE TABLE IF NOT EXISTS `stats` (
  `id` int NOT NULL,
  `boxeur_id` int DEFAULT NULL,
  `victoires` int DEFAULT '0',
  `defaites` int DEFAULT '0',
  `KO` int DEFAULT '0',
  `decision` int DEFAULT '0',
  `abandon` int DEFAULT '0',
  `TKO` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `boxeur_id` (`boxeur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tournoi`
--

DROP TABLE IF EXISTS `tournoi`;
CREATE TABLE IF NOT EXISTS `tournoi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ronde` int NOT NULL,
  `boxeur1_id` int NOT NULL,
  `boxeur2_id` int DEFAULT NULL,
  `gagnant_id` int DEFAULT NULL,
  `termine` tinyint(1) DEFAULT '0',
  `nom` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `categorie` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `annee` int NOT NULL,
  `arbitre_id` int DEFAULT NULL,
  `date_combat` datetime DEFAULT NULL,
  `type` enum('round','semi_finale','finale') COLLATE utf8mb4_general_ci DEFAULT 'round',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tournoi`
--

INSERT INTO `tournoi` (`id`, `ronde`, `boxeur1_id`, `boxeur2_id`, `gagnant_id`, `termine`, `nom`, `categorie`, `annee`, `arbitre_id`, `date_combat`, `type`) VALUES
(7, 3, 5, 7, 7, 1, '', '', 0, NULL, '2025-03-07 08:49:41', 'finale');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','arbitre') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, '0', '0', 'arbitre'),
(2, 'arbitre', 'passer', 'arbitre'),
(5, 'lamine', 'passer', 'admin');