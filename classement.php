<?php

$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// Réinitialiser le classement
$mysqli->query("UPDATE boxeurs SET classement = 0");

// Récupérer les boxeurs triés par victoires, KO, TKO, etc.
$query = "SELECT b.nom, b.age, b.pays, s.victoires, s.KO, s.TKO, s.decision, s.abandon 
          FROM boxeurs b
          LEFT JOIN stats s ON b.id = s.boxeur_id
          ORDER BY s.victoires DESC, s.KO DESC, s.TKO DESC, s.decision DESC, s.abandon DESC";
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
            echo "<tr><th>Classement</th><th>Nom</th><th>Âge</th><th>Pays</th><th>Victoires</th><th>KO</th><th>TKO</th><th>Décision</th><th>Abandon</th></tr>";

            // Afficher les boxeurs
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
                echo "<td>" . ($row['decision'] ?: 0) . "</td>";
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
$mysqli->close();
?>
