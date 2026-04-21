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
    <a href="users.php">Zarządzaj użytkownikami</a> |
    <a href="income.php">Dodaj dochód</a> |
    <a href="expenses.php">Dodaj wydatek</a>
</body>
</html>