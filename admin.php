<?php

$mysqli = new mysqli("localhost", "root", "", "WBSS");
if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// Vérifier si un gagnant du tournoi existe
$winner_query = "SELECT b.nom AS gagnant 
                 FROM tournoi t
                 JOIN boxeurs b ON t.gagnant_id = b.id
                 WHERE t.gagnant_id IS NOT NULL
                 GROUP BY t.gagnant_id
                 ORDER BY COUNT(t.gagnant_id) DESC
                 LIMIT 1";
$winner_result = $mysqli->query($winner_query);
$winner = $winner_result->fetch_assoc();

// Initialiser un tournoi
function initialiserTournoi($mysqli) {
    $result = $mysqli->query("SELECT id FROM boxeurs");
    if ($result->num_rows < 2) {
        echo "<p style='color: red;'>Il faut au moins 2 boxeurs.</p>";
        return;
    }

    $boxeurs = [];
    while ($row = $result->fetch_assoc()) {
        $boxeurs[] = $row['id'];
    }
    shuffle($boxeurs);

    // Vider la table tournoi et réinitialiser l'auto-incrémentation
    if (!$mysqli->query("TRUNCATE TABLE tournoi")) {
        echo "<p style='color: red;'>Erreur lors de la suppression des anciens matchs.</p>";
        return;
    }

    // Ajouter les matchs de round
    $boxeurs_utilises = [];
    for ($i = 0; $i < count($boxeurs) - 1; $i += 2) {
        $boxeur1_id = $boxeurs[$i];
        $boxeur2_id = $boxeurs[$i + 1];

        // Vérifier si les boxeurs sont différents et non utilisés
        if ($boxeur1_id == $boxeur2_id || in_array($boxeur1_id, $boxeurs_utilises) || in_array($boxeur2_id, $boxeurs_utilises)) {
            continue;
        }

        // Insérer un match de round avec une date par défaut
        $date_combat = date('Y-m-d H:i:s');
        $stmt = $mysqli->prepare("INSERT INTO tournoi (boxeur1_id, boxeur2_id, date_combat, type) VALUES (?, ?, ?, 'round')");
        if (!$stmt) {
            echo "<p style='color: red;'>Erreur lors de la préparation de la requête d'insertion.</p>";
            return;
        }
        $stmt->bind_param("iis", $boxeur1_id, $boxeur2_id, $date_combat);
        if (!$stmt->execute()) {
            echo "<p style='color: red;'>Erreur lors de l'exécution de la requête d'insertion.</p>";
            return;
        }

        // Marquer les boxeurs comme utilisés
        $boxeurs_utilises[] = $boxeur1_id;
        $boxeurs_utilises[] = $boxeur2_id;
    }

    echo "<p style='color: green;'>Tournoi initialisé !</p>";
}

function reinitialiserStats($mysqli) {
    // Reset statistics in the `boxeur_stats` table
    if (!$mysqli->query("UPDATE boxeur_stats SET victoires = 0, defaites = 0, KO = 0, TKO = 0, abandon = 0")) {
        echo "<p style='color: red;'>Erreur lors de la réinitialisation des statistiques.</p>";
        return;
    }
    echo "<p style='color: green;'>Statistiques réinitialisées !</p>";
}

function passerAuxDemiFinales($mysqli) {
    // Récupérer les gagnants des matchs de round
    $result = $mysqli->query("SELECT gagnant_id FROM tournoi WHERE type = 'round' AND termine = 1");
    $gagnants = [];
    while ($row = $result->fetch_assoc()) {
        $gagnants[] = $row['gagnant_id'];
    }

    if (count($gagnants) < 2) {
        echo "<p style='color: red;'>Il faut au moins 2 gagnants pour passer aux demi-finales.</p>";
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
        $stmt = $mysqli->prepare("INSERT INTO tournoi (boxeur1_id, boxeur2_id, date_combat, type) VALUES (?, ?, ?, 'semi_finale')");
        $stmt->bind_param("iis", $boxeur1_id, $boxeur2_id, $date_combat);
        $stmt->execute();

        // Marquer les boxeurs comme utilisés
        $boxeurs_utilises[] = $boxeur1_id;
        $boxeurs_utilises[] = $boxeur2_id;
    }

    // Supprimer les matchs de round
    $mysqli->query("DELETE FROM tournoi WHERE type = 'round'");
}

