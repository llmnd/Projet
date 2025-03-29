<?php

$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// Récupérer les statistiques de tous les matchs du tournoi
$query = "SELECT t.id, t.boxeur1_id, t.boxeur2_id, b1.nom AS boxeur1, b2.nom AS boxeur2, t.date_combat, 
                 t.gagnant_id, bs1.victoires AS victoires_b1, bs1.defaites AS defaites_b1, 
                 bs2.victoires AS victoires_b2, bs2.defaites AS defaites_b2
          FROM tournoi t
          JOIN boxeurs b1 ON t.boxeur1_id = b1.id
          JOIN boxeurs b2 ON t.boxeur2_id = b2.id
          LEFT JOIN boxeur_stats bs1 ON b1.id = bs1.boxeur_id
          LEFT JOIN boxeur_stats bs2 ON b2.id = bs2.boxeur_id
          ORDER BY t.date_combat ASC"; // Ensure no filtering is applied
$result = $mysqli->query($query);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Matchs</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <h3>Statistiques des Matchs</h3>
        <nav>
            <a href="logout.php">Se déconnecter</a>
            <a href="accueil.php" class="btn-home">Retour à l'Accueil</a>
        </nav>
    </header>
    
    <section>
        <h3>Statistiques de Tous les Matchs du Tournoi</h3>
        <p>Voici les statistiques complètes de tous les matchs ayant eu lieu pendant le tournoi.</p>
        <?php 
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Match ID</th>";
            echo "<th>Boxeur 1</th>";
            echo "<th>Boxeur 2</th>";
            echo "<th>Date</th>";
            echo "<th>Gagnant</th>";
            echo "<th>Stats Boxeur 1</th>";
            echo "<th>Stats Boxeur 2</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            while ($row = $result->fetch_assoc()) {
                // Determine le gagnant pour chaque match
                $gagnant = is_null($row['gagnant_id']) ? "Pas encore déterminé" : (($row['gagnant_id'] == $row['boxeur1_id']) ? $row['boxeur1'] : $row['boxeur2']);
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['boxeur1'] . "</td>";
                echo "<td>" . $row['boxeur2'] . "</td>";
                echo "<td style='color: white;'>" . $row['date_combat'] . "</td>";
                echo "<td>" . $gagnant . "</td>";
                echo "<td>Victoires: " . $row['victoires_b1'] . ", Défaites: " . $row['defaites_b1'] . "</td>";
                echo "<td>Victoires: " . $row['victoires_b2'] . ", Défaites: " . $row['defaites_b2'] . "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p>Aucun match trouvé.</p>";
        }
        ?>
    </section>
</body>
</html>

<?php
$mysqli->close();
?>
