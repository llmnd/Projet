<?php
$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// ✅ Vérifier et créer la table arbitres si elle n'existe pas
$create_table = "CREATE TABLE IF NOT EXISTS arbitres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL
)";

if (!$mysqli->query($create_table)) {
    die("Erreur de création de la table arbitres: " . $mysqli->error);
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];   // Nom d'utilisateur
    $mot_de_passe = $_POST['mot_de_passe'];  // Mot de passe

    // ✅ Vérifier si l'arbitre existe déjà
    $stmt = $mysqli->prepare("SELECT id FROM arbitres WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        // ✅ Hachage du mot de passe
        $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        // ✅ Insérer l'arbitre si inexistant
        $stmt = $mysqli->prepare("INSERT INTO arbitres (login, mot_de_passe) VALUES (?, ?)");
        $stmt->bind_param("ss", $login, $hashed_password);
        $stmt->execute();

        echo "Arbitre ajouté avec succès !";
    } else {
        echo "L'arbitre existe déjà.";
    }

    // Fermer la requête
    $stmt->close();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Arbitre</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <h1>Ajouter un Arbitre</h1>

    <form method="post" action="ajoutarad.php">
        <label for="login">Nom d'utilisateur:</label>
        <input type="text" name="login" id="login" required>

        <label for="mot_de_passe">Mot de passe:</label>
        <input type="password" name="mot_de_passe" id="mot_de_passe" required>

        <button type="submit">Ajouter Arbitre</button>
    </form>

    <a href="admin.php">Retour à l'administration</a>
</body>
</html>
