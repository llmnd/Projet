<?php

$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}


// Récupérer les matchs organisés depuis la table matches

$query = "SELECT * from matches
          ORDER BY date_combat ASC";
$result = $mysqli->query($query);



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
                <li><a href="histoire.html">Histoire</a></li>
                <li><a href="#matchs">Matchs</a></li>
                <li><a href="classement.php">Classement</a></li>
                <li><a href="connexion.php">Connexion</a></li>
                <li><a href="stats.php">Stats</a></li>
            </ul>
        </nav>
    </header>
    
    
    <section id="matchs">
        <h2> Matchs </h2>
        <div class="matchs-container">
            <?php 
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='match'>";
                    echo "<p><strong>" . $row['boxeur1'] . "</strong> VS <strong>" . $row['boxeur2'] . "</strong></p>";
                    $date_combat = $row['date_combat'] ? date("d-m-Y", strtotime($row['date_combat'])) : 'Date non disponible';
echo "<p class='combat-date'>Date: " . $date_combat . "</p>";

                    echo "<p>Gagnant: " . $row['vainqueur'] . "</p>";
                    echo "</div>"; // Maintenant, chaque match a bien son propre div
                }
                
                }
            else {
                echo "<p>Aucun match prévu.</p>";
            }
            ?>
        </div>
    </section>
    
    
    
</body>
</html>

<?php
$mysqli->close();

?>

