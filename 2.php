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

//creation de la table Journalistes
$sql = "CREATE TABLE Journalistes ( 

id INT AUTO_INCREMENT PRIMARY KEY,
username varchar(30),
passwd varchar(30),
)";

if ($conn->query($sql) === TRUE) {

    echo "Table des Journalistes créé avec succes.\n";
} else {
    echo "Erreur lors de la création de la table Journalistes: " . $conn->error;
}

//creation de la table Statistiques

$sql = "CREATE TABLE IF NOT EXISTS Statistiques (

id INT AUTO_INCREMENT PRIMARY KEY,
Combat INT,
Boxeur varchar(40),
Coups_Portés INT,
Coups_Encaissés INT,
Rounds INT,
Precision INT,
)";

if ($conn->query($sql) === TRUE) {

    echo "Table des Statistiques créé avec succes.\n";
} else {
    echo "Erreur lors de la création de la table Statistiques: " . $conn->error;
}


































