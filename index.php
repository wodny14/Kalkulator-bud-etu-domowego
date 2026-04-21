<?php include 'functions.php'; ?>

<?php
$selectedUser = null;
if (isset($_GET['user'])) {
    $users = getUsers();
    foreach ($users as $user) {
        if ($user['id'] == $_GET['user']) {
            $selectedUser = $user;
            break;
        }
    }
}
if (!$selectedUser) {
    header('Location: family.php');
    exit;
}
?>

<html>
<head>
    <title>Dashboard - Kalkulator Budżetu Domowego</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body>

    <h1>Dashboard dla <?php echo $selectedUser['name']; ?> 😊</h1>
    <div class="box">
        <h2>Podsumowanie</h2>
        <p class="good">Suma przychodów: <?php echo getTotalIncomeForUser($selectedUser['id']); ?> PLN</p>
        <p class="bad">Suma wydatków: <?php echo getTotalExpensesForUser($selectedUser['id']); ?> PLN</p>
        <p class="<?php echo getBalanceForUser($selectedUser['id']) >= 0 ? 'good' : 'bad'; ?>">Saldo: <?php echo getBalanceForUser($selectedUser['id']); ?> PLN</p>
    </div>
    <div class="box">
        <h2>Budżet miesięczny (<?php echo date('Y-m'); ?>)</h2>
        <p>Budżet: <?php echo getBudget(); ?> PLN</p>
        <p class="bad">Wydano w tym miesiącu: <?php echo getSpentThisMonthForUser($selectedUser['id']); ?> PLN</p>
        <p class="<?php echo getRemainingForUser($selectedUser['id']) >= 0 ? 'good' : 'bad'; ?>">Pozostało: <?php echo getRemainingForUser($selectedUser['id']); ?> PLN</p>
        <p>Wykorzystanie: <?php echo round(getUsagePercentForUser($selectedUser['id']), 2); ?>%</p>
        <?php
        $percent = getUsagePercentForUser($selectedUser['id']);
        if ($percent >= 100) {
            echo "<p class='bad'>🚨 Przekroczono budżet!</p>";
        } elseif ($percent >= 80) {
            echo "<p style='color:orange;'>⚠️ Ostrzeżenie: Wykorzystano ponad 80% budżetu.</p>";
        }
        ?>
    </div>
    <div class="box">
        <h2>Wykres wydatków (ostatnie 6 miesięcy)</h2>
        <canvas id="expensesChart" width="400" height="200"></canvas>
    </div>
    <br>
    <a href="income.php?user=<?php echo $selectedUser['id']; ?>">Dodaj dochód 💰</a> |
    <a href="expenses.php?user=<?php echo $selectedUser['id']; ?>">Dodaj wydatek 🛒</a> |
    <a href="budget.php">Ustaw budżet 📊</a> |
    <a href="family.php">Zmień członka rodziny 👨‍👩‍👧‍👦</a>
    <script>
        var expensesData = <?php echo json_encode(getMonthlyExpensesForUser($selectedUser['id'])); ?>;
        drawChart(expensesData);
    </script>
</body>
</html>