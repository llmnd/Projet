<?php
session_start();
require 'db.php';

// Initialiser le tournoi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['init_tournoi'])) {
    mysqli_query($link, "TRUNCATE TABLE matches");
    $phases = ['8eme' => 8, 'quart' => 4, 'demi' => 2, 'finale' => 1];
    $stmt = mysqli_prepare($link, "INSERT INTO matches (boxeur1, boxeur2, phase) VALUES (NULL, NULL, ?)");
    foreach ($phases as $phase => $count) {
        for ($i = 0; $i < $count; $i++) {
            mysqli_stmt_bind_param($stmt, 's', $phase);
            mysqli_stmt_execute($stmt);
        }
    }
    header('Location: admin.php');
    exit;
}

// Récupérer les matchs
$query = mysqli_query($link, "SELECT id, boxeur1, boxeur2, vainqueur, mode_victoire, date_combat, time, phase FROM matches ORDER BY id ASC");
$matches = mysqli_fetch_all($query, MYSQLI_ASSOC);

// Organiser les matchs par phase
$matchTree = [];
foreach (["8eme", "quart", "demi", "finale"] as $phase) {
    $matchTree[$phase] = array_values(array_filter($matches, fn($m) => $m['phase'] === $phase));
}

// Remplir les phases avec les vainqueurs des phases précédentes et mettre à jour la base de données
$usedBoxers = [];
foreach (["quart", "demi", "finale"] as $phase) {
    $previousPhase = $phase === "quart" ? "8eme" : ($phase === "demi" ? "quart" : "demi");
    $previousWinners = array_column(array_filter($matches, fn($m) => $m['phase'] === $previousPhase && $m['vainqueur']), 'vainqueur');
    
    foreach ($matchTree[$phase] as &$match) {
        $updated = false;
        foreach (['boxeur1', 'boxeur2'] as $boxerKey) {
            if (empty($match[$boxerKey]) || in_array($match[$boxerKey], $usedBoxers)) {
                foreach ($previousWinners as $winner) {
                    if (!in_array($winner, $usedBoxers)) {
                        $match[$boxerKey] = $winner;
                        $usedBoxers[] = $winner;
                        $updated = true;
                        break;
                    }
                }
            } else {
                $usedBoxers[] = $match[$boxerKey];
            }
        }
        if ($updated) {
            mysqli_query($link, "UPDATE matches SET boxeur1 = '{$match['boxeur1']}', boxeur2 = '{$match['boxeur2']}' WHERE id = {$match['id']}");
        }
    }
}

// Afficher le tournoi
function generateTournament($matchTree, $phase) {
    if (!isset($matchTree[$phase])) return;

    echo "<div class='round round-$phase'>";
    foreach ($matchTree[$phase] as $match) {
        $id = $match['id'];
        $boxeur1 = htmlspecialchars($match['boxeur1'] ?? 'En attente');
        $boxeur2 = htmlspecialchars($match['boxeur2'] ?? 'En attente');
        $date = htmlspecialchars($match['date_combat'] ?? 'À définir');
        $time = htmlspecialchars($match['time'] ?? 'À définir');
        
        echo "<div class='match'>
                <div class='boxer'>$boxeur1</div>
                <div class='boxer'>$boxeur2</div>
                <div class='date'>Date: $date - Heure: $time</div>
                <form action='edit_admin.php' method='get'>
                    <input type='hidden' name='id' value='$id'>
                    <input type='hidden' name='boxeur1' value='$boxeur1'>
                    <input type='hidden' name='boxeur2' value='$boxeur2'>
                    <button type='submit'>Modifier</button>
                </form>
              </div>";
    }
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <title>Admin - WBSS</title>
    <link rel="stylesheet" href="admin.css">
    <a href="accueil.php" class="btn-home">Retour à l'Accueil</a>
    <meta charset="UTF-8">
    <title>Tournoi de Boxe - Administrateur</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .tournament-container { display: flex; justify-content: center; align-items: center; flex-wrap: wrap; }
        .round { display: flex; flex-direction: column; margin: 20px; padding: 10px; }
        .match { margin: 10px 0; padding: 10px; background: #eee; border-radius: 5px; text-align: center; }
        .boxer { margin: 5px; padding: 5px; border: 1px solid #000; background: #fff; }
        .date { font-size: 0.9em; color: gray; }
    </style>
</head>
<body>
    <h1>Tournoi de Boxe - Interface Administrateur</h1>
    <form method="post">
        <button type="submit" name="init_tournoi">Initialiser le Tournoi</button>
    </form>
    <div class="tournament-container">
        <?php foreach (["8eme", "quart", "demi", "finale"] as $phase) generateTournament($matchTree, $phase); ?>
    </div>
</body>
</html>
