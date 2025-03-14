<?php

$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// Récupérer le gagnant du tournoi
$query = "SELECT b.nom, b.age, b.pays, s.victoires, s.defaites, s.KO, s.decision, s.abandon 
          FROM tournoi t
          JOIN boxeurs b ON t.gagnant_id = b.id
          LEFT JOIN stats s ON b.id = s.boxeur_id
          WHERE t.type = 'finale' AND t.termine = 1
          LIMIT 1";
$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $gagnant = $result->fetch_assoc();
} else {
    $gagnant = null;
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gagnant du Tournoi</title>
    <link rel="stylesheet" href="accueil.css">
</head>
<body>
    <header>
        <h3>Gagnant du Tournoi</h3>
        <nav>
            <a href="accueil.php" class="btn-home">Retour à l'Accueil</a>
        </nav>
    </header>
    
    <section id="gagnant">
        <h2>Félicitations au Gagnant !</h2>
        <?php if ($gagnant): ?>
            <div class="gagnant-container">
                <h3><?php echo $gagnant['nom']; ?></h3>
                <p><strong>Âge:</strong> <?php echo $gagnant['age']; ?></p>
                <p><strong>Pays:</strong> <?php echo $gagnant['pays']; ?></p>
                <h4>Statistiques</h4>
                <ul>
                    <li><strong>Victoires:</strong> <?php echo $gagnant['victoires']; ?></li>
                    <li><strong>Défaites:</strong> <?php echo $gagnant['defaites']; ?></li>
                    <li><strong>KO:</strong> <?php echo $gagnant['KO']; ?></li>
                    <li><strong>Décision:</strong> <?php echo $gagnant['decision']; ?></li>
                    <li><strong>Abandon:</strong> <?php echo $gagnant['abandon']; ?></li>
                </ul>
            </div>
        <?php else: ?>
            <p>Aucun gagnant trouvé.</p>
        <?php endif; ?>
    </section>
</body>
</html>
