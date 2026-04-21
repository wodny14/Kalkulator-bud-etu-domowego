let expensesChartInstance = null;
let donutChartInstance = null;

function drawCharts(barData, donutData) {
    // --- WYKRES SŁUPKOWY (HISTORIA) ---
    var ctxBar = document.getElementById('expensesChart').getContext('2d');
    var labelsBar = barData.map(d => d.month);
    var amountsBar = barData.map(d => d.amount);

    if (expensesChartInstance) expensesChartInstance.destroy();

    expensesChartInstance = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: labelsBar,
            datasets: [{
                label: 'Wydatki (PLN)',
                data: amountsBar,
                backgroundColor: 'rgba(255, 69, 58, 0.8)', // iOS Red
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(28, 28, 30, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    cornerRadius: 12,
                    displayColors: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(84, 84, 88, 0.2)', drawBorder: false },
                    ticks: { color: 'rgba(235, 235, 245, 0.6)' }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { color: 'rgba(235, 235, 245, 0.6)' }
                }
            },
            animation: { duration: 1000, easing: 'easeOutQuart' }
        }
    });

    // --- WYKRES KOŁOWY (KATEGORIE) ---
    var ctxDonut = document.getElementById('donutChart').getContext('2d');
    var labelsDonut = donutData.map(d => d.category);
    var amountsDonut = donutData.map(d => d.amount);

    // Paleta iOS
    var colors = ['#0A84FF', '#FF9F0A', '#30D158', '#BF5AF2', '#FF453A', '#5E5CE6', '#FF375F', '#64D2FF'];

    if (donutChartInstance) donutChartInstance.destroy();

    // Jeśli brak danych, nie rysuj pustego koła z błędem
    if(amountsDonut.length === 0) {
        document.getElementById('donutChart').style.display = 'none';
        var container = document.getElementById('donutChart').parentElement;
        if(!document.getElementById('noDataMsg')) {
            var msg = document.createElement('div');
            msg.id = 'noDataMsg';
            msg.style.cssText = 'height:100%; display:flex; align-items:center; justify-content:center; color:rgba(235,235,245,0.4);';
            msg.innerText = 'Brak danych z tego miesiąca';
            container.appendChild(msg);
        }
        return;
    }

    document.getElementById('donutChart').style.display = 'block';
    var existingMsg = document.getElementById('noDataMsg');
    if(existingMsg) existingMsg.remove();

    donutChartInstance = new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: labelsDonut,
            datasets: [{
                data: amountsDonut,
                backgroundColor: colors,
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%', // Cienki ładny okręg
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        color: 'rgba(235, 235, 245, 0.8)',
                        boxWidth: 12,
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(28, 28, 30, 0.9)',
                    padding: 12,
                    cornerRadius: 12
                }
            },
            animation: { animateScale: true, animateRotate: true, duration: 1200, easing: 'easeOutQuart' }
        }
    });
}

function animateValue(obj, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const easeProgress = 1 - Math.pow(1 - progress, 4); // easeOutQuart
        const current = start + (end - start) * easeProgress;
        
        let text = current.toFixed(2);
        if (obj.innerHTML.includes(' PLN')) {
            obj.innerHTML = text + ' PLN';
        } else if (obj.innerHTML.includes('%')) {
            obj.innerHTML = text + '%';
        } else {
            obj.innerHTML = text;
        }
        
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
            if (obj.innerHTML.includes(' PLN')) obj.innerHTML = end.toFixed(2) + ' PLN';
            else if (obj.innerHTML.includes('%')) obj.innerHTML = end.toFixed(2) + '%';
        }
    };
    window.requestAnimationFrame(step);
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('.count-up').forEach(el => {
        let text = el.innerText.replace(' PLN', '').replace('%', '').trim();
        const val = parseFloat(text);
        if (!isNaN(val)) {
            // Tymczasowo czyścimy przed startem
            if (el.innerText.includes(' PLN')) el.innerText = '0.00 PLN';
            else if (el.innerText.includes('%')) el.innerText = '0.00%';
            else el.innerText = '0';
            
            animateValue(el, 0, val, 1500);
        }
    });
});