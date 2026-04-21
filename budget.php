<?php include 'functions.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = floatval($_POST['amount']);
    if ($amount > 0) {
        setBudget($amount);
        echo "<p>Budżet ustawiony.</p>";
    } else {
        echo "<p>Błąd: Kwota musi być większa od 0.</p>";
    }
}
?>

<html>
<head>
    <title>Budżet - Kalkulator Budżetu Domowego</title>
</head>
<body>
    <h1>Ustaw budżet miesięczny</h1>
    <form method="post">
        <label for="amount">Kwota budżetu:</label>
        <input type="number" step="0.01" id="amount" name="amount" required>
        <button type="submit">Ustaw budżet</button>
    </form>
    <br>
    <h2>Stan budżetu (<?php echo date('Y-m'); ?>)</h2>
    <p>Budżet: <?php echo getBudget(); ?> PLN</p>
    <p>Wydano: <?php echo getSpentThisMonth(); ?> PLN</p>
    <p>Pozostało: <?php echo getRemaining(); ?> PLN</p>
    <p>Wykorzystanie: <?php echo round(getUsagePercent(), 2); ?>%</p>
    <br>
    <a href="index.php">Powrót do dashboard</a>
</body>
</html>