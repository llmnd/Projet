CREATE TABLE `administrateurs` (
  `id` int(11) NOT NULL,
  `login` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `administrateurs`
--

INSERT INTO `administrateurs` (`id`, `login`, `mot_de_passe`) VALUES
(1, 'lamine', 'passer'),
(2, 'Cheikh', '$2y$10$.rC3LJlnmJwSMOmI79Yk5.V5WQVQzXET7w3.z7fNVx.sI1U5L2E4G');

-- --------------------------------------------------------

--
-- Structure de la table `arbitres`
--

CREATE TABLE `arbitres` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL
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

CREATE TABLE `boxeurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `pays` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `classement` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `boxeurs`
--

INSERT INTO `boxeurs` (`id`, `nom`, `pays`, `age`, `classement`) VALUES
(1, 'Canelo Alvarez', 'Mexique', 33, 1),
(2, 'Oleksandr Usyk', 'Ukraine', 37, 2),
(3, 'Tyson Fury', 'Royaume-Uni', 35, 3),
(4, 'Naoya Inoue', 'Japon', 30, 4),
(5, 'Gervonta Davis', 'USA', 29, 5),
(6, 'Dmitry Bivol', 'Russie', 33, 6),
(7, 'Artur Beterbiev', 'Canada', 39, 7),
(8, 'Teofimo Lopez', 'USA', 26, 8);

-- --------------------------------------------------------

--
-- Structure de la table `matchs`
--

CREATE TABLE `matchs` (
  `id` int(11) NOT NULL,
  `boxeur1` varchar(100) NOT NULL,
  `boxeur2` varchar(100) NOT NULL,
  `date_match` date NOT NULL,
  `gagnant_id` int(11) DEFAULT NULL,
  `termine` tinyint(1) DEFAULT 0,
  `statut` enum('en_cours','termine') DEFAULT 'en_cours'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rounds`
--

CREATE TABLE `rounds` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `round_number` int(11) NOT NULL,
  `winner_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `stats`
--

CREATE TABLE `stats` (
  `id` int(11) NOT NULL,
  `boxeur_id` int(11) DEFAULT NULL,
  `victoires` int(11) DEFAULT 0,
  `defaites` int(11) DEFAULT 0,
  `KO` int(11) DEFAULT 0,
  `decision` int(11) DEFAULT 0,
  `abandon` int(11) DEFAULT 0,
  `TKO` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `stats`
--

INSERT INTO `stats` (`id`, `boxeur_id`, `victoires`, `defaites`, `KO`, `decision`, `abandon`, `TKO`) VALUES
(1, 3, 2, 3, 2, 0, 0, 0),
(2, 2, 3, 2, 3, 0, 0, 0),
(3, 7, 4, 1, 1, 2, 0, 1),
(4, 6, 2, 2, 1, 0, 0, 1),
(5, NULL, 0, 0, 0, 0, 0, 0),
(6, NULL, 0, 0, 0, 0, 0, 0),
(7, NULL, 0, 0, 0, 0, 0, 0),
(8, NULL, 0, 0, 0, 0, 0, 0),
(9, NULL, 0, 0, 0, 0, 0, 0),
(10, 1, 3, 1, 3, 0, 0, 0),
(11, 5, 4, 0, 3, 0, 0, 1),
(12, 8, 1, 2, 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `tournoi`
--

CREATE TABLE `tournoi` (
  `id` int(11) NOT NULL,
  `ronde` int(11) NOT NULL,
  `boxeur1_id` int(11) NOT NULL,
  `boxeur2_id` int(11) DEFAULT NULL,
  `gagnant_id` int(11) DEFAULT NULL,
  `termine` tinyint(1) DEFAULT 0,
  `nom` varchar(255) NOT NULL,
  `categorie` varchar(255) NOT NULL,
  `annee` int(11) NOT NULL,
  `arbitre_id` int(11) DEFAULT NULL,
  `date_combat` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tournoi`
--

INSERT INTO `tournoi` (`id`, `ronde`, `boxeur1_id`, `boxeur2_id`, `gagnant_id`, `termine`, `nom`, `categorie`, `annee`, `arbitre_id`, `date_combat`) VALUES
(479, 1, 1, 6, NULL, 0, '', '', 0, 1, '2025-03-04 16:10:00'),
(480, 1, 6, 7, NULL, 0, '', '', 0, NULL, '2025-03-04 16:10:45'),
(481, 1, 1, 2, NULL, 0, '', '', 0, NULL, '2025-03-04 16:10:45');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','arbitre') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, '0', '0', 'arbitre'),
(2, 'arbitre', 'passer', 'arbitre'),
(5, 'lamine', 'passer', 'admin');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `administrateurs`
--
ALTER TABLE `administrateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Index pour la table `arbitres`
--
ALTER TABLE `arbitres`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `boxeurs`
--
ALTER TABLE `boxeurs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `matchs`
--
ALTER TABLE `matchs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gagnant_id` (`gagnant_id`);

--
-- Index pour la table `rounds`
--
ALTER TABLE `rounds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `match_id` (`match_id`),
  ADD KEY `winner_id` (`winner_id`);

--
-- Index pour la table `stats`
--
ALTER TABLE `stats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `boxeur_id` (`boxeur_id`);

--
-- Index pour la table `tournoi`
--
ALTER TABLE `tournoi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `boxeur1_id` (`boxeur1_id`),
  ADD KEY `boxeur2_id` (`boxeur2_id`),
  ADD KEY `gagnant_id` (`gagnant_id`),
  ADD KEY `arbitre_id` (`arbitre_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `administrateurs`
--
ALTER TABLE `administrateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `arbitres`
--
ALTER TABLE `arbitres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `boxeurs`
--
ALTER TABLE `boxeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `matchs`
--
ALTER TABLE `matchs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `rounds`
--
ALTER TABLE `rounds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `stats`
--
ALTER TABLE `stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `tournoi`
--
ALTER TABLE `tournoi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=482;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `matchs`
--
ALTER TABLE `matchs`
  ADD CONSTRAINT `matchs_ibfk_1` FOREIGN KEY (`gagnant_id`) REFERENCES `boxeurs` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `rounds`
--
ALTER TABLE `rounds`
  ADD CONSTRAINT `rounds_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `tournoi` (`id`),
  ADD CONSTRAINT `rounds_ibfk_2` FOREIGN KEY (`winner_id`) REFERENCES `boxeurs` (`id`);

--
-- Contraintes pour la table `stats`
--
ALTER TABLE `stats`
  ADD CONSTRAINT `stats_ibfk_1` FOREIGN KEY (`boxeur_id`) REFERENCES `boxeurs` (`id`);

--
-- Contraintes pour la table `tournoi`
--
ALTER TABLE `tournoi`
  ADD CONSTRAINT `tournoi_ibfk_1` FOREIGN KEY (`boxeur1_id`) REFERENCES `boxeurs` (`id`),
  ADD CONSTRAINT `tournoi_ibfk_2` FOREIGN KEY (`boxeur2_id`) REFERENCES `boxeurs` (`id`),
  ADD CONSTRAINT `tournoi_ibfk_3` FOREIGN KEY (`gagnant_id`) REFERENCES `boxeurs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tournoi_ibfk_4` FOREIGN KEY (`arbitre_id`) REFERENCES `arbitres` (`id`) ON DELETE SET NULL;