function enregistrerTousLesMatchs($mysqli) {
    // Marquer tous les matchs de round comme terminés
    $mysqli->query("UPDATE tournoi SET termine = 1 WHERE type = 'round'");

    echo "<p style='color: green;'>Tous les matchs ont été enregistrés !</p>";
}

function enregistrerEtPasserAuxDemiFinales($mysqli) {
    // Marquer tous les matchs de round comme terminés
    $mysqli->query("UPDATE tournoi SET termine = 1 WHERE type = 'round'");

    // Passer aux demi-finales
    passerAuxDemiFinales($mysqli);
}

function toutSupprimer($mysqli) {
    // Supprimer tous les matchs
    if (!$mysqli->query("TRUNCATE TABLE tournoi")) {
        echo "<p style='color: red;'>Erreur lors de la suppression des matchs.</p>";
        return;
    }

    echo "<p style='color: green;'>Tous les matchs ont été supprimés !</p>";
}

// Vérifier si l'administrateur veut initialiser le tournoi
if (isset($_POST['initialiser_tournoi'])) {
    initialiserTournoi($mysqli);
    reinitialiserStats($mysqli);
}


// Vérifier si l'administrateur veut tout supprimer
if (isset($_POST['tout_supprimer'])) {
    toutSupprimer($mysqli);
}

// Modifier les boxeurs, arbitre et date du combat
if (isset($_POST['modifier_combat'])) {
    $match_id = $_POST['match_id'];
    $boxeur1_id = $_POST['boxeur1_id'];
    $boxeur2_id = $_POST['boxeur2_id'];
    $arbitre_id = $_POST['arbitre_id'];
    $date_combat = $_POST['date_combat'];

    // Mise à jour des boxeurs, arbitre et date
    $stmt = $mysqli->prepare("UPDATE tournoi SET boxeur1_id = ?, boxeur2_id = ?, arbitre_id = ?, date_combat = ? WHERE id = ?");
    $stmt->bind_param("iiisi", $boxeur1_id, $boxeur2_id, $arbitre_id, $date_combat, $match_id);
    if ($stmt->execute()) {
        echo "<p>Combat mis à jour avec succès !</p>";
    } else {
        echo "<p>Erreur lors de la mise à jour du combat.</p>";
    }
}

// Supprimer un match
if (isset($_POST['supprimer_match'])) {
    $match_id = $_POST['match_id'];
    $stmt = $mysqli->prepare("DELETE FROM tournoi WHERE id = ?");
    $stmt->bind_param("i", $match_id);
    $stmt->execute();
    echo "<p>Match supprimé !</p>";
}

// Vérifier s'il y a des matchs existants
$matchs = $mysqli->query("SELECT t.id, b1.nom AS boxeur1, b2.nom AS boxeur2, a.username AS arbitre, t.date_combat, t.boxeur1_id, t.boxeur2_id, t.arbitre_id, t.type FROM tournoi t JOIN boxeurs b1 ON t.boxeur1_id = b1.id JOIN boxeurs b2 ON t.boxeur2_id = b2.id LEFT JOIN arbitres a ON t.arbitre_id = a.id ORDER BY t.date_combat");

// Liste des boxeurs et arbitres
$boxeurs = $mysqli->query("SELECT id, nom FROM boxeurs");
$arbitres = $mysqli->query("SELECT id, username FROM arbitres");

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - WBSS</title>
    <link rel="stylesheet" href="admin.css">
    <a href="logout.php" class="btn-home">Se déconnecter</a>
    <a href="accueil.php" class="btn-home">Retour à l'Accueil</a>
