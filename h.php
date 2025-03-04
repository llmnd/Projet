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
    <link rel="stylesheet" href="histoire.css">
    <script src="h.js" defer></script>

</head>
<body>
    <header>
        <h3>Boxe</h3>
        <nav>
            <ul>
            <li><a href="#histoire">Histoire</a></li>
                <li><a href="#matchs">Matchs</a></li>
                <li><a href="classement.php">Classement</a></li>
                <li><a href="connexion.php">Connexion</a></li>
                <li><a href="stats.php">Stats</a></li>
            </ul>
        </nav>
    </header>
    
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
    
    <section id="histoire" class="histoire-boxe">
    <h2>L'Histoire de la Boxe</h2>
    <div class="histoire-container">
        <div class="histoire-content">
            <p><strong>Les Origines :</strong> La boxe est un des sports de combat les plus anciens, pratiqué depuis l'Antiquité. Les premières traces remontent à la Mésopotamie et à l'Égypte, mais c'est en Grèce que le pugilat est introduit aux Jeux Olympiques dès le VIIe siècle av. J.-C.</p>
            <p><strong>Le Pugilat Romain :</strong> Les Romains reprennent cette pratique en l’armant de cestus, des gants renforcés de métal. Cependant, avec la chute de Rome, la boxe disparaît en Europe.</p>
            <p><strong>La Renaissance en Angleterre :</strong> Elle refait surface au XVIIIe siècle en Angleterre, sous une forme plus réglementée. James Figg, premier champion officiel, popularise le sport.</p>
            <p><strong>Les Règles Modernes :</strong> En 1867, les règles du Marquis de Queensberry imposent les gants, des rounds limités et l’interdiction des coups bas, façonnant la boxe que nous connaissons aujourd’hui.</p>
            <p><strong>Le XXe siècle et les légendes :</strong> Des icônes comme Jack Johnson, Joe Louis, Muhammad Ali, Mike Tyson et Floyd Mayweather ont marqué l’histoire du noble art.</p>
            <p><strong>La Boxe Aujourd’hui :</strong> Ce sport est structuré autour de nombreuses fédérations comme la WBA, WBC et IBF, et des tournois prestigieux comme le WBSS (World Boxing Super Series) perpétuent la tradition du combat ultime.</p>
        </div>
    </div>
</section>

<section id="matchs">
    <h2>Matchs Passés</h2>
    <div class="matchs-container">
        <?php
        // Connexion à la base de données (ajuste selon ton paramétrage)
        $mysqli = new mysqli("localhost", "root", "", "WBSS");

        if ($mysqli->connect_error) {
            die('Erreur de connexion (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
        }

        // Récupérer les matchs passés (date match avant la date actuelle)
        $date_aujourdhui = date('Y-m-d');
        $query = "SELECT m.*, b1.nom AS boxeur1_nom, b2.nom AS boxeur2_nom FROM matchs m
                  JOIN boxeurs b1 ON m.boxeur1 = b1.id
                  JOIN boxeurs b2 ON m.boxeur2 = b2.id
                  WHERE m.date_match < '$date_aujourdhui'";

        $result = $mysqli->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='match'>";
                echo "<p><strong>" . $row['boxeur1_nom'] . "</strong> VS <strong>" . $row['boxeur2_nom'] . "</strong></p>";
                echo "<p>Date: " . $row['date_match'] . "</p>";
                if ($row['gagnant_id'] != NULL) {
                    // Afficher le gagnant si un gagnant est défini
                    $gagnant = $row['gagnant_id'] == $row['boxeur1'] ? $row['boxeur1_nom'] : $row['boxeur2_nom'];
                    echo "<p>Gagnant: " . $gagnant . "</p>";
                } else {
                    echo "<p>Match sans gagnant défini.</p>";
                }
                echo "</div>";
            }
        } else {
            echo "<p>Aucun match passé trouvé.</p>";
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

