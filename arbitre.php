<?php

$mysqli = new mysqli("localhost", "root", "", "WBSS");
if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// Choisir le gagnant et affecter l'arbitre
if (isset($_POST['enregistrer_resultat'])) {
    $match_id = $_POST['match_id'];
    $gagnant_id = $_POST['gagnant_id'];
    $arbitre_id = $_SESSION['arbitre_id'];

    // Vérifier si le gagnant existe dans la table boxeurs
    $result = $mysqli->query("SELECT id FROM boxeurs WHERE id = $gagnant_id");
    if ($result->num_rows > 0) {
        // Mettre à jour le match avec le gagnant et l'arbitre
        $stmt = $mysqli->prepare("UPDATE tournoi SET gagnant_id = ?, arbitre_id = ? WHERE id = ?");
        $stmt->bind_param("iii", $gagnant_id, $arbitre_id, $match_id);
        if ($stmt->execute()) {
            echo "<p>Résultat enregistré !</p>";
        } else {
            echo "<p>Erreur lors de l'enregistrement du résultat.</p>";
        }
    } else {
        echo "<p style='color: red;'>L'ID du gagnant n'existe pas dans la base de données.</p>";
    }
}

// Affichage des matchs à juger
$matchs = $mysqli->query("SELECT t.id, t.ronde, b1.nom AS boxeur1, b2.nom AS boxeur2 FROM tournoi t JOIN boxeurs b1 ON t.boxeur1_id = b1.id JOIN boxeurs b2 ON t.boxeur2_id = b2.id WHERE t.gagnant_id IS NULL ORDER BY t.ronde");

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Arbitre - WBSS</title>
    <link rel="stylesheet" href="arbitre.css">
</head>
<body>
    <h1>Arbitrage des matchs</h1>
    <h2>Choisissez le gagnant pour chaque match</h2>
    <table>
        <tr><th>Ronde</th><th>Boxeur 1</th><th>Boxeur 2</th><th>Choisir le gagnant</th></tr>
        <?php while ($row = $matchs->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['ronde'] ?></td>
                <td><?= $row['boxeur1'] ?></td>
                <td><?= $row['boxeur2'] ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="match_id" value="<?= $row['id'] ?>">
                        <select name="gagnant_id" required>
                            <option value="">Choisir le gagnant</option>
                            <option value="<?= $row['boxeur1_id'] ?>"><?= $row['boxeur1'] ?></option>
                            <option value="<?= $row['boxeur2_id'] ?>"><?= $row['boxeur2'] ?></option>
                        </select>
                        <button type="submit" name="enregistrer_resultat">Enregistrer</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
