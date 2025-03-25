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
        enregistrerEtPasserAuGagnant($mysqli, $_POST['rounds'], $_POST['methodes'], $_POST['winners']);
    }
}

function enregistrerEtPasserAuGagnant($mysqli, $rounds, $methodes, $winners) {
    foreach ($rounds as $match_id => $round_data) {
        $boxeur1_rounds = min($round_data['boxeur1'], 12); // Limit to 12 rounds
        $boxeur2_rounds = min($round_data['boxeur2'], 12); // Limit to 12 rounds
        $methode_victoire = $methodes[$match_id];
        $gagnant_id = $winners[$match_id]; // Explicitly selected winner

        // Determine the winner based on the method of victory
        if (in_array($methode_victoire, ['KO', 'TKO', 'abandon'])) {
            if (!$gagnant_id) {
                echo "<p style='color: red;'>Veuillez sélectionner le gagnant pour le match ID $match_id.</p>";
                continue; // Skip this match if no winner is selected
            }
        } elseif ($methode_victoire === 'decision') {
            // Compare rounds won to determine the winner
            if ($boxeur1_rounds > $boxeur2_rounds) {
                $gagnant_id = $round_data['boxeur1_id'];
            } elseif ($boxeur2_rounds > $boxeur1_rounds) {
                $gagnant_id = $round_data['boxeur2_id'];
            } else {
                echo "<p style='color: orange;'>Match nul : les deux boxeurs ont gagné le même nombre de rounds pour le match ID $match_id.</p>";
                continue; // Skip this match
            }
        } else {
            echo "<p style='color: red;'>Méthode de victoire non reconnue pour le match ID $match_id.</p>";
            continue; // Skip this match
        }

        $perdant_id = ($gagnant_id == $round_data['boxeur1_id']) ? $round_data['boxeur2_id'] : $round_data['boxeur1_id'];

        // Ensure the match exists in the `matchs` table
        $match_check = $mysqli->query("SELECT id FROM matchs WHERE id = $match_id");
        if ($match_check->num_rows == 0) {
            $mysqli->query("INSERT INTO matchs (id, boxeur1, boxeur2, date_match, termine) 
                            SELECT t.id, b1.nom, b2.nom, t.date_combat, t.termine 
                            FROM tournoi t
                            JOIN boxeurs b1 ON t.boxeur1_id = b1.id
                            JOIN boxeurs b2 ON t.boxeur2_id = b2.id
                            WHERE t.id = $match_id");
        }

        // Update the match as completed and record the winner
        $mysqli->query("UPDATE tournoi SET gagnant_id = $gagnant_id, termine = 1 WHERE id = $match_id");

        // Update individual statistics for the winner
        $mysqli->query("UPDATE boxeur_stats SET victoires = victoires + 1, $methode_victoire = $methode_victoire + 1 WHERE boxeur_id = $gagnant_id");

        // Update individual statistics for the loser
        $mysqli->query("UPDATE boxeur_stats SET defaites = defaites + 1 WHERE boxeur_id = $perdant_id");

        // Record match statistics
        $mysqli->query("INSERT INTO match_stats (match_id, boxeur1_score, boxeur2_score, method_victoire) 
                        VALUES ($match_id, $boxeur1_rounds, $boxeur2_rounds, '$methode_victoire')");
    }

    // Redirect to the winner page
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1e1e2f, #3a3a5f);
            color: #fff;
            margin: 0;
            padding: 0;
        }
        header {
            background: #4b4b7f;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        header h3 {
            margin: 0;
            font-size: 2rem;
            color: #fff;
        }
        header nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
            font-size: 1rem;
        }
        header nav a:hover {
            text-decoration: underline;
        }
        section {
            max-width: 800px;
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        section h3 {
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }
        .match {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .match p {
            margin: 0 0 10px;
            font-size: 1.1rem;
        }
        .match label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        .match input, .match select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background: #4b4b7f;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #3a3a5f;
        }
    </style>
</head>
<body>
    <header>
        <h3>Finale</h3>
        <nav>
            <a href="accueil.php" class="btn-home">Retour à l'Accueil</a>
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
                    echo "<input type='hidden' name='rounds[" . $row['id'] . "][boxeur1_id]' value='" . $row['boxeur1_id'] . "'>";
                    echo "<input type='hidden' name='rounds[" . $row['id'] . "][boxeur2_id]' value='" . $row['boxeur2_id'] . "'>";
                    echo "<label>Rounds gagnés par " . $row['boxeur1'] . " :</label>";
                    echo "<input type='number' name='rounds[" . $row['id'] . "][boxeur1]' min='0' max='12' required>";
                    echo "<label>Rounds gagnés par " . $row['boxeur2'] . " :</label>";
                    echo "<input type='number' name='rounds[" . $row['id'] . "][boxeur2]' min='0' max='12' required>";
                    echo "<label>Méthode de victoire :</label>";
                    echo "<select name='methodes[" . $row['id'] . "]' required>";
                    echo "<option value='KO'>KO</option>";
                    echo "<option value='TKO'>TKO</option>";
                    echo "<option value='decision'>Décision</option>";
                    echo "<option value='abandon'>Abandon</option>";
                    echo "</select>";
                    echo "<label>Gagnant :</label>";
                    echo "<select name='winners[" . $row['id'] . "]'>";
                    echo "<option value=''>Sélectionner</option>";
                    echo "<option value='" . $row['boxeur1_id'] . "'>" . $row['boxeur1'] . "</option>";
                    echo "<option value='" . $row['boxeur2_id'] . "'>" . $row['boxeur2'] . "</option>";
                    echo "</select>";
                    echo "</div>";
                }
            } else {
                echo "<p style='text-align: center;'>Aucun match de finale prévu.</p>";
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
