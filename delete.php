<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // Rediriger vers la page de login si l'utilisateur n'est pas connecté
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

if (isset($_GET['id'])) {
    $boxeur_id = $_GET['id'];

    // Vérification si le boxeur existe
    $stmt = $mysqli->prepare("SELECT id FROM boxeurs WHERE id = ?");
    $stmt->bind_param("i", $boxeur_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Suppression du boxeur
        $stmt->close(); // Fermer la requête de vérification

        $stmt = $mysqli->prepare("DELETE FROM boxeurs WHERE id = ?");
        $stmt->bind_param("i", $boxeur_id);
        if ($stmt->execute()) {
            echo "Boxeur supprimé avec succès!";
        } else {
            echo "Erreur lors de la suppression du boxeur.";
        }

        $stmt->close();
    } else {
        echo "Erreur : Le boxeur n'existe pas.";
    }
} else {
    echo "Erreur : Aucun ID de boxeur spécifié.";
}

$mysqli->close();
?>
