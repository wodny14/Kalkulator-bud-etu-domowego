// Funkcja do rysowania wykresu wydatków miesięcznych
function drawChart(data) {
    var canvas = document.getElementById('expensesChart');
    if (!canvas) return;
    var ctx = canvas.getContext('2d');
    var width = canvas.width;
    var height = canvas.height;
    var barWidth = width / data.length - 10;
    var maxAmount = Math.max(...data.map(d => d.amount));

    ctx.clearRect(0, 0, width, height);
    ctx.fillStyle = '#f44336'; // Czerwony dla wydatków

    data.forEach((item, index) => {
        var barHeight = (item.amount / maxAmount) * (height - 40);
        var x = index * (barWidth + 10) + 5;
        var y = height - barHeight - 20;
        ctx.fillRect(x, y, barWidth, barHeight);

        // Etykieta miesiąca
        ctx.fillStyle = '#ffffff';
        ctx.font = '12px Arial';
        ctx.fillText(item.month, x, height - 5);
        ctx.fillText(item.amount + ' PLN', x, y - 5);
        ctx.fillStyle = '#f44336';
    });
}