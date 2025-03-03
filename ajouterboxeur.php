<?php
$mysqli = new mysqli("localhost", "root", "", "WBSS");

if ($mysqli->connect_error) {
    die("Connexion échouée: " . $mysqli->connect_error);
}

// Récupérer toutes les catégories
$categories_result = $mysqli->query("SELECT * FROM tournoi");

if ($categories_result->num_rows > 0) {
    while ($category = $categories_result->fetch_assoc()) {
        $categorie_id = $category['id'];

        // Insérer 8 boxeurs pour chaque catégorie
        for ($i = 1; $i <= 8; $i++) {
            $nom = "Boxeur $i";
            $prenom = "Nom-$categorie_id";
            $age = rand(18, 35); // Âge aléatoire entre 18 et 35
            $pays = "Pays-$categorie_id";
            $classement = $i; // Classement de 1 à 8 dans chaque catégorie

            $stmt = $mysqli->prepare("INSERT INTO boxeurs (nom, age, pays, categorie_id, classement) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisis", $nom, $age, $pays, $categorie_id, $classement);
            $stmt->execute();
        }
    }

    echo "8 boxeurs ont été ajoutés dans chaque catégorie.";
} else {
    echo "Aucune catégorie trouvée.";
}

$mysqli->close();
?>
