<?php
session_start();
require 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Match invalide.");
}

$match_id = (int)$_GET['id'];

// Récupérer les informations du match
$query = mysqli_prepare($link, "SELECT boxeur1, boxeur2 FROM matches WHERE id = ?");
mysqli_stmt_bind_param($query, "i", $match_id);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);
$match = mysqli_fetch_assoc($result);

if (!$match) {
    die("Match introuvable.");
}

$boxeur1 = $match['boxeur1'] ?? '';
$boxeur2 = $match['boxeur2'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $winner = $_POST['winner'] ?? '';
    $win_method = $_POST['win_method'] ?? '';

    if (!in_array($winner, [$boxeur1, $boxeur2]) || 
        !in_array($win_method, ['KO', 'TKO', 'Décision', 'Abandon'])) {
        die("Données invalides.");
    }
    
    $loser = ($winner == $boxeur1) ? $boxeur2 : $boxeur1;

    // Mettre à jour le match
    $update_match = mysqli_prepare($link, "UPDATE matches SET vainqueur = ?, mode_victoire = ? WHERE id = ?");
    mysqli_stmt_bind_param($update_match, "ssi", $winner, $win_method, $match_id);
    mysqli_stmt_execute($update_match);

    // Insérer un boxeur dans stats s'il n'existe pas
    function insertBoxerIfNotExists($link, $boxer) {
        $check_query = mysqli_prepare($link, "SELECT 1 FROM stats WHERE nom = ? LIMIT 1");
        mysqli_stmt_bind_param($check_query, "s", $boxer);
        mysqli_stmt_execute($check_query);
        $result = mysqli_stmt_get_result($check_query);
        if (mysqli_num_rows($result) == 0) {
            $insert_query = mysqli_prepare($link, "INSERT INTO stats (nom, victoires, defaites, KO, abandon, TKO) VALUES (?, 0, 0, 0, 0, 0)");
            mysqli_stmt_bind_param($insert_query, "s", $boxer);
            mysqli_stmt_execute($insert_query);
        }
    }

    insertBoxerIfNotExists($link, $winner);
    insertBoxerIfNotExists($link, $loser);

    // Mettre à jour les statistiques du vainqueur
    $update_winner_query = "UPDATE stats SET victoires = victoires + 1";
    if ($win_method == 'KO') {
        $update_winner_query .= ", KO = KO + 1";
    } elseif ($win_method == 'TKO') {
        $update_winner_query .= ", TKO = TKO + 1";
    }
    $update_winner_query .= " WHERE nom = ?";
    $update_winner = mysqli_prepare($link, $update_winner_query);
    mysqli_stmt_bind_param($update_winner, "s", $winner);
    mysqli_stmt_execute($update_winner);

    // Mettre à jour les statistiques du perdant
    $update_loser_query = "UPDATE stats SET defaites = defaites + 1";
    if ($win_method == 'Abandon') {
        $update_loser_query .= ", abandon = abandon + 1";
    }
    $update_loser_query .= " WHERE nom = ?";
    $update_loser = mysqli_prepare($link, $update_loser_query);
    mysqli_stmt_bind_param($update_loser, "s", $loser);
    mysqli_stmt_execute($update_loser);

    echo "<p>Résultat mis à jour avec succès !</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="edit_admin.css">
    <title>Modification du match</title>
</head>
<body>
    <h1>Modifier le match</h1>
    <p><strong>Match :</strong> <?= htmlspecialchars($boxeur1) ?> vs <?= htmlspecialchars($boxeur2) ?></p>
    
    <form method="post">
        <label for="winner">Vainqueur :</label>
        <select name="winner" id="winner" required>
            <option value="<?= htmlspecialchars($boxeur1) ?>"><?= htmlspecialchars($boxeur1) ?></option>
            <option value="<?= htmlspecialchars($boxeur2) ?>"><?= htmlspecialchars($boxeur2) ?></option>
        </select>
        
        <label for="win_method">Mode de victoire :</label>
        <select name="win_method" id="win_method" required>
            <option value="KO">KO</option>
            <option value="TKO">TKO</option>
            <option value="Décision">Décision</option>
            <option value="Abandon">Abandon</option>
        </select>
        
        <button type="submit">Mettre à jour</button>
    </form>
    
    <a href="test_tournoi.php">Retour au tournoi</a>
</body>
</html>
