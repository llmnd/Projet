<?php
session_start();
require 'db.php';

// Récupérer les matchs
$query = mysqli_query($link, "SELECT id, boxeur1, boxeur2, vainqueur, mode_victoire, date_combat, time, phase FROM matches ORDER BY id ASC");
$matches = mysqli_fetch_all($query, MYSQLI_ASSOC);

// Organiser les matchs par phase
$matchTree = [];
foreach (["8eme", "quart", "demi", "finale"] as $phase) {
    $matchTree[$phase] = array_values(array_filter($matches, fn($m) => $m['phase'] === $phase));
}

// Remplir les phases avec les vainqueurs des phases précédentes
$usedBoxers = [];
foreach (["quart", "demi", "finale"] as $phase) {
    $previousPhase = $phase === "quart" ? "8eme" : ($phase === "demi" ? "quart" : "demi");
    $previousWinners = array_column(array_filter($matches, fn($m) => $m['phase'] === $previousPhase && $m['vainqueur']), 'vainqueur');
    
    foreach ($matchTree[$phase] as &$match) {
        foreach (['boxeur1', 'boxeur2'] as $boxerKey) {
            if (empty($match[$boxerKey]) || in_array($match[$boxerKey], $usedBoxers)) {
                foreach ($previousWinners as $winner) {
                    if (!in_array($winner, $usedBoxers)) {
                        $match[$boxerKey] = $winner;
                        $usedBoxers[] = $winner;
                        break;
                    }
                }
            } else {
                $usedBoxers[] = $match[$boxerKey];
            }
        }
    }
}

// Fonction pour afficher le tournoi
function generateTournament($matchTree, $currentPhase, $editPage) {
    if (!isset($matchTree[$currentPhase])) return;

    echo "<div class='round round-$currentPhase'>";
    foreach ($matchTree[$currentPhase] as $match) {
        $id = $match['id'];
        $boxeur1 = htmlspecialchars($match['boxeur1'] ?? 'En attente');
        $boxeur2 = htmlspecialchars($match['boxeur2'] ?? 'En attente');
        $date = htmlspecialchars($match['date_combat'] ?? 'À définir');
        $time = htmlspecialchars($match['time'] ?? 'À définir');
        
        echo "<div class='match'>
                <div class='boxer'>$boxeur1</div>
                <div class='boxer'>$boxeur2</div>
                <div class='date'>Date: $date - Heure: $time</div>
                <button onclick=\"location.href='$editPage?id=$id'\">Modifier</button>
              </div>";
    }
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arbitre - Validation des Matchs</title>
    <link rel="stylesheet" href="admin.css">
    <title>Tournoi de Boxe</title>
    <a href="accueil.php" class="btn-home">Retour à l'Accueil</a>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .tournament-container { display: flex; justify-content: center; align-items: center; flex-wrap: wrap; }
        .round { display: flex; flex-direction: column; margin: 20px; padding: 10px; }
        .match { position: relative; margin: 10px 0; padding: 10px; background: #eee; border-radius: 5px; text-align: center; }
        .boxer { margin: 5px; padding: 5px; border: 1px solid #000; background: #fff; }
        .date { font-size: 0.9em; color: gray; }
    </style>
</head>
<body>
    <h1>Tournoi de Boxe</h1>
    <div class="tournament-container">
        <?php 
        $editPage = basename($_SERVER['PHP_SELF']) === 'test_tournoi.php' ? 'edit_arbitre.php' : 'edit_admin.php';
        foreach (["8eme", "quart", "demi", "finale"] as $phase) generateTournament($matchTree, $phase, $editPage); 
        ?>
    </div>
</body>
</html>
