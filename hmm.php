<?php

$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// Récupérer les matchs non terminés
$query = "SELECT t.id, b1.nom AS boxeur1, b2.nom AS boxeur2, t.date_combat, t.boxeur1_id, t.boxeur2_id 
          FROM tournoi t
          JOIN boxeurs b1 ON t.boxeur1_id = b1.id
          JOIN boxeurs b2 ON t.boxeur2_id = b2.id
          WHERE t.termine = 0
          ORDER BY t.date_combat ASC";
$result = $mysqli->query($query);

// Gérer la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $match_id = $_POST['match_id'];
    $gagnant_id = $_POST['gagnant_id'];  // Corrected to only the winner's ID
    $methode_victoire = $_POST['methode_victoire'];
    $date_combat = $_POST['date_combat'];

    $update_query = "UPDATE tournoi SET gagnant_id = ?, termine = 1, date_combat = ?, arbitre_id = ? WHERE id = ?";
    $stmt = $mysqli->prepare($update_query);
    $stmt->bind_param("isii", $gagnant_id, $date_combat, $_SESSION['arbitre_id'], $match_id);
    $stmt->execute();
    $stmt->close();
    
    echo "<p>Résultat enregistré avec succès !</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arbitre - Validation des Matchs</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <h3>Validation des Matchs</h3>
        <nav>
            <a href="logout.php">Se déconnecter</a>
        </nav>
    </header>
    
    <section>
        <h3>Matchs en attente</h3>
        <?php 
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<form method='POST'>";
                echo "<p><strong>" . $row['boxeur1'] . "</strong> VS <strong>" . $row['boxeur2'] . "</strong></p>";
                echo "<label>Vainqueur :</label>";
                echo "<select name='gagnant_id' required>";
                echo "<option value=''>Sélectionner</option>";
                echo "<option value='" . $row['boxeur1_id'] . "'>" . $row['boxeur1'] . "</option>";
                echo "<option value='" . $row['boxeur2_id'] . "'>" . $row['boxeur2'] . "</option>";
                echo "</select>";
                echo "<label>Méthode de victoire :</label>";
                echo "<select name='methode_victoire' required>";
                echo "<option value='KO'>KO</option>";
                echo "<option value='TKO'>TKO</option>";
                echo "<option value='Décision'>Décision</option>";
                echo "<option value='Abandon'>Abandon</option>";
                echo "</select>";
                echo "<label>Date et heure :</label>";
                echo "<input type='datetime-local' name='date_combat' required>";
                echo "<input type='hidden' name='match_id' value='" . $row['id'] . "'>";
                echo "<button type='submit'>Enregistrer</button>";
                echo "</form><hr>";
            }
        } else {
            echo "<p>Aucun match en attente.</p>";
        }
        ?>
    </section>
    
</body>
</html>

<?php
$mysqli->close();
?>
