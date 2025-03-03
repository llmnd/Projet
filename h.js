
    const matchsContainer = document.querySelector(".matchs-container");
    const matchs = [
        { boxeur1: "Mike Tyson", boxeur2: "Muhammad Ali", date: "10 Mars 2025",  },
        { boxeur1: "Floyd ", boxeur2: "Manny ", date: "15 Mars 2025" }
    ];

    matchs.forEach(match => {
        const matchElement = document.createElement("div");
        matchElement.classList.add("match");
        matchElement.innerHTML = `<strong>${match.boxeur1}</strong> vs <strong>${match.boxeur2}</strong> - ${match.date}`;
        matchsContainer.appendChild(matchElement);
    });

    // Simuler des données pour le classement
    const classementContainer = document.querySelector(".classement-container");
const classement = [
    { rang: 1, nom: "Mike Tyson", victoires: 50, defaites: 6 },
    { rang: 2, nom: "Muhammad Ali", victoires: 56, defaites: 5},
    { rang: 3, nom: "Hmmm", victoires: 50, defaites: 7 }
];

classement.forEach(boxeur => {
    const boxeurElement = document.createElement("div");
    boxeurElement.classList.add("classement-boxeur");

    boxeurElement.innerHTML = `
        <h3>${boxeur.nom}</h3>
        <p>Rang: ${boxeur.rang}</p>
        <p>Victoires: ${boxeur.victoires}</p>
        <p>Défaites: ${boxeur.defaites}</p>
    `;

    classementContainer.appendChild(boxeurElement);
});

    