<?php

$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// Récupérer les matchs de demi finale non terminés
$query = "SELECT t.id, b1.nom AS boxeur1, b2.nom AS boxeur2, t.date_combat, t.boxeur1_id, t.boxeur2_id 
          FROM tournoi t
          JOIN boxeurs b1 ON t.boxeur1_id = b1.id
          JOIN boxeurs b2 ON t.boxeur2_id = b2.id
          WHERE t.type = 'semi_finale' AND t.termine = 0
          ORDER BY t.date_combat ASC";
$result = $mysqli->query($query);

// Gérer la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['enregistrer_et_passer_finale'])) {
        // Check if required POST data is set
        if (!isset($_POST['gagnants']) || !isset($_POST['methodes'])) {
            echo "<p style='color: red; text-align: center;'>Aucun match à traiter. Veuillez vérifier les données soumises.</p>";
        } else {
            enregistrerEtPasserAuxFinales($mysqli, $_POST['gagnants'], $_POST['methodes']);
        }
    }
}

function enregistrerEtPasserAuxFinales($mysqli, $gagnants, $methodes) {
    if (empty($gagnants) || empty($methodes)) {
        echo "<p style='color: red; text-align: center;'>Aucun match à traiter. Veuillez vérifier les données soumises.</p>";
        return;
    }

    foreach ($gagnants as $match_id => $gagnant_id) {
        if (empty($gagnant_id) || !isset($methodes[$match_id])) {
            echo "<p style='color: red;'>Erreur : Données manquantes pour le match ID $match_id. Veuillez vérifier les gagnants et les méthodes de victoire.</p>";
            return;
        }

        $methode_victoire = $methodes[$match_id];
        $mysqli->query("UPDATE tournoi SET gagnant_id = $gagnant_id, termine = 1 WHERE id = $match_id");

        // Récupérer l'ID du perdant
        $result = $mysqli->query("SELECT boxeur1_id, boxeur2_id FROM tournoi WHERE id = $match_id");
        $row = $result->fetch_assoc();
        if (!$row) {
            echo "<p style='color: red;'>Erreur : Impossible de récupérer les données pour le match ID $match_id.</p>";
            return;
        }

        $perdant_id = ($row['boxeur1_id'] == $gagnant_id) ? $row['boxeur2_id'] : $row['boxeur1_id'];
        if (empty($perdant_id)) {
            echo "<p style='color: red;'>Erreur : Données manquantes pour le perdant du match ID $match_id.</p>";
            return;
        }

        // Mettre à jour les statistiques du boxeur gagnant
        $update_winner_stats_query = "UPDATE boxeur_stats SET victoires = victoires + 1, $methode_victoire = $methode_victoire + 1 WHERE boxeur_id = $gagnant_id";
        $mysqli->query($update_winner_stats_query);

        // Mettre à jour les statistiques du boxeur perdant
        $update_loser_stats_query = "UPDATE boxeur_stats SET defaites = defaites + 1 WHERE boxeur_id = $perdant_id";
        $mysqli->query($update_loser_stats_query);
    }

    // Récupérer les gagnants des matchs de demi-finale
    $result = $mysqli->query("SELECT gagnant_id FROM tournoi WHERE type = 'semi_finale' AND termine = 1");
    $gagnants = [];
    while ($row = $result->fetch_assoc()) {
        $gagnants[] = $row['gagnant_id'];
    }

    if (count($gagnants) < 2) {
        echo "<p style='color: red;'>Il faut au moins 2 gagnants pour passer à la finale.</p>";
        return;
    }

    // Ajouter les matchs de finale
    $boxeurs_utilises = [];
    for ($i = 0; $i < count($gagnants) - 1; $i += 2) {
        $boxeur1_id = $gagnants[$i];
        $boxeur2_id = $gagnants[$i + 1];

        // Vérifier si les boxeurs sont différents et non utilisés
        if ($boxeur1_id == $boxeur2_id || in_array($boxeur1_id, $boxeurs_utilises) || in_array($boxeur2_id, $boxeurs_utilises)) {
            continue;
        }

        $date_combat = date('Y-m-d H:i:s');
        $stmt = $mysqli->prepare("INSERT INTO tournoi (ronde, boxeur1_id, boxeur2_id, date_combat, type) VALUES (3, ?, ?, ?, 'finale')");
        $stmt->bind_param("iis", $boxeur1_id, $boxeur2_id, $date_combat);
        $stmt->execute();

        // Marquer les boxeurs comme utilisés
        $boxeurs_utilises[] = $boxeur1_id;
        $boxeurs_utilises[] = $boxeur2_id;
    }

    // Supprimer les matchs de demi-finale
    $mysqli->query("DELETE FROM tournoi WHERE type = 'semi_finale'");

    // Rediriger vers la page de la finale
    header("Location: finale.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demi-finales</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <h3>Demi-finales</h3>
        <nav>
            <a href="accueil.php" class="btn-home">Retour à l'Accueil</a>
        </nav>
    </header>
    
    <section>
        <h3>Matchs de Demi-finales</h3>
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
                echo "<p>Aucun match de demi-finale prévu.</p>";
            }
            ?>
            <button type="submit" name="enregistrer_et_passer_finale">Tout Enregistrer et Passer à la Finale</button>
        </form>
    </section>
</body>
</html>

<?php
$mysqli->close();
?>
