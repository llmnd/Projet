<?php
$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// Sample data - Insert an admin user
$username = 'admin';
$password = 'admin123'; // The raw password (will be hashed)
$hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
$role = 'admin'; // Can be 'admin' or 'arbitre'

// Insert the user into the database
$stmt = $mysqli->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $hashed_password, $role);
if ($stmt->execute()) {
    echo "Utilisateur ajouté avec succès!";
} else {
    echo "Erreur lors de l'ajout de l'utilisateur.";
}

$stmt->close();
$mysqli->close();
?>
