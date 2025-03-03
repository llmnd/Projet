<?php

$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// Récupérer les boxeurs triés par classement
$query = "SELECT nom, age, pays, classement FROM boxeurs ORDER BY classement ASC";
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
            <a href="admin.php">Retour à l'Admin</a>
            <a href="logout.php">Se déconnecter</a>
        </nav>
    </header>

    <section>
        <h3>Classement des Boxeurs WBSS</h3>

        <?php 
        if ($result->num_rows > 0) {
            echo "<div class='classement-boxeurs-container'>";
            echo "<table>";
            echo "<tr><th>Classement</th><th>Nom</th><th>Âge</th><th>Pays</th></tr>";

            // Afficher les boxeurs
            $rank = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $rank . "</td>";
                echo "<td>" . $row['nom'] . "</td>";
                echo "<td>" . $row['age'] . "</td>";
                echo "<td>" . $row['pays'] . "</td>";
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
