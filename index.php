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
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pulpit - Kalkulator Budżetu Domowego</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <nav>
        <ul>
            <li><a href="index.php?user=<?php echo $selectedUser['id']; ?>" class="active">Pulpit</a></li>
            <li><a href="income.php?user=<?php echo $selectedUser['id']; ?>">Dochody</a></li>
            <li><a href="expenses.php?user=<?php echo $selectedUser['id']; ?>">Wydatki</a></li>
            <li><a href="budget.php">Budżet</a></li>
            <li><a href="family.php">Zmień osobę</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Witaj, <?php echo $selectedUser['name']; ?> 👋</h1>
        
        <div class="grid">
            <div class="box">
                <h2>Podsumowanie Ogólne</h2>
                <div class="stat-item">
                    <span>Suma przychodów:</span>
                    <span class="badge good"><?php echo getTotalIncomeForUser($selectedUser['id']); ?> PLN</span>
                </div>
                <div class="stat-item">
                    <span>Suma wydatków:</span>
                    <span class="badge bad"><?php echo getTotalExpensesForUser($selectedUser['id']); ?> PLN</span>
                </div>
                <div class="stat-item">
                    <span>Saldo:</span>
                    <span class="badge <?php echo getBalanceForUser($selectedUser['id']) >= 0 ? 'good' : 'bad'; ?>">
                        <?php echo getBalanceForUser($selectedUser['id']); ?> PLN
                    </span>
                </div>
            </div>

            <div class="box">
                <h2>Budżet miesięczny (<?php echo date('Y-m'); ?>)</h2>
                <div class="stat-item">
                    <span>Całkowity budżet:</span>
                    <span><?php echo getBudget(); ?> PLN</span>
                </div>
                <div class="stat-item">
                    <span>Wydano w tym miesiącu:</span>
                    <span class="badge bad"><?php echo getSpentThisMonthForUser($selectedUser['id']); ?> PLN</span>
                </div>
                <div class="stat-item">
                    <span>Pozostało:</span>
                    <span class="badge <?php echo getRemainingForUser($selectedUser['id']) >= 0 ? 'good' : 'bad'; ?>">
                        <?php echo getRemainingForUser($selectedUser['id']); ?> PLN
                    </span>
                </div>
                <div class="stat-item" style="border-bottom:none; margin-top:0.5rem; justify-content: center; flex-direction: column;">
                    <div style="width: 100%; background: var(--bg-color); height: 8px; border-radius: 4px; overflow: hidden; position: relative;">
                        <?php
                            $percent = getUsagePercentForUser($selectedUser['id']);
                            $barColor = $percent >= 100 ? 'var(--danger-color)' : ($percent >= 80 ? 'var(--warning-color)' : 'var(--success-color)');
                            $width = min($percent, 100);
                        ?>
                        <div style="width: <?php echo $width; ?>%; background: <?php echo $barColor; ?>; height: 100%;"></div>
                    </div>
                    <div style="width: 100%; text-align: right; font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">
                        Wykorzystano: <?php echo round($percent, 2); ?>%
                    </div>
                </div>
                <?php
                if ($percent >= 100) {
                    echo "<div class='alert alert-danger' style='margin-top:1rem; margin-bottom:0;'>🚨 Przekroczono budżet!</div>";
                } elseif ($percent >= 80) {
                    echo "<div class='alert' style='background: var(--warning-bg); color: var(--warning-color); border: 1px solid rgba(245, 158, 11, 0.2); margin-top:1rem; margin-bottom:0;'>⚠️ Wykorzystano ponad 80% budżetu.</div>";
                }
                ?>
            </div>
        </div>

        <div class="chart-container">
            <h2>Wykres wydatków (ostatnie 6 miesięcy)</h2>
            <canvas id="expensesChart" width="100%" height="300" style="max-height: 300px; width: 100%; display: block;"></canvas>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var expensesData = <?php echo json_encode(getMonthlyExpensesForUser($selectedUser['id'])); ?>;
            if(typeof drawChart === 'function') {
                drawChart(expensesData);
            }
        });
    </script>
</body>
</html>