<?php
$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// Retrieve statistics for all boxers
$query = "SELECT b.nom, b.pays, bs.victoires, bs.defaites, bs.KO, bs.TKO, bs.abandon 
          FROM boxeurs b
          JOIN boxeur_stats bs ON b.id = bs.boxeur_id
          ORDER BY bs.victoires DESC";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Boxeurs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Statistiques des Boxeurs</h1>
    </header>
    <a href="accueil.php" class="btn-home">Retour à l'Accueil</a>
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Pays</th>
                    <th>Victoires</th>
                    <th>Défaites</th>
                    <th>KO</th>
                    <th>TKO</th>
                    <th>Abandon</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['nom']; ?></td>
                        <td><?php echo $row['pays']; ?></td>
                        <td><?php echo $row['victoires']; ?></td>
                        <td><?php echo $row['defaites']; ?></td>
                        <td><?php echo $row['KO']; ?></td>
                        <td><?php echo $row['TKO']; ?></td>
                        <td><?php echo $row['abandon']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $mysqli->close(); ?>
