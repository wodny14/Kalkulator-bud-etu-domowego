// Funkcja do rysowania wykresu wydatków miesięcznych za pomocą Chart.js
let expensesChartInstance = null;

function drawChart(data) {
    var ctx = document.getElementById('expensesChart').getContext('2d');
    
    var labels = data.map(d => d.month);
    var amounts = data.map(d => d.amount);

    if (expensesChartInstance) {
        expensesChartInstance.destroy();
    }

    expensesChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Wydatki (PLN)',
                data: amounts,
                backgroundColor: 'rgba(255, 69, 58, 0.8)', // iOS Red
                borderColor: 'rgba(255, 69, 58, 1)',
                borderWidth: 0,
                borderRadius: 8, // Zaokrąglone słupki jak w iOS
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // Ukrywamy legendę dla czystszego wyglądu
                },
                tooltip: {
                    backgroundColor: 'rgba(28, 28, 30, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    cornerRadius: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' PLN';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(84, 84, 88, 0.2)', // Delikatne linie
                        drawBorder: false,
                    },
                    ticks: {
                        color: 'rgba(235, 235, 245, 0.6)'
                    }
                },
                x: {
                    grid: {
                        display: false, // Ukrywamy pionowe linie
                        drawBorder: false,
                    },
                    ticks: {
                        color: 'rgba(235, 235, 245, 0.6)'
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            }
        }
    });
}