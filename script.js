document.addEventListener("DOMContentLoaded", function () {
    // Sélectionne les liens et la zone de contenu
    const addStudentLink = document.getElementById("addStudentLink");
    const listStudentsLink = document.getElementById("listStudentsLink");
    const addVerstLink = document.getElementById("addVerstLink");
    const listVerstLink = document.getElementById("listVerstLink");
    const addNotesLink = document.getElementById("addNotesLink");
    const listNotestLink = document.getElementById("listNotestLink");
    
    const listCoursLink = document.getElementById("listCoursLink");
    const addBullLink = document.getElementById("addBullLink");
    // const listVerstLink = document.getElementById("listVerstLink");
    const mainContent = document.getElementById("mainContent");

    // Vérifie si tous les éléments existent
    if (!addStudentLink || !listStudentsLink || !addVerstLink || !listVerstLink || !mainContent) {
        console.error("Un ou plusieurs éléments nécessaires ne sont pas trouvés dans le DOM.");
        return;
    }
    console.log("Tous les éléments sont correctement trouvés.");

    // Fonction pour charger du contenu via AJAX
    function loadContent(url) {
        console.log("Chargement de la page : " + url); // Vérifiez l'URL
        fetch(url)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP : ${response.status}`); // Capture l'erreur HTTP
                }
                return response.text();
            })
            .then((html) => {
                console.log("Contenu chargé : ", html); // Vérifiez le contenu récupéré
                mainContent.innerHTML = html; // Injecte le contenu dans l'élément mainContent
            })
            .catch((error) => {
                console.error("Erreur lors du chargement :", error); // Affiche l'erreur dans la console
                mainContent.innerHTML = `<p>Une erreur est survenue lors du chargement de la page.</p>`; // Affiche un message d'erreur dans mainContent
            });
    }

    // Gestionnaires d'événements pour chaque lien
    addStudentLink.addEventListener("click", (e) => {
        e.preventDefault();
        loadContent("ajout_Etud.php"); // Charge la page ajout_Etud.php
    });

    listStudentsLink.addEventListener("click", (e) => {
        e.preventDefault();
        loadContent("lister_et.php"); // Charge la page lister_Etud.php
    });

    addVerstLink.addEventListener("click", (e) => {
        e.preventDefault();
        loadContent("versform.php"); // Charge la page versform.php
    });

    listVerstLink.addEventListener("click", (e) => {
        e.preventDefault();
        loadContent("lister_vers.php"); // Charge la page lister_vers.php
    });

    addNotesLink.addEventListener("click", (e) => {
        e.preventDefault();
        loadContent("ajouter_notes.php"); // Charge la page ajout_Etud.php
    });

    listNotesLink.addEventListener("click", (e) => {
        e.preventDefault();
        loadContent("liste_notes.php"); // Charge la page lister_vers.php
    });
  
    

    addBullLink.addEventListener("click", (e) => {
        e.preventDefault();
        loadContent("generer_bulletins.php"); // Charge la page lister_vers.php
    });

    // Affiche la date actuelle
    const dateElement = document.getElementById("currentDate");
    if (dateElement) {
        const currentDate = new Date().toLocaleDateString("fr-FR", {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric",
        });
        dateElement.textContent = currentDate; // Affiche la date au format français
    }

});


document.addEventListener("DOMContentLoaded", function () {
    // Sélectionner les liens et la zone de contenu
    const addBullLink = document.getElementById("addBullLink");
    const addCoursLink = document.getElementById("addCoursLink");
    const listCoursLink = document.getElementById("listCoursLink");
    const listNotesLink = document.getElementById("listNotesLink");
    const listBullLink = document.getElementById("listBullLink");
    const mainContent = document.getElementById("mainContent");

    // Vérifie si tous les éléments existent
    if (!addBullLink || !mainContent) {
        console.error("Un ou plusieurs éléments nécessaires ne sont pas trouvés dans le DOM.");
        return;
    }

    console.log("Tous les éléments sont correctement trouvés.");

    // Fonction pour charger du contenu via AJAX
    function loadContent(url) {
        console.log("Chargement de la page : " + url); // Vérifiez l'URL
        fetch(url)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP : ${response.status}`); // Capture l'erreur HTTP
                }
                return response.text();
            })
            .then((html) => {
                console.log("Contenu chargé : ", html); // Vérifiez le contenu récupéré
                mainContent.innerHTML = html; // Injecte le contenu dans l'élément mainContent
            })
            .catch((error) => {
                console.error("Erreur lors du chargement :", error); // Affiche l'erreur dans la console
                mainContent.innerHTML = `<p>Une erreur est survenue lors du chargement de la page.</p>`; // Affiche un message d'erreur dans mainContent
            });
    }

    // Gestion de l'événement sur le lien "Generer les Bulletins"
    addBullLink.addEventListener("click", (e) => {
        e.preventDefault();  // Empêche le lien de recharger la page
        loadContent("generer_bulletins.php"); // Charge le contenu de "generer_bulletins.php" dans la zone "mainContent"
    });
    addCoursLink.addEventListener("click", (e) => {
        e.preventDefault();  // Empêche le lien de recharger la page
        loadContent("ajouter_cours.php"); // Charge le contenu de "generer_bulletins.php" dans la zone "mainContent"
    });

    listCoursLink.addEventListener("click", (e) => {
        e.preventDefault();  // Empêche le lien de recharger la page
        loadContent("liste_cours.php"); // Charge le contenu de "generer_bulletins.php" dans la zone "mainContent"
    });
    listNotesLink.addEventListener("click", (e) => {
        e.preventDefault();  // Empêche le lien de recharger la page
        loadContent("liste_notes.php"); // Charge le contenu de "generer_bulletins.php" dans la zone "mainContent"
    });
    listBullLink.addEventListener("click", (e) => {
        e.preventDefault();  // Empêche le lien de recharger la page
        loadContent("liste_bulletins.php"); // Charge le contenu de "generer_bulletins.php" dans la zone "mainContent"
    });

    
});

