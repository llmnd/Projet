<?php
$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// ✅ Vérifier et créer la table administrateurs si elle n'existe pas
$create_table = "CREATE TABLE IF NOT EXISTS administrateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL
)";
$mysqli->query($create_table);

// ✅ Vérifier si l'administrateur existe déjà
$login = "Cheikh";  // Nom d'utilisateur
$mot_de_passe = "admin";  // Mot de passe à modifier plus tard

$stmt = $mysqli->prepare("SELECT id FROM administrateurs WHERE login = ?");
if (!$stmt) {
    die("Erreur SQL: " . $mysqli->error);
}

$stmt->bind_param("s", $login);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    // ✅ Hachage du mot de passe
    $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // ✅ Insérer l'admin si inexistant
    $stmt = $mysqli->prepare("INSERT INTO administrateurs (login, mot_de_passe) VALUES (?, ?)");
    $stmt->bind_param("ss", $login, $hashed_password);
    $stmt->execute();

    echo "Utilisateur admin créé avec succès!";
} else {
    echo "L'administrateur existe déjà.";
}

// ✅ Fermer la connexion
$stmt->close();
$mysqli->close();
?>
