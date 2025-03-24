<?php
// Connexion à la base de données
$mysqli = new mysqli("localhost", "root", "", "WBSS");

// Vérifier si la connexion a échoué
if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// Réinitialiser le classement des boxeurs
$mysqli->query("UPDATE boxeurs SET classement = 0");

// Récupérer les matchs du tournoi
$query = "SELECT m.id, m.boxeur1, m.boxeur2, m.vainqueur, m.mode_victoire, s.victoires, s.KO, s.TKO, s.abandon 
          FROM matches m
          LEFT JOIN stats s ON m.vainqueur = s.nom 
          ORDER BY m.id ASC"; 
$result = $mysqli->query($query);

// Organiser les statistiques des boxeurs
while ($row = $result->fetch_assoc()) {
    $vainqueur = $row['vainqueur'];
    $mode_victoire = $row['mode_victoire'];

    // Mettre à jour les statistiques du vainqueur
    if ($vainqueur) {
        $update_query = "UPDATE stats SET victoires = victoires + 1";
        
        if ($mode_victoire == 'KO') {
            $update_query .= ", KO = KO + 1";
        } elseif ($mode_victoire == 'TKO') {
            $update_query .= ", TKO = TKO + 1";
        } elseif ($mode_victoire == 'Abandon') {
            $update_query .= ", abandon = abandon + 1";
        }

        $update_query .= " WHERE nom = ?";
        
        $stmt = $mysqli->prepare($update_query);
        $stmt->bind_param("s", $vainqueur);
        $stmt->execute();
    }
}

// Récupérer tous les boxeurs triés par statistiques
$query = "SELECT b.nom, b.age, b.pays, s.victoires, s.KO, s.TKO, s.abandon 
          FROM boxeurs b
          LEFT JOIN stats s ON b.nom = s.nom
          ORDER BY s.victoires DESC, s.KO DESC, s.TKO DESC, s.abandon DESC";
$result = $mysqli->query($query);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Classement des Boxeurs</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <h3>Classement des Boxeurs</h3>
        <nav>
            <a href="accueil.php">Retour</a>
        </nav>
    </header>

    <section>
        <h3>Classement des Boxeurs WBSS</h3>

        <?php 
        if ($result->num_rows > 0) {
            echo "<div class='classement-boxeurs-container'>";
            echo "<table>";
            echo "<tr><th>Classement</th><th>Nom</th><th>Âge</th><th>Pays</th><th>Victoires</th><th>KO</th><th>TKO</th><th>Abandon</th></tr>";

            // Afficher les boxeurs dans le classement
            $rank = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $rank . "</td>";
                echo "<td>" . $row['nom'] . "</td>";
                echo "<td>" . $row['age'] . "</td>";
                echo "<td>" . $row['pays'] . "</td>";
                echo "<td>" . ($row['victoires'] ?: 0) . "</td>";
                echo "<td>" . ($row['KO'] ?: 0) . "</td>";
                echo "<td>" . ($row['TKO'] ?: 0) . "</td>";
                echo "<td>" . ($row['abandon'] ?: 0) . "</td>";
                echo "</tr>";
                $rank++;
            }

            echo "</table>";
            echo "</div>"; // Fin de la div .classement-boxeurs-container
        } else {
            echo "<p>Aucun boxeur trouvé.</p>";
        }
        ?>
    </section>
</body>
</html>

<?php
// Fermer la connexion à la base de données
$mysqli->close();
?>
