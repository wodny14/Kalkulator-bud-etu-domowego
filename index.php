<?php include 'functions.php'; ?>

<html>
<head>
    <title>Dashboard - Kalkulator Budżetu Domowego</title>
</head>
<body>
    <h1>Dashboard</h1>
    <p>Suma przychodów: <?php echo getTotalIncome(); ?> PLN</p>
    <p>Suma wydatków: <?php echo getTotalExpenses(); ?> PLN</p>
    <p>Saldo: <?php echo getBalance(); ?> PLN</p>
    <br>
    <h2>Budżet miesięczny (<?php echo date('Y-m'); ?>)</h2>
    <p>Budżet: <?php echo getBudget(); ?> PLN</p>
    <p>Wydano w tym miesiącu: <?php echo getSpentThisMonth(); ?> PLN</p>
    <p>Pozostało: <?php echo getRemaining(); ?> PLN</p>
    <p>Wykorzystanie: <?php echo round(getUsagePercent(), 2); ?>%</p>
    <?php
    $percent = getUsagePercent();
    if ($percent >= 100) {
        echo "<p style='color:red;'>Przekroczono budżet!</p>";
    } elseif ($percent >= 80) {
        echo "<p style='color:orange;'>Ostrzeżenie: Wykorzystano ponad 80% budżetu.</p>";
    }
    ?>
    <br>
    <a href="users.php">Zarządzaj użytkownikami</a> |
    <a href="income.php">Dodaj dochód</a> |
    <a href="expenses.php">Dodaj wydatek</a> |
    <a href="budget.php">Ustaw budżet</a>
</body>
</html>