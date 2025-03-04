<?php
$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// Si un boxeur est sélectionné pour afficher ses stats
if (isset($_GET['id'])) {
    $boxeur_id = $_GET['id'];

    // Récupérer les statistiques du boxeur
    $query = "SELECT b.nom, s.victoires, s.defaites, s.KO, s.decision, s.abandon 
              FROM boxeurs b
              LEFT JOIN stats s ON b.id = s.boxeur_id
              WHERE b.id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $boxeur_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nom = $row['nom'];
        $victoires = $row['victoires'] ?: 0;
        $defaites = $row['defaites'] ?: 0;
        $KO = $row['KO'] ?: 0;
        $decision = $row['decision'] ?: 0;
        $abandon = $row['abandon'] ?: 0;
    } else {
        $nom = "Inconnu";
        $victoires = $defaites = $KO = $decision = $abandon = 0;
    }

    $stmt->close();
} else {
    // Par défaut, afficher tous les boxeurs
    $query = "SELECT b.id, b.nom, s.victoires, s.defaites, s.KO, s.decision, s.abandon 
              FROM boxeurs b
              LEFT JOIN stats s ON b.id = s.boxeur_id
              ORDER BY b.nom ASC";
    $result = $mysqli->query($query);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Boxeurs</title>
    <link rel="stylesheet" href="stats.css">
</head>
<body>

<header>
    <h1>Statistiques des Boxeurs</h1>
</header>
<a href="h.php" class="btn-home">Retour à l'Accueil</a>

<div class="container">
    <?php if (isset($boxeur_id)): ?>
        <!-- Affichage des statistiques d'un boxeur -->
        <h2>Statistiques de <?php echo $nom; ?></h2>
        <table>
            <tr><td>Victoires</td><td><?php echo $victoires; ?></td></tr>
            <tr><td>Défaites</td><td><?php echo $defaites; ?></td></tr>
            <tr><td>KO</td><td><?php echo $KO; ?></td></tr>
            <tr><td>Décision</td><td><?php echo $decision; ?></td></tr>
            <tr><td>Abandon</td><td><?php echo $abandon; ?></td></tr>
        </table>
        <br>
        <a href="stats.php" class="btn-back">Retour à la liste des boxeurs</a>
    <?php else: ?>
        <!-- Affichage de la liste des boxeurs -->
        <h2>Liste des Boxeurs</h2>
        <table>
            <thead>
                <tr>
                    <th>Nom du Boxeur</th>
                    <th>Victoires</th>
                    <th>Défaites</th>
                    <th>KO</th>
                    <th>Décision</th>
                    <th>Abandon</th>
                    <th>Voir</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['nom'] . "</td>";
                        echo "<td>" . ($row['victoires'] ?: 0) . "</td>";
                        echo "<td>" . ($row['defaites'] ?: 0) . "</td>";
                        echo "<td>" . ($row['KO'] ?: 0) . "</td>";
                        echo "<td>" . ($row['decision'] ?: 0) . "</td>";
                        echo "<td>" . ($row['abandon'] ?: 0) . "</td>";
                        echo "<td><a href='stats.php?id=" . $row['id'] . "' class='btn-view'>Voir</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Aucun boxeur trouvé.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>

<?php
$mysqli->close();
?>
