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


// Gestion des actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['ajouter_boxeur'])) {
        // Récupération des données du formulaire
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $age = $_POST['age'];
        $pays = $_POST['pays'];
        $categorie_id = $_POST['categorie_id'];
        $classement = $_POST['classement'];

        // Vérifier si la catégorie existe
        $stmt = $mysqli->prepare("SELECT id FROM categories WHERE id = ?");
        $stmt->bind_param("i", $categorie_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // La catégorie existe, vous pouvez insérer le boxeur
            $stmt->close(); // Fermer la première requête

            // Insertion du boxeur dans la base de données
            $stmt = $mysqli->prepare("INSERT INTO boxeurs (nom, prenom, age, pays, categorie_id, classement) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisis", $nom, $prenom, $age, $pays, $categorie_id, $classement);

            if ($stmt->execute()) {
                echo "Boxeur ajouté avec succès!";
            } else {
                echo "Erreur lors de l'ajout du boxeur.";
            }

        } else {
            echo "Erreur : La catégorie n'existe pas.";
        }

        $stmt->close(); // Fermer la requête de vérification
    }
}


// Récupération des données
$categories = $mysqli->query("SELECT * FROM categories");
$boxeurs = $mysqli->query("SELECT * FROM boxeurs");
$arbitres = $mysqli->query("SELECT * FROM arbitres");
$journalistes = $mysqli->query("SELECT * FROM journalistes");
$combats = $mysqli->query("SELECT * FROM combats");

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Administrateur</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Bienvenue, Administrateur</h1>
        <nav>
            <a href="#boxeurs">Boxeurs</a>
            <a href="#categories">Catégories</a>
            <a href="#arbitres">Arbitres</a>
            <a href="#journalistes">Journalistes</a>
            <a href="#combats">Combats</a>
            <a href="logout.php">Se déconnecter</a>
        </nav>
    </header>

    <section id="boxeurs">
        <h2>Gestion des Boxeurs</h2>
        <form method="post">
            <input type="text" name="nom" placeholder="Nom" required>
            <input type="text" name="prenom" placeholder="Prénom" required>
            <input type="number" name="age" placeholder="Âge" required>
            <input type="text" name="pays" placeholder="Pays" required>
            <select name="categorie_id" required>
                <?php while ($row = $categories->fetch_assoc()) { ?>
                    <option value="<?= $row['id'] ?>"><?= $row['nom'] ?></option>
                <?php } ?>
            </select>
            <input type="number" name="classement" placeholder="Classement WBSS" required>
            <button type="submit" name="ajouter_boxeur">Ajouter Boxeur</button>
        </form>

        <h3>Boxeurs Actuels</h3>
        <table>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Âge</th>
                <th>Pays</th>
                <th>Classement</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $boxeurs->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['nom'] ?></td>
                    <td><?= $row['prenom'] ?></td>
                    <td><?= $row['age'] ?></td>
                    <td><?= $row['pays'] ?></td>
                    <td><?= $row['classement'] ?></td>
                    <td><a href="edit_boxeur.php?id=<?= $row['id'] ?>">Modifier</a> | <a href="delete.php?id=<?= $row['id'] ?>">Supprimer</a></td>
                </tr>
            <?php } ?>
        </table>
    </section>

    <section id="categories">
        <h2>Gestion des Catégories</h2>

        <h3>Catégories Actuelles</h3>
        <table>
            <tr>
                <th>Nom</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $categories->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['nom'] ?></td>
                    <td><a href="edit_categorie.php?id=<?= $row['id'] ?>">Modifier</a> | <a href="delete.php?id=<?= $row['id'] ?>">Supprimer</a></td>
                </tr>
            <?php } ?>
        </table>
    </section>

    <!-- Ajoutez les sections pour les arbitres, journalistes et combats ici -->

</body>
</html>
