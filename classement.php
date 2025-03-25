<?php
$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// Retrieve statistics for all boxers
$query = "SELECT b.nom, b.pays, 
                 COALESCE(bs.victoires, 0) AS victoires, 
                 COALESCE(bs.defaites, 0) AS defaites, 
                 COALESCE(bs.KO, 0) AS KO, 
                 COALESCE(bs.TKO, 0) AS TKO, 
                 COALESCE(bs.abandon, 0) AS abandon 
          FROM boxeurs b
          LEFT JOIN boxeur_stats bs ON b.id = bs.boxeur_id
          ORDER BY bs.victoires DESC, bs.KO DESC, bs.TKO DESC";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classement des Boxeurs</title>
    <link rel="stylesheet" href="classement.css">
</head>
<body>
    <header>
        <h1>Classement des Boxeurs</h1>
        <a href="accueil.php" class="btn-home">Retour à l'Accueil</a>
    </header>
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Position</th>
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
                <?php 
                $position = 1;
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $position++; ?></td>
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
