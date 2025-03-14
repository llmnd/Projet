<?php
// Configuration de la connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tournoi_boxe";

// Connexion au serveur MySQL
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Création de la base de données si elle n'existe pas déjà
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Base de données créée ou déjà existante.\n";
} else {
    echo "Erreur lors de la création de la base de données: " . $conn->error;
}
$conn->select_db($dbname);

// Création de la table des boxeurs
$sql = "CREATE TABLE IF NOT EXISTS Boxeurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    nationalite VARCHAR(50),
    categorie VARCHAR(50)
)";
if ($conn->query($sql) === TRUE) {
    echo "Table Boxeurs créée avec succès.\n";
} else {
    echo "Erreur lors de la création de la table Boxeurs: " . $conn->error;
}

// Création de la table des combats
$sql = "CREATE TABLE IF NOT EXISTS Combats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    boxeur1_id INT,
    boxeur2_id INT,
    date_combat DATETIME,
    resultat VARCHAR(50), -- ex : 'KO', 'TKO', 'Decision'
    methode VARCHAR(50),
    rounds INT,
    FOREIGN KEY (boxeur1_id) REFERENCES Boxeurs(id),
    FOREIGN KEY (boxeur2_id) REFERENCES Boxeurs(id)
)";
if ($conn->query($sql) === TRUE) {
    echo "Table Combats créée avec succès.\n";
} else {
    echo "Erreur lors de la création de la table Combats: " . $conn->error;
}

// Création de la table des statistiques (pour chaque combat et chaque boxeur)
$sql = "CREATE TABLE IF NOT EXISTS Stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    combat_id INT,
    boxeur_id INT,
    coups_portes INT,
    coups_encaisses INT,
    rounds_gagnes INT,
    FOREIGN KEY (combat_id) REFERENCES Combats(id),
    FOREIGN KEY (boxeur_id) REFERENCES Boxeurs(id)
)";
if ($conn->query($sql) === TRUE) {
    echo "Table Stats créée avec succès.\n";
} else {
    echo "Erreur lors de la création de la table Stats: " . $conn->error;
}

// Création de la table des journalistes pour gérer les accès restreints
$sql = "CREATE TABLE IF NOT EXISTS Journalistes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL  -- stocker le mot de passe hashé
)";
if ($conn->query($sql) === TRUE) {
    echo "Table Journalistes créée avec succès.\n";
} else {
    echo "Erreur lors de la création de la table Journalistes: " . $conn->error;
}

// Insertion de quelques boxeurs (données d'exemple) avec une boucle for
$boxeurs = [
    ['nom'=>'Sarr', 'prenom'=>'Ali', 'nationalite'=>'SN', 'categorie'=>'Poids Légers'],
    ['nom'=>'Diop', 'prenom'=>'Moussa', 'nationalite'=>'SN', 'categorie'=>'Poids Moyens'],
];

foreach ($boxeurs as $b) {
    $nom = $conn->real_escape_string($b['nom']);
    $prenom = $conn->real_escape_string($b['prenom']);
    $nationalite = $conn->real_escape_string($b['nationalite']);
    $categorie = $conn->real_escape_string($b['categorie']);
    $sql = "INSERT INTO Boxeurs (nom, prenom, nationalite, categorie) 
            VALUES ('$nom', '$prenom', '$nationalite', '$categorie')";
    if ($conn->query($sql) === TRUE) {
        echo "Boxeur inséré : $prenom $nom.\n";
    } else {
        echo "Erreur lors de l'insertion d'un boxeur: " . $conn->error;
    }
}

// Fonction pour simuler un combat entre deux boxeurs
function simulateCombat($conn, $boxeur1_id, $boxeur2_id) {
    // Choix aléatoire du résultat : KO, TKO ou Decision
    $resultats = ['KO', 'TKO', 'Decision'];
    $resultat = $resultats[array_rand($resultats)];
    
    // Nombre aléatoire de rounds (entre 1 et 12)
    $rounds = rand(1, 12);
    $date_combat = date('Y-m-d H:i:s');
    
    // Insertion du combat dans la table Combats
    $sql = "INSERT INTO Combats (boxeur1_id, boxeur2_id, date_combat, resultat, methode, rounds)
            VALUES ($boxeur1_id, $boxeur2_id, '$date_combat', '$resultat', '$resultat', $rounds)";
    if ($conn->query($sql) === TRUE) {
        $combat_id = $conn->insert_id;
        echo "Combat inséré entre Boxeur $boxeur1_id et Boxeur $boxeur2_id. Résultat : $resultat\n";
        
        // Simulation des statistiques pour chacun des deux boxeurs
        for ($i = 0; $i < 2; $i++) {
            $boxeur_id = ($i == 0) ? $boxeur1_id : $boxeur2_id;
            $coups_portes = rand(20, 100);
            $coups_encaisses = rand(10, 50);
            $rounds_gagnes = rand(0, $rounds);
            $sqlStats = "INSERT INTO Stats (combat_id, boxeur_id, coups_portes, coups_encaisses, rounds_gagnes)
                         VALUES ($combat_id, $boxeur_id, $coups_portes, $coups_encaisses, $rounds_gagnes)";
            if ($conn->query($sqlStats) === TRUE) {
                echo "Statistiques insérées pour le Boxeur $boxeur_id dans le combat $combat_id.\n";
            } else {
                echo "Erreur lors de l'insertion des stats: " . $conn->error;
            }
        }
    } else {
        echo "Erreur lors de l'insertion du combat: " . $conn->error;
    }
}

// Récupération des IDs des boxeurs pour simuler des combats
$boxeur_ids = [];
$result = $conn->query("SELECT id FROM Boxeurs");
while ($row = $result->fetch_assoc()) {
    $boxeur_ids[] = $row['id'];
}

// Pour simplifier, on s'assure d'avoir un nombre pair de boxeurs
if (count($boxeur_ids) % 2 != 0) {
    array_pop($boxeur_ids);
}

// On mélange les boxeurs et on les pair pour simuler des combats
shuffle($boxeur_ids);
for ($i = 0; $i < count($boxeur_ids); $i += 2) {
    $boxeur1_id = $boxeur_ids[$i];
    $boxeur2_id = $boxeur_ids[$i+1];
    simulateCombat($conn, $boxeur1_id, $boxeur2_id);
}

// Gestion d'accès pour les journalistes
session_start();
// Dans un vrai système, le rôle serait défini lors de l'authentification
$_SESSION['role'] = 'journaliste';

function isJournaliste() {
    return (isset($_SESSION['role']) && $_SESSION['role'] == 'journaliste');
}

// Affichage des combats accessible aux journalistes
if (isJournaliste()) {
    echo "\n--- Accès Journaliste : Liste des Combats ---\n";
    $sql = "SELECT c.id, b1.prenom AS boxeur1, b2.prenom AS boxeur2, c.resultat, c.date_combat
            FROM Combats c
            JOIN Boxeurs b1 ON c.boxeur1_id = b1.id
            JOIN Boxeurs b2 ON c.boxeur2_id = b2.id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "Combat #" . $row['id'] . " : " . $row['boxeur1'] . " vs " . $row['boxeur2'] . 
                 " - Résultat: " . $row['resultat'] . " - Date: " . $row['date_combat'] . "\n";
        }
    } else {
        echo "Aucun combat trouvé.\n";
    }
} else {
    echo "Accès refusé. Vous devez être journaliste pour consulter cette page.\n";
}

$conn->close();
?>