<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get username and password from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Connect to the database
    $mysqli = new mysqli("localhost", "root", "", "WBSS");

    if ($mysqli->connect_error) {
        die("Connexion échouée: " . $mysqli->connect_error);
    }

    // Query to check if user exists
    $stmt = $mysqli->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($password== 'passer'){
            // Start the session and store user data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect to the appropriate page based on role
            if ($user['role'] == 'admin') {
                header("Location: admin.php");
                exit();
            } elseif ($user['role'] == 'arbitre') {
                header("Location: hmm.php");
                exit();
            }
        } else {
            $error = "Mot de passe incorrect!";
        }
    } else {
        $error = "Utilisateur non trouvé!";
    }

    $stmt->close();
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - WBSS</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <h1>Connexion</h1>

    <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>

    <form method="post" action="connexion.php">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Mot de passe:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
