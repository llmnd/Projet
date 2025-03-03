<?php
$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Vérifier si c'est un administrateur
    $stmt = $mysqli->prepare("SELECT id, mot_de_passe FROM administrateurs WHERE login = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if ($password == 'passer') {  // Remplace par `password_verify($password, $hashed_password)` si mot de passe haché
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = 'admin';
            header("Location: admin.php");
            exit();
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        // Vérifier si c'est un arbitre
        $stmt = $mysqli->prepare("SELECT id, mot_de_passe FROM arbitres WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();

            if ($password == 'passer') {  // Remplace par `password_verify($password, $hashed_password)` si mot de passe haché
                $_SESSION['user_id'] = $id;
                $_SESSION['role'] = 'arbitre';
                header("Location: hmm.php");
                exit();
            } else {
                $error = "Mot de passe incorrect.";
            }
        } else {
            $error = "Nom d'utilisateur introuvable.";
        }
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Connexion</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post" action='connexion.php'>
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
