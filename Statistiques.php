

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

    <main class="main-content">
      <h1>Statistiques </h1>

      <!-- Section : Graphiques et Rapports -->
      <section class="charts-section">
        <h2>Graphiques</h2>
        <div class="charts-container">
          <!-- Camembert -->
          <div class="chart">
            <h3>Taux de Solvabilite</h3>
            <canvas id="solvabiliteChart"></canvas>
          </div>

          <div class="chart">
            <h3>Taux de Versements</h3>
            <canvas id="versementsParJourChart"></canvas>
          </div>

          <!-- Histogramme Etud -->
          <div class="chart">
            <h3>Nombres d'etudiants</h3>
            <canvas id="etudiantsParNiveauChart"></canvas>
          </div>

            <!-- Courbe des Notes -->
            <div class="chart">
            <h3>Mentions par classe</h3>
            <canvas id="mentionsParNiveauChart"></canvas>
          


            <!-- <div class="chart">
            <h3>Tendance des Versements </h3>
            <canvas id="tendanceVersementsChart"></canvas>
          </div>

      
       
          
          <div class="chart">
            <h3>Nombres de notes pas cours</h3>
            <canvas id="coursParNotesChart"></canvas>
          </div> -->

       
 
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
        function createOrUpdateChart(chartId, chartType, labels, datasets) {
            const chartElement = document.getElementById(chartId);
            let chart = Chart.getChart(chartId);

            if (chart) {
                chart.data.labels = labels;
                chart.data.datasets = datasets;
                chart.update();
            } else {
                chart = new Chart(chartElement, {
                    type: chartType,
                    data: {
                        labels: labels,
                        datasets: datasets
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

                createOrUpdateChart('solvabiliteChart', 'pie', labels, datasets);
            } catch (error) {
                console.error('Erreur lors de la mise à jour du graphique de solvabilité:', error);
            }
        }

        // Étudiants par Niveau Chart
              // Étudiants par Niveau Chart
              async function updateEtudiantsParNiveauChart() {
            try {
                const data = await fetchData('etudiants_par_niveau');
                const labels = data.map(d => d.Niveau);
                const nombreEtudiants = data.map(d => d.nombre_etudiants);
                const backgroundColors = ['#FF6384', '#36A2EB', '#FFCE56'];

                const datasets = [{
                    label: 'Nombre d\'Étudiants',
                    data: nombreEtudiants,
                    backgroundColor: backgroundColors
                }];

                createOrUpdateChart('etudiantsParNiveauChart', 'bar', labels, datasets);
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

                createOrUpdateChart('mentionsParNiveauChart', 'bar', niveaux, datasets);
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
                const backgroundColors = 'rgba(103, 61, 187, 0.6)';

                const datasets = [{
                    label: 'Nombre de Notes',
                    data: nombreNotes,
                    backgroundColor: backgroundColors
                }];

                createOrUpdateChart('coursParNotesChart', 'bar', labels, datasets);
            } catch (error) {
                console.error('Erreur lors de la mise à jour du graphique des cours par notes:', error);
            }
        }

        // Versements par Jour Chart
        async function updateVersementsParJourChart() {
            try {
                const data = await fetchData('versements_par_jour');
                console.log('Données des versements par jour:', data); // Ajoutez ce log pour vérifier les données
                const labels = data.map(d => d.date);
                const totalVersements = data.map(d => d.total_versements);
                const backgroundColors = 'rgba(214, 54, 235, 0.6)';

                const datasets = [{
                    label: 'Total des Versements',
                    data: totalVersements,
                    backgroundColor: backgroundColors
                }];

                createOrUpdateChart('versementsParJourChart', 'bar', labels, datasets);
            } catch (error) {
                console.error('Erreur lors de la mise à jour du graphique des versements par jour:', error);
            }
        }

        // Tendance des Versements par Jour Chart
        async function updateTendanceVersementsChart() {
            try {
                const data = await fetchData('tendance_versements');
                console.log('Données de la tendance des versements par jour:', data); // Ajoutez ce log pour vérifier les données
                const labels = data.map(d => d.date);
                const totalVersements = data.map(d => d.total_versements);
                const backgroundColors = 'rgba(54, 162, 235, 0.6)';

                const datasets = [{
                    label: 'Tendance des Versements',
                    data: totalVersements,
                    backgroundColor: backgroundColors,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4
                }];

                const options = {
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'day'
                            },
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total des Versements'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                };

                createOrUpdateChart('tendanceVersementsChart', 'line', labels, datasets, options);
            } catch (error) {
                console.error('Erreur lors de la mise à jour du graphique de la tendance des versements par jour:', error);
            }
        }

        // Fonction pour mettre à jour tous les graphiques
        async function updateAllCharts() {
            await updateSolvabiliteChart();
            await updateEtudiantsParNiveauChart();
            await updateMentionsParNiveauChart();
            await updateCoursParNotesChart();
            await updateTendanceVersementsChart();
        }

        // Mettre à jour les graphiques toutes les 10 secondes
        setInterval(updateAllCharts, 10000);

        // Charger les graphiques initialement
        updateAllCharts();

    </script>
  
</body>

</html>