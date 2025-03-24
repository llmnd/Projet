<?php
session_start();
require 'db.php';

// Récupérer les informations du match à modifier
$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID du match non spécifié.");
}

$query = mysqli_query($link, "SELECT * FROM matches WHERE id = '$id'");
$match = mysqli_fetch_assoc($query);

if (!$match) {
    die("Match non trouvé.");
}

// Déterminer les vainqueurs des phases précédentes pour le pré-remplissage
$prevPhase = null;
switch ($match['phase']) {
    case 'quart':
        $prevPhase = '8eme';
        break;
    case 'demi':
        $prevPhase = 'quart';
        break;
    case 'finale':
        $prevPhase = 'demi';
        break;
}

$boxeur1 = $match['boxeur1'] ?? '';
$boxeur2 = $match['boxeur2'] ?? '';

// Pré-remplir avec les vainqueurs des phases précédentes si les boxeurs ne sont pas définis
if ($prevPhase) {
    $queryWinners = mysqli_query($link, "SELECT vainqueur FROM matches WHERE phase = '$prevPhase' ORDER BY id ASC");
    $winners = mysqli_fetch_all($queryWinners, MYSQLI_ASSOC);
    
    if (count($winners) >= 2) {
        $boxeur1 = empty($boxeur1) ? ($winners[0]['vainqueur'] ?? '') : $boxeur1;
        $boxeur2 = empty($boxeur2) ? ($winners[1]['vainqueur'] ?? '') : $boxeur2;
    }
}

// Récupérer la liste des boxeurs pour les 8èmes de finale
$queryBoxeurs = mysqli_query($link, "SELECT nom FROM boxeurs");
$boxeurs = mysqli_fetch_all($queryBoxeurs, MYSQLI_ASSOC);

// Gérer la mise à jour des informations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $boxeur1 = $_POST['boxeur1'] ?? $boxeur1;
    $boxeur2 = $_POST['boxeur2'] ?? $boxeur2;
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    
    $updateQuery = "UPDATE matches SET boxeur1 = '$boxeur1', boxeur2 = '$boxeur2', date_combat = '$date', time = '$time' WHERE id = '$id'";
    mysqli_query($link, $updateQuery);
    header("Location: tournoi_admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="edit_admin.css">
    <title>Modifier le Match</title>
</head>
<body>
    <h1>Modifier le Match</h1>
    <form method="post">
        <label>Boxeur 1:</label>
        <?php if ($match['phase'] === '8eme'): ?>
            <select name="boxeur1" required>
                <option value="">Sélectionner un boxeur</option>
                <?php foreach ($boxeurs as $boxeur): ?>
                    <option value="<?= htmlspecialchars($boxeur['nom']) ?>" <?= ($boxeur['nom'] == $boxeur1) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($boxeur['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <!-- Afficher le boxeur pour les autres phases -->
            <input type="text" name="boxeur1" value="<?= htmlspecialchars($boxeur1 ?? '') ?>" readonly>
        <?php endif; ?>
        
        <label>Boxeur 2:</label>
        <?php if ($match['phase'] === '8eme'): ?>
            <select name="boxeur2" required>
                <option value="">Sélectionner un boxeur</option>
                <?php foreach ($boxeurs as $boxeur): ?>
                    <option value="<?= htmlspecialchars($boxeur['nom']) ?>" <?= ($boxeur['nom'] == $boxeur2) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($boxeur['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <!-- Afficher le boxeur pour les autres phases -->
            <input type="text" name="boxeur2" value="<?= htmlspecialchars($boxeur2 ?? '') ?>" readonly>
        <?php endif; ?>
        
        <br>
        <label>Date:</label>
        <input type="date" name="date" value="<?= htmlspecialchars($match['date'] ?? '') ?>" required>
        
        <label>Heure:</label>
        <input type="time" name="time" value="<?= htmlspecialchars($match['time'] ?? '') ?>" required>
        
        <br>
        <button type="submit">Enregistrer</button>
    </form>
    <a href="tournoi_admin.php">Retour</a>
</body>
</html>
