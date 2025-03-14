<?php

$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// Récupérer les matchs de finale non terminés
$query = "SELECT t.id, b1.nom AS boxeur1, b2.nom AS boxeur2, t.date_combat, t.boxeur1_id, t.boxeur2_id 
          FROM tournoi t
          JOIN boxeurs b1 ON t.boxeur1_id = b1.id
          JOIN boxeurs b2 ON t.boxeur2_id = b2.id
          WHERE t.type = 'finale' AND t.termine = 0
          ORDER BY t.date_combat ASC";
$result = $mysqli->query($query);

// Gérer la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['enregistrer_et_passer_gagnant'])) {
        enregistrerEtPasserAuGagnant($mysqli, $_POST['gagnants'], $_POST['methodes']);
    }
}

function enregistrerEtPasserAuGagnant($mysqli, $gagnants, $methodes) {
    // Marquer tous les matchs de finale comme terminés et enregistrer les gagnants
    foreach ($gagnants as $match_id => $gagnant_id) {
        $methode_victoire = $methodes[$match_id];
        $mysqli->query("UPDATE tournoi SET gagnant_id = $gagnant_id, termine = 1 WHERE id = $match_id");

        // Mettre à jour les statistiques du boxeur gagnant
        $update_stats_query = "UPDATE stats SET victoires = victoires + 1, $methode_victoire = $methode_victoire + 1 WHERE boxeur_id = $gagnant_id";
        $mysqli->query($update_stats_query);
    }

    // Rediriger vers la page du gagnant
    header("Location: gagnant.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finale</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <h3>Finale</h3>
        <nav>
            <a href="h.php" class="btn-home">Retour à l'Accueil</a>
        </nav>
    </header>
    
    <section>
        <h3>Matchs de Finale</h3>
        <form method="post">
            <?php 
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='match'>";
                    echo "<p><strong>" . $row['boxeur1'] . "</strong> VS <strong>" . $row['boxeur2'] . "</strong></p>";
                    echo "<label>Vainqueur :</label>";
                    echo "<select name='gagnants[" . $row['id'] . "]' required>";
                    echo "<option value=''>Sélectionner</option>";
                    echo "<option value='" . $row['boxeur1_id'] . "'>" . $row['boxeur1'] . "</option>";
                    echo "<option value='" . $row['boxeur2_id'] . "'>" . $row['boxeur2'] . "</option>";
                    echo "</select>";
                    echo "<label>Méthode de victoire :</label>";
                    echo "<select name='methodes[" . $row['id'] . "]' required>";
                    echo "<option value='KO'>KO</option>";
                    echo "<option value='TKO'>TKO</option>";
                    echo "<option value='decision'>Décision</option>";
                    echo "<option value='abandon'>Abandon</option>";
                    echo "</select>";
                    echo "</div><hr>";
                }
            } else {
                echo "<p>Aucun match de finale prévu.</p>";
            }
            ?>
            <button type="submit" name="enregistrer_et_passer_gagnant">Tout Enregistrer et Passer au Gagnant</button>
        </form>
    </section>
</body>
</html>

<?php
$mysqli->close();
?>
