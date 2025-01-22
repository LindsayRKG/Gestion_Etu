document.addEventListener('DOMContentLoaded', function() {
    // Gestion du thème sombre
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    
    // Vérifier le thème enregistré
    const savedTheme = localStorage.getItem('theme') || 'dark';
    body.setAttribute('data-theme', savedTheme);
    
    themeToggle.addEventListener('click', () => {
        const currentTheme = body.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        body.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        // Mettre à jour les graphiques pour le nouveau thème
        updateChartsTheme(newTheme);
    });

    // Animation des cartes au défilement
    const observerOptions = {
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.stat-card, .chart-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        observer.observe(card);
    });

    // Gestion du menu responsive
    const menuToggle = document.createElement('button');
    menuToggle.className = 'menu-toggle';
    menuToggle.innerHTML = '<i class="ph ph-list"></i>';
    document.querySelector('.main-header').prepend(menuToggle);

    menuToggle.addEventListener('click', () => {
        document.querySelector('.sidebar').classList.toggle('active');
    });

    // Mise à jour de la date
    function updateDate() {
        const dateElement = document.querySelector('.date');
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const date = new Date().toLocaleDateString('fr-FR', options);
        dateElement.textContent = date.charAt(0).toUpperCase() + date.slice(1);
    }

    updateDate();
    setInterval(updateDate, 1000 * 60); // Mise à jour toutes les minutes
});

// Fonction pour mettre à jour le thème des graphiques
function updateChartsTheme(theme) {
    const chartConfig = {
        light: {
            color: '#18181b',
            backgroundColor: '#ffffff',
            borderColor: '#e4e4e7',
            gridColor: 'rgba(24, 24, 27, 0.1)'
        },
        dark: {
            color: '#f4f4f5',
            backgroundColor: '#27272a',
            borderColor: '#3f3f46',
            gridColor: 'rgba(244, 244, 245, 0.1)'
        }
    };

    const config = chartConfig[theme];
    
    Chart.defaults.color = config.color;
    Chart.defaults.borderColor = config.borderColor;

    // Mettre à jour tous les graphiques existants
    Chart.instances.forEach(chart => {
        chart.options.plugins.legend.labels.color = config.color;
        if (chart.options.scales) {
            Object.values(chart.options.scales).forEach(scale => {
                scale.grid.color = config.gridColor;
                scale.ticks.color = config.color;
            });
        }
        chart.update();
    });
}