document.addEventListener("DOMContentLoaded", function () {

    const listStats = document.getElementById("listStats");
    const mainContent = document.getElementById("mainContent");
     // Vérifie si tous les éléments existent
     if (!addBullLink || !mainContent) {
        console.error("Un ou plusieurs éléments nécessaires ne sont pas trouvés dans le DOM.");
        return;
    }
    
    console.log("Tous les éléments sont correctement trouvés.");
    
    // Fonction pour charger du contenu via AJAX
    function loadContent(url) {
        console.log("Chargement de la page : " + url); // Vérifiez l'URL
        fetch(url)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP : ${response.status}`); // Capture l'erreur HTTP
                }
                return response.text();
            })
            .then((html) => {
                console.log("Contenu chargé : ", html); // Vérifiez le contenu récupéré
                mainContent.innerHTML = html; // Injecte le contenu dans l'élément mainContent
            })
            .catch((error) => {
                console.error("Erreur lors du chargement :", error); // Affiche l'erreur dans la console
                mainContent.innerHTML = `<p>Une erreur est survenue lors du chargement de la page.</p>`; // Affiche un message d'erreur dans mainContent
            });
    }
    
    listStats.addEventListener("click", (e) => {
        e.preventDefault();  // Empêche le lien de recharger la page
        loadContent("Statistiques.php"); // Charge le contenu de "generer_bulletins.php" dans la zone "mainContent"
    });
});
    

function searchTable() {
    var input, filter, table, tr, td, i, j, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase(); // Convertir l'entrée en majuscule
    table = document.querySelector(".table"); // Sélectionner le tableau
    tr = table.getElementsByTagName("tr"); // Récupérer toutes les lignes du tableau

    // Parcours de toutes les lignes du tableau, en commençant à 1 pour ignorer les en-têtes
    for (i = 1; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td"); // Récupérer toutes les cellules de la ligne
        var matchFound = false; // Flag pour savoir si on trouve une correspondance

        // Parcours des cellules de la ligne
        for (j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText; // Récupérer le texte de la cellule
                if (txtValue.toUpperCase().indexOf(filter) > -1) { // Si une correspondance est trouvée
                    matchFound = true;
                    break; // Pas besoin de continuer à vérifier d'autres cellules
                }
            }
        }

        // Afficher ou cacher la ligne en fonction de la correspondance trouvée
        tr[i].style.display = matchFound ? "" : "none";
    }
}
// Appel pour récupérer les données



document.addEventListener('DOMContentLoaded', () => {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const currentDateElement = document.getElementById('currentDate');
  
    if (!darkModeToggle || !currentDateElement) {
      console.error('Required elements not found in the DOM.');
      return;
    }
  
    // Initialize theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateToggleIcon(savedTheme);
  
    // Toggle theme on button click
    darkModeToggle.addEventListener('click', toggleTheme);
  
    // Update current date
    updateCurrentDate(currentDateElement);
  
    // Observe theme changes for chart updates
    const observer = new MutationObserver(() => {
      updateChartsTheme();
    });
  
    observer.observe(document.documentElement, {
      attributes: true,
      attributeFilter: ['data-theme']
    });
  });
  
  function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateToggleIcon(newTheme);
  }
  
  function updateToggleIcon(theme) {
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
      darkModeToggle.textContent = theme === 'light' ? '🌙' : '☀️';
    }
  }
  
  function updateCurrentDate(element) {
    const currentDate = new Date().toLocaleDateString('fr-FR', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
    element.textContent = currentDate;
  }
  
  function updateChartsTheme() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#f3f4f6' : '#111827';
    
    Chart.defaults.color = textColor;
    Chart.defaults.borderColor = isDark ? '#374151' : '#e5e7eb';
    
    // Update all existing charts
    if (Chart.instances && Chart.instances.length > 0) {
      Chart.instances.forEach(chart => chart.update());
    }
  }