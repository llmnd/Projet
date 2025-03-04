<?php

$mysqli = new mysqli("localhost", "root", "", "WBSS");
if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

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

    // Vider la table tournoi
    if (!$mysqli->query("DELETE FROM tournoi")) {
        echo "<p style='color: red;'>Erreur lors de la suppression des anciens matchs.</p>";
        return;
    }

    for ($i = 0; $i < count($boxeurs) - 1; $i += 2) {
        $boxeur1_id = $boxeurs[$i];
        $boxeur2_id = $boxeurs[$i + 1];

        // Vérifier si les boxeurs sont différents
        if ($boxeur1_id == $boxeur2_id) {
            continue;
        }

        // Insérer un match avec une date par défaut
        $date_combat = date('Y-m-d H:i:s');
        $stmt = $mysqli->prepare("INSERT INTO tournoi (ronde, boxeur1_id, boxeur2_id, date_combat) VALUES (1, ?, ?, ?)");
        if (!$stmt) {
            echo "<p style='color: red;'>Erreur lors de la préparation de la requête d'insertion.</p>";
            return;
        }
        $stmt->bind_param("iis", $boxeur1_id, $boxeur2_id, $date_combat);
        if (!$stmt->execute()) {
            echo "<p style='color: red;'>Erreur lors de l'exécution de la requête d'insertion.</p>";
            return;
        }
    }
    echo "<p style='color: green;'>Tournoi initialisé !</p>";
}

// Vérifier si l'administrateur veut initialiser le tournoi
if (isset($_POST['initialiser_tournoi'])) {
    initialiserTournoi($mysqli);
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
$matchs = $mysqli->query("SELECT t.id, t.ronde, b1.nom AS boxeur1, b2.nom AS boxeur2, a.username AS arbitre, t.date_combat, t.boxeur1_id, t.boxeur2_id, t.arbitre_id FROM tournoi t JOIN boxeurs b1 ON t.boxeur1_id = b1.id JOIN boxeurs b2 ON t.boxeur2_id = b2.id LEFT JOIN arbitres a ON t.arbitre_id = a.id ORDER BY t.ronde");

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
    <a href="h.php" class="btn-home">Retour à l'Accueil</a>
</head>
<body>
    <h1>Gestion du Tournoi</h1>
    
    <?php
    // Vérification si des matchs existent, sinon afficher un bouton pour initialiser
    if ($matchs->num_rows == 0) {
        echo "<p style='color: red;'>Il n'y a pas de matchs disponibles. Veuillez initialiser le tournoi.</p>";
        echo "<form method='post'>
                <button type='submit' name='initialiser_tournoi'>Initialiser Tournoi</button>
              </form>";
    }
    ?>

    <h2>Matchs en cours</h2>
    <table>
        <tr><th>Ronde</th><th>Boxeur 1</th><th>Boxeur 2</th><th>Arbitre</th><th>Date du Combat</th><th>Action</th></tr>
        <?php while ($row = $matchs->fetch_assoc()) {
            // Ne pas afficher les matchs avec les mêmes boxeurs
            if ($row['boxeur1_id'] == $row['boxeur2_id']) {
                continue;
            }
        ?>
            <tr>
                <td><?= $row['ronde'] ?></td>
                <td><?= $row['boxeur1'] ?></td>
                <td><?= $row['boxeur2'] ?></td>
                <td><?= $row['arbitre'] ? $row['arbitre'] : 'Non assigné' ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row['date_combat'])) ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="match_id" value="<?= $row['id'] ?>">
                        <select name="boxeur1_id" required>
                            <option value="<?= $row['boxeur1_id'] ?>"><?= $row['boxeur1'] ?></option>
                            <?php
                            // Réinitialiser les résultats des boxeurs à chaque itération
                            $boxeurs->data_seek(0);
                            while ($boxeur = $boxeurs->fetch_assoc()) { ?>
                                <option value="<?= $boxeur['id'] ?>"><?= $boxeur['nom'] ?></option>
                            <?php } ?>
                        </select>
                        <select name="boxeur2_id" required>
                            <option value="<?= $row['boxeur2_id'] ?>"><?= $row['boxeur2'] ?></option>
                            <?php
                            // Réinitialiser les résultats des boxeurs à chaque itération
                            $boxeurs->data_seek(0);
                            while ($boxeur = $boxeurs->fetch_assoc()) { ?>
                                <option value="<?= $boxeur['id'] ?>"><?= $boxeur['nom'] ?></option>
                            <?php } ?>
                        </select>
                        <select name="arbitre_id" required>
                            <option value="<?= $row['arbitre_id'] ?>"><?= $row['arbitre'] ? $row['arbitre'] : 'Aucun' ?></option>
                            <?php
                            // Réinitialiser les résultats des arbitres à chaque itération
                            $arbitres->data_seek(0);
                            while ($arbitre = $arbitres->fetch_assoc()) { ?>
                                <option value="<?= $arbitre['id'] ?>"><?= $arbitre['username'] ?></option>
                            <?php } ?>
                        </select>
                        <input type="datetime-local" name="date_combat" value="<?= date('Y-m-d\TH:i', strtotime($row['date_combat'])) ?>" required>
                        <button type="submit" name="modifier_combat">Modifier Combat</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="match_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="supprimer_match" value="supprimer">Supprimer</button>
                    </form>
                </td>
            </tr>
            
        <?php } ?>
    </table>
</body>
</html>

