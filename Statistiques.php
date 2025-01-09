
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sidebar</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <!-- Barre latérale -->
  <div class="sidebar">
    <div class="profile">
      <ion-icon name="person-outline"></ion-icon>
    </div>
    <ul>
      <span>Statistiques</span>
      <li>
        <a href="dashadmin.php"><ion-icon name="home-outline"></ion-icon>
          <p>Dashboard</p>
        </a>
      </li>
      <li>
        <a href="#"><ion-icon name="pie-chart-outline"></ion-icon>
          <p>Graphes</p>
        </a>
      </li>
    </ul>
    <ul>
      <span>Gestion des etudiants</span>
      <li>
        <a href="ajout_Etud.php" id="addStudentLink"><ion-icon name="person-add-outline"></ion-icon>
          <p>Ajouter un Etudiant</p>
        </a>
      </li>
      <li>
        <a href="lister_et.php" id="listStudentsLink"><ion-icon name="list-outline"></ion-icon>
          <p>Lister les Etudiants</p>
        </a>
      </li>
    </ul>
    <ul>
      <span>Gestion des Versements</span>
      <li class="">
        <a href="versform.php" id="addVerstLink"><ion-icon name="bag-add-outline"></ion-icon></ion-icon>
          <p>Ajouter un Versement</p>
        </a>
      </li>
      <li>
        <a href="lister_vers.php" id="listVerstLink"><ion-icon name="list-outline"></ion-icon>
          <p>Lister les Versements</p>
        </a>
      </li>
    </ul>
    <ul>
      <span>Gestion des Cours</span>
      <li class="">
      <a href="ajouter_cours.php" id="addCoursLink"><ion-icon name="bag-add-outline"></ion-icon></ion-icon>
          <p>Ajouter des Cours</p>
        </a>
      </li>
      <li>
        <a href="liste_cours.php" id="listCoursLink"><ion-icon name="book-outline"></ion-icon>
          <p>Lister les Cours</p>
        </a>
      </li>
    </ul>
    <ul>
      <span>Gestion des Notes</span>
      <li class="">
      <a href="ajouter_notes.php" id="addNotesLink"><ion-icon name="bag-add-outline"></ion-icon></ion-icon>
          <p>Ajouter des Notes</p>
        </a>
      </li>
      <li>
        <a href="liste_notes.php" id="ListNotesLink"><ion-icon name="book-outline"></ion-icon>
          <p>Lister les Notes</p>
        </a>
      </li>
    </ul>
    <ul>
      <span>Gestion des Bulletins</span>
      <li>
        <a href="#"><ion-icon name="albums-outline"></ion-icon>
          <p>Lister les Bulletins</p>
        </a>
      </li>
    </ul>
    <ul>
      <span>Custom</span>
      <li class="switch-theme">
        <a href="#"><ion-icon name="moon-outline"></ion-icon>
          <p>Darkmode</p>
          <button id="darkModeToggle">
            <div class="circle"></div>
          </button>
        </a>
      </li>
      <li>
        <a href="#"><ion-icon name="log-out-outline"></ion-icon>
          <p>Logout</p>
        </a>
      </li>
    </ul>
  </div>

  <!-- Contenu principal -->
  <div class="main-content" id="mainContent">
    <!-- Header -->
    <header class="main-header">
      <div class="logo">
        <img src="images/logoK.ico" alt="Logo" />
        <h1>Gestion des Étudiants</h1>
      </div>
      <div class="info">
        <p>Date : <span id="currentDate"></span></p>
      </div>
      <div class="search-bar">
        <form class="search-form d-flex align-items-center" method="POST" action="#">
          <input type="text" name="query" placeholder="Search" title="Enter search keyword">
          <button type="submit" title="Search"><i class="bi bi-search"></i></button>
        </form>
      </div>
    </header>

    <main class="main-content">
      <h2>BIENVENUE DANS LE GESTIONNAIRE DES ETUDIANTS DE KEYCE!</h2>
      <h2>Choisissez une action dans le menu</h2>
      <!-- Section : Statistiques clés -->
      <!-- Section : Graphiques et Rapports -->
       <!-- Section : Graphiques et Rapports -->
       <section class="charts-section">
        <h2>Graphiques et Rapports</h2>
        <div class="charts-container">
          <!-- Camembert -->
          <div class="chart">
            <h3>Paiements</h3>
            <canvas id="solvabiliteChart"></canvas>
          </div>
          <!-- Histogramme Absences -->
          <div class="chart">
            <h3>Nombres d'etudiants</h3>
            <canvas id="etudiantsParNiveauChart"></canvas>
          </div>
          <!-- Courbe des Notes -->
          <div class="chart">
            <h3>Mentions pas classe</h3>
            <canvas id="mentionsParNiveauChart"></canvas>
          </div>
          <!-- Classe avec le plus d’étudiants -->
          <div class="chart">
            <h3>Nombres de notes pas cours</h3>
            <canvas id="coursParNotesChart"></canvas>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <script src="script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
        // Fonction générique pour récupérer les données
        async function fetchData(statType) {
            const response = await fetch(`get_stats.php?stat=${statType}`);
            if (!response.ok) {
                throw new Error(`Erreur de réseau: ${response.statusText}`);
            }
            const data = await response.json();
            console.log(`Données pour ${statType}:`, data); // Ajoutez ce log pour vérifier les données
            return data;
        }

        // Fonction pour créer ou mettre à jour un graphique
        function createOrUpdateChart(chartId, chartType, labels, data, backgroundColors) {
            const chartElement = document.getElementById(chartId);
            let chart = Chart.getChart(chartId);

            if (chart) {
                chart.data.labels = labels;
                chart.data.datasets[0].data = data;
                chart.data.datasets[0].backgroundColor = backgroundColors;
                chart.update();
            } else {
                chart = new Chart(chartElement, {
                    type: chartType,
                    data: {
                        labels: labels,
                        datasets: [{
                            label: chartId,
                            data: data,
                            backgroundColor: backgroundColors
                        }]
                    }
                });
            }
        }

        // Solvabilité Chart
        async function updateSolvabiliteChart() {
            try {
                const data = await fetchData('solvabilite');
                const solvableCount = data.filter(d => d.categorie === 'Solvable').length;
                const insolvableCount = data.filter(d => d.categorie === 'Insolvable').length;
                const enCoursCount = data.filter(d => d.categorie === 'En Cours').length;
                

                const labels = ['Solvable', 'Insolvable', 'En Cours'];
                const counts = [solvableCount, insolvableCount, enCoursCount];
                const backgroundColors = ['rgba(75, 192, 192, 0.6)', 'rgba(255, 99, 132, 0.6)', 'rgba(255, 205, 86, 0.6)'];

                const datasets = [{
                    label: 'Taux de Solvabilité',
                    data: counts,
                    backgroundColor: backgroundColors
                }];

                createOrUpdateChart('solvabiliteChart', 'pie', labels, counts, backgroundColors);
            } catch (error) {
                console.error('Erreur lors de la mise à jour du graphique de solvabilité:', error);
            }
        }

        // Étudiants par Niveau Chart
        async function updateEtudiantsParNiveauChart() {
            try {
                const data = await fetchData('etudiants_par_niveau');
                const labels = data.map(d => d.Niveau);
                const nombreEtudiants = data.map(d => d.nombre_etudiants);
                const backgroundColors = ['#FF6384', '#36A2EB', '#FFCE56'];

                createOrUpdateChart('etudiantsParNiveauChart', 'bar', labels, nombreEtudiants, backgroundColors);
            } catch (error) {
                console.error('Erreur lors de la mise à jour du graphique des étudiants par niveau:', error);
            }
        }

        // Mentions par Niveau Chart
        async function updateMentionsParNiveauChart() {
            try {
                const data = await fetchData('mentions_par_niveau');
                const niveaux = [...new Set(data.map(d => d.Niveau))];
                const mentions = [...new Set(data.map(d => d.mention))];

                const datasets = mentions.map(mention => ({
                    label: mention,
                    data: niveaux.map(niveau => {
                        const niveauData = data.find(d => d.Niveau === niveau && d.mention === mention);
                        return niveauData ? niveauData.nombre_mentions : 0;
                    }),
                    backgroundColor: '#' + Math.floor(Math.random() * 16777215).toString(16)
                }));

                const labels = niveaux;
                const dataArray = datasets.map(dataset => dataset.data);
                const backgroundColors = datasets.map(dataset => dataset.backgroundColor);

                createOrUpdateChart('mentionsParNiveauChart', 'bar', labels, dataArray, backgroundColors);
            } catch (error) {
                console.error('Erreur lors de la mise à jour du graphique des mentions par niveau:', error);
            }
        }

        // Cours par Notes Chart
        async function updateCoursParNotesChart() {
            try {
                const data = await fetchData('cours_par_notes');
                const labels = data.map(d => d.nom_cours);
                const nombreNotes = data.map(d => d.nombre_notes);
                const backgroundColors = 'rgba(153, 102, 255, 0.6)';

                createOrUpdateChart('coursParNotesChart', 'bar', labels, nombreNotes, backgroundColors);
            } catch (error) {
                console.error('Erreur lors de la mise à jour du graphique des cours par notes:', error);
            }
        }

        // Fonction pour mettre à jour tous les graphiques
        async function updateAllCharts() {
            await updateSolvabiliteChart();
            await updateEtudiantsParNiveauChart();
            await updateMentionsParNiveauChart();
            await updateCoursParNotesChart();
        }

        // Mettre à jour les graphiques toutes les 10 secondes
        setInterval(updateAllCharts, 10000);

        // Charger les graphiques initialement
        updateAllCharts();
    </script>
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>Keyce</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      Designed by Groupe 7
    </div>
  </footer>
</body>

</html>
