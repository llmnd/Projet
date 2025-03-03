<?php

$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// Récupérer les matchs organisés depuis la table tournoi
$query = "SELECT t.id, b1.nom AS boxeur1, b2.nom AS boxeur2, t.date_combat 
          FROM tournoi t
          JOIN boxeurs b1 ON t.boxeur1_id = b1.id
          JOIN boxeurs b2 ON t.boxeur2_id = b2.id
          WHERE t.termine = 0
          ORDER BY t.date_combat ASC";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournoi de Boxe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Tournoi de Boxe</h1>
        <nav>
            <ul>
                <li><a href="#matchs">Matchs</a></li>
                <li><a href="classement.php">Classement</a></li>
                <li><a href="connexion.php">Connexion</a></li>
            </ul>
        </nav>
    </header>
    
    <section id="videos" class="hero">
        <video autoplay loop controls>
            <source src="h2.mp4" type="video/mp4">
            Votre navigateur ne supporte pas la vidéo.
        </video>
        <div class="overlay">
            <h2>Vivez l'intensité des combats</h2>
        </div>
    </section>
    
    <section id="matchs">
        <h2>Prochains Matchs</h2>
        <div class="matchs-container">
            <?php 
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='match'>";
                    echo "<p><strong>" . $row['boxeur1'] . "</strong> VS <strong>" . $row['boxeur2'] . "</strong></p>";
                    echo "<p>Date: " . $row['date_combat'] . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>Aucun match prévu.</p>";
            }
            ?>
        </div>
    </section>
    
    <footer>
        <p>&copy; 2025 Tournoi de Boxe - Tous droits réservés</p>
    </footer>
</body>
</html>

<?php
$mysqli->close();
?>
