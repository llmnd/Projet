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
    if (isset($_POST['enregistrer_et_passer_demi_finale'])) {
        enregistrerEtPasserAuxDemiFinales($mysqli, $_POST['gagnants'], $_POST['methodes']);
    }
}

function enregistrerEtPasserAuxDemiFinales($mysqli, $gagnants, $methodes) {
    foreach ($gagnants as $match_id => $gagnant_id) {
        $methode_victoire = $methodes[$match_id];

        // Update the match as completed and record the winner
        $mysqli->query("UPDATE tournoi SET gagnant_id = $gagnant_id, termine = 1 WHERE id = $match_id");

        // Retrieve the IDs of both boxers in the match
        $result = $mysqli->query("SELECT boxeur1_id, boxeur2_id FROM tournoi WHERE id = $match_id");
        $match = $result->fetch_assoc();
        $boxeur1_id = $match['boxeur1_id'];
        $boxeur2_id = $match['boxeur2_id'];

        // Determine the loser
        $perdant_id = ($gagnant_id == $boxeur1_id) ? $boxeur2_id : $boxeur1_id;

        // Update individual statistics for the winner
        $mysqli->query("UPDATE boxeur_stats SET victoires = victoires + 1, $methode_victoire = $methode_victoire + 1 WHERE boxeur_id = $gagnant_id");

        // Update individual statistics for the loser
        $mysqli->query("UPDATE boxeur_stats SET defaites = defaites + 1 WHERE boxeur_id = $perdant_id");

        // Ensure the match exists in the `matchs` table
        $match_check = $mysqli->query("SELECT id FROM matchs WHERE id = $match_id");
        if ($match_check->num_rows > 0) {
            // Record match statistics
            $mysqli->query("INSERT INTO match_stats (match_id, boxeur1_score, boxeur2_score, method_victoire) 
                            VALUES ($match_id, 
                                    IF($gagnant_id = $boxeur1_id, 1, 0), 
                                    IF($gagnant_id = $boxeur2_id, 1, 0), 
                                    '$methode_victoire')");
        } else {
            echo "<p style='color: red;'>Le match avec l'ID $match_id n'existe pas dans la table `matchs`.</p>";
        }
    }

    // Récupérer les gagnants des matchs de round
    $result = $mysqli->query("SELECT gagnant_id FROM tournoi WHERE type = 'round' AND termine = 1");
    $gagnants = [];
    while ($row = $result->fetch_assoc()) {
        $gagnants[] = $row['gagnant_id'];
    }

    // Vérifier qu'il y a exactement 4 gagnants
    if (count($gagnants) != 4) {
        echo "<p style='color: red;'>Il faut exactement 4 gagnants pour passer aux demi-finales. Actuellement, il y a " . count($gagnants) . " gagnants.</p>";
        return;
    }

    // Ajouter les matchs de demi-finale
    $boxeurs_utilises = [];
    for ($i = 0; $i < count($gagnants) - 1; $i += 2) {
        $boxeur1_id = $gagnants[$i];
        $boxeur2_id = $gagnants[$i + 1];

        // Vérifier si les boxeurs sont différents et non utilisés
        if ($boxeur1_id == $boxeur2_id || in_array($boxeur1_id, $boxeurs_utilises) || in_array($boxeur2_id, $boxeurs_utilises)) {
            continue;
        }

        $date_combat = date('Y-m-d H:i:s');
        $stmt = $mysqli->prepare("INSERT INTO tournoi (ronde, boxeur1_id, boxeur2_id, date_combat, type) VALUES (2, ?, ?, ?, 'semi_finale')");
        $stmt->bind_param("iis", $boxeur1_id, $boxeur2_id, $date_combat);
        $stmt->execute();

        // Marquer les boxeurs comme utilisés
        $boxeurs_utilises[] = $boxeur1_id;
        $boxeurs_utilises[] = $boxeur2_id;
    }

    // Supprimer les matchs de round
    $mysqli->query("DELETE FROM tournoi WHERE type = 'round'");

    // Rediriger vers la page des demi-finales
    header("Location: demi_finale.php");
    exit();
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
            <a href="accueil.php" class="btn-home">Retour à l'Accueil</a>
        </nav>
    </header>
    
    <section>
        <h3>Matchs en attente</h3>
        <form method="post">
            <?php 
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='match'>";
                    echo "<p><strong>" . $row['boxeur1'] . "</strong> VS <strong>" . $row['boxeur2'] . "</strong></p>";
                    echo "<input type='hidden' name='boxeur1_id[" . $row['id'] . "]' value='" . $row['boxeur1_id'] . "'>";
                    echo "<input type='hidden' name='boxeur2_id[" . $row['id'] . "]' value='" . $row['boxeur2_id'] . "'>";
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
                echo "<p>Aucun match en attente.</p>";
            }
            ?>
            <button type="submit" name="enregistrer_et_passer_demi_finale">Tout Enregistrer et Passer aux Demi-finales</button>
        </form>
    </section>
</body>
</html>

<?php
$mysqli->close();
?>
