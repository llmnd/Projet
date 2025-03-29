<?php

$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// Récupérer les matchs non passés et sans gagnant
$query = "SELECT t.id, b1.nom AS boxeur1, b2.nom AS boxeur2, t.date_combat, t.gagnant_id 
          FROM tournoi t
          JOIN boxeurs b1 ON t.boxeur1_id = b1.id
          JOIN boxeurs b2 ON t.boxeur2_id = b2.id
          WHERE t.date_combat >= CURDATE() AND t.gagnant_id IS NULL
          ORDER BY t.date_combat ASC";
$result = $mysqli->query($query);

// Vérifier si tous les matchs sont terminés pour déterminer le gagnant
$winner_query = "SELECT b.nom AS gagnant 
                 FROM tournoi t
                 JOIN boxeurs b ON t.gagnant_id = b.id
                 WHERE t.gagnant_id IS NOT NULL
                 GROUP BY t.gagnant_id
                 ORDER BY COUNT(t.gagnant_id) DESC
                 LIMIT 1";
$winner_result = $mysqli->query($winner_query);
$winner = $winner_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournoi de Boxe</title>
    <link rel="stylesheet" href="accueil.css">
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
                <li><a href="stats_match.php">Statsmatch</a></li>
            </ul>
        </nav>
    </header>
    
    <section id="matchs">
        <div class="matchs-container">
            <?php 
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='match'>";
                    echo "<p><strong>" . $row['boxeur1'] . "</strong> VS <strong>" . $row['boxeur2'] . "</strong></p>";
                    echo "<p class='combat-date' style='color: orange;'>Date: " . $row['date_combat'] . "</p>"; // Updated style
                    echo "</div>";
                }
            } else {
                echo "<p>Aucun match prévu.</p>";
            }

            if ($winner) {
                echo "<p><strong>Le gagnant du tournoi est : " . $winner['gagnant'] . "</strong></p>";
            }
            ?>
        </div>
    </section>
    
    <section id="histoire" class="histoire-boxe">
        <h2>Histoire</h2>
        <div class="histoire-container">
            <div class="histoire-content">
                <h3>Histoire</h3>
                <img src="b6.jpg" alt="photo" class="round-image"> <!-- Add class "round-image" -->
               
                <br>
                
                <div class="matchs-container">
                   
                </div>

                <h3>Légende</h3>
                <p>Muhammad Ali, né Cassius Marcellus Clay Jr., était un boxeur professionnel américain et une icône culturelle.  
                Il est largement considéré comme l'un des plus grands boxeurs de tous les temps.  
                Ali était connu pour ses coups puissants, son jeu de jambes rapide et sa personnalité charismatique.  
                Il a remporté trois fois le championnat du monde des poids lourds et une médaille d'or olympique.  
                En dehors du ring, Ali était un fervent défenseur des droits civiques et de la justice sociale.  
                Sa célèbre citation, **« Flotte comme un papillon, pique comme une abeille »**, résume son style unique et sa confiance en lui.  
                L'héritage d'Ali continue d'inspirer les athlètes et les militants du monde entier.</p>
                <div class="stats-container">
                    <!-- Content for stats can be added here -->
                </div>
            </div>
        </div>
    </section>
    
</body>
</html>

<?php
$mysqli->close();
?>

