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
</head>
<body>
    <h1>Dashboard dla <?php echo $selectedUser['name']; ?> 😊</h1>
    <p>Suma przychodów: <?php echo getTotalIncomeForUser($selectedUser['id']); ?> PLN</p>
    <p>Suma wydatków: <?php echo getTotalExpensesForUser($selectedUser['id']); ?> PLN</p>
    <p>Saldo: <?php echo getBalanceForUser($selectedUser['id']); ?> PLN</p>
    <br>
    <h2>Budżet miesięczny (<?php echo date('Y-m'); ?>)</h2>
    <p>Budżet: <?php echo getBudget(); ?> PLN</p>
    <p>Wydano w tym miesiącu: <?php echo getSpentThisMonthForUser($selectedUser['id']); ?> PLN</p>
    <p>Pozostało: <?php echo getRemainingForUser($selectedUser['id']); ?> PLN</p>
    <p>Wykorzystanie: <?php echo round(getUsagePercentForUser($selectedUser['id']), 2); ?>%</p>
    <?php
    $percent = getUsagePercentForUser($selectedUser['id']);
    if ($percent >= 100) {
        echo "<p style='color:red;'>🚨 Przekroczono budżet!</p>";
    } elseif ($percent >= 80) {
        echo "<p style='color:orange;'>⚠️ Ostrzeżenie: Wykorzystano ponad 80% budżetu.</p>";
    }
    ?>
    <br>
    <a href="income.php?user=<?php echo $selectedUser['id']; ?>">Dodaj dochód 💰</a> |
    <a href="expenses.php?user=<?php echo $selectedUser['id']; ?>">Dodaj wydatek 🛒</a> |
    <a href="budget.php">Ustaw budżet 📊</a> |
    <a href="family.php">Zmień członka rodziny 👨‍👩‍👧‍👦</a>
</body>
</html>