</head>
<body>
    <h1>Gestion du Tournoi</h1>
    
    <?php
    if ($winner) {
        // Afficher un message si un gagnant du tournoi existe
        echo "<p style='color: green;'>Le gagnant du tournoi est : <strong>" . $winner['gagnant'] . "</strong></p>";
        echo "<p>Tous les matchs ont été terminés. Veuillez initialiser un nouveau tournoi pour afficher les matchs.</p>";
        echo "<form method='post'>
                <button type='submit' name='initialiser_tournoi'>Initialiser Tournoi</button>
              </form>";
    } else {
        // Vérification si des matchs existent, sinon afficher un bouton pour initialiser
        if ($matchs->num_rows == 0) {
            echo "<p style='color: red;'>Il n'y a pas de matchs disponibles. Veuillez initialiser le tournoi.</p>";
            echo "<form method='post'>
                    <button type='submit' name='initialiser_tournoi'>Initialiser Tournoi</button>
                  </form>";
        } else {
            echo "<h2>Matchs en cours</h2>";
            echo "<table>";
            echo "<tr><th>Boxeur 1</th><th>Boxeur 2</th><th>Arbitre</th><th>Date du Combat</th><th>Type</th><th>Action</th></tr>";
            while ($row = $matchs->fetch_assoc()) {
                // Ne pas afficher les matchs avec les mêmes boxeurs
                if ($row['boxeur1_id'] == $row['boxeur2_id']) {
                    continue;
                }
                echo "<tr>";
                echo "<td>" . $row['boxeur1'] . "</td>";
                echo "<td>" . $row['boxeur2'] . "</td>";
                echo "<td>" . ($row['arbitre'] ? $row['arbitre'] : 'Non assigné') . "</td>";
                echo "<td>" . date('d/m/Y H:i', strtotime($row['date_combat'])) . "</td>";
                echo "<td>" . ucfirst($row['type']) . "</td>";
                echo "<td>";
                echo "<form method='post' style='display:inline;'>";
                echo "<input type='hidden' name='match_id' value='" . $row['id'] . "'>";
                echo "<select name='boxeur1_id' required>";
                echo "<option value='" . $row['boxeur1_id'] . "'>" . $row['boxeur1'] . "</option>";
                $boxeurs->data_seek(0);
                while ($boxeur = $boxeurs->fetch_assoc()) {
                    echo "<option value='" . $boxeur['id'] . "'>" . $boxeur['nom'] . "</option>";
                }
                echo "</select>";
                echo "<select name='boxeur2_id' required>";
                echo "<option value='" . $row['boxeur2_id'] . "'>" . $row['boxeur2'] . "</option>";
                $boxeurs->data_seek(0);
                while ($boxeur = $boxeurs->fetch_assoc()) {
                    echo "<option value='" . $boxeur['id'] . "'>" . $boxeur['nom'] . "</option>";
                }
                echo "</select>";
                echo "<select name='arbitre_id' required>";
                echo "<option value='" . $row['arbitre_id'] . "'>" . ($row['arbitre'] ? $row['arbitre'] : 'Aucun') . "</option>";
                $arbitres->data_seek(0);
                while ($arbitre = $arbitres->fetch_assoc()) {
                    echo "<option value='" . $arbitre['id'] . "'>" . $arbitre['username'] . "</option>";
                }
                echo "</select>";
                echo "<input type='datetime-local' name='date_combat' value='" . date('Y-m-d\TH:i', strtotime($row['date_combat'])) . "' required>";
                echo "<button type='submit' name='modifier_combat'>Modifier Combat</button>";
                echo "</form>";
                echo "<form method='post' style='display:inline;'>";
                echo "<input type='hidden' name='match_id' value='" . $row['id'] . "'>";
                echo "<button type='submit' name='supprimer_match' value='supprimer'>Supprimer</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<form method='post'>
                    <button type='submit' name='tout_supprimer'>Tout Supprimer</button>
                  </form>";
        }
    }
    ?>
</body>
</html>
