<?php include 'functions.php'; ?>

<?php
$isGlobal = !isset($_GET['user']);
$selectedUser = null;
$users = getUsers();

if (!$isGlobal) {
    foreach ($users as $user) {
        if ($user['id'] == $_GET['user']) {
            $selectedUser = $user;
            break;
        }
    }
    if (!$selectedUser) {
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isGlobal ? 'Pulpit Rodzinny' : 'Pulpit - ' . $selectedUser['name']; ?></title>
    <link rel="stylesheet" href="style.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script.js" defer></script>
</head>
<body>
    <nav>
        <ul>
            <?php if ($isGlobal): ?>
                <li><a href="index.php" class="active">Pulpit Rodzinny</a></li>
                <li><a href="income.php">Dochody (Wszyscy)</a></li>
                <li><a href="expenses.php">Wydatki (Wszyscy)</a></li>
                <li><a href="budget.php">Budżet</a></li>
                <li><a href="users.php">Członkowie</a></li>
            <?php else: ?>
                <li><a href="index.php">Powrót do Rodziny</a></li>
                <li><a href="index.php?user=<?php echo $selectedUser['id']; ?>" class="active">Pulpit - <?php echo $selectedUser['name']; ?></a></li>
                <li><a href="income.php?user=<?php echo $selectedUser['id']; ?>">Dochody</a></li>
                <li><a href="expenses.php?user=<?php echo $selectedUser['id']; ?>">Wydatki</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="container">
        <?php if ($isGlobal): ?>
            <h1>Panel Główny Rodziny 🏠</h1>
        <?php else: ?>
            <h1>Witaj, <?php echo $selectedUser['name']; ?> 👋</h1>
        <?php endif; ?>
        
        <div class="grid">
            <div class="box">
                <h2>Podsumowanie Ogólne</h2>
                <div class="stat-item">
                    <span class="stat-label">Suma przychodów:</span>
                    <span class="stat-value badge good">
                        <?php echo $isGlobal ? getTotalIncome() : getTotalIncomeForUser($selectedUser['id']); ?> PLN
                    </span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Suma wydatków:</span>
                    <span class="stat-value badge bad">
                        <?php echo $isGlobal ? getTotalExpenses() : getTotalExpensesForUser($selectedUser['id']); ?> PLN
                    </span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Saldo:</span>
                    <?php 
                        $balance = $isGlobal ? getBalance() : getBalanceForUser($selectedUser['id']);
                        $balanceClass = $balance >= 0 ? 'good' : 'bad';
                    ?>
                    <span class="stat-value badge <?php echo $balanceClass; ?>">
                        <?php echo $balance; ?> PLN
                    </span>
                </div>
            </div>

            <div class="box">
                <h2>Budżet (<?php echo date('Y-m'); ?>)</h2>
                <div class="stat-item">
                    <span class="stat-label">Całkowity budżet:</span>
                    <span class="stat-value"><?php echo getBudget(); ?> PLN</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Wydano:</span>
                    <span class="stat-value badge bad">
                        <?php echo $isGlobal ? getSpentThisMonth() : getSpentThisMonthForUser($selectedUser['id']); ?> PLN
                    </span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Pozostało:</span>
                    <?php 
                        $remaining = $isGlobal ? getRemaining() : getRemainingForUser($selectedUser['id']);
                        $remClass = $remaining >= 0 ? 'good' : 'bad';
                    ?>
                    <span class="stat-value badge <?php echo $remClass; ?>">
                        <?php echo $remaining; ?> PLN
                    </span>
                </div>
                <div style="margin-top: 1rem;">
                    <?php
                        $percent = $isGlobal ? getUsagePercent() : getUsagePercentForUser($selectedUser['id']);
                        $barColor = $percent >= 100 ? 'var(--danger-color)' : ($percent >= 80 ? 'var(--warning-color)' : 'var(--success-color)');
                        $width = min($percent, 100);
                    ?>
                    <div class="progress-bg">
                        <div class="progress-fill" style="width: <?php echo $width; ?>%; background: <?php echo $barColor; ?>;"></div>
                    </div>
                    <div style="text-align: right; font-size: 0.85rem; color: var(--text-muted); margin-top: 0.5rem;">
                        Wykorzystano: <?php echo round($percent, 1); ?>%
                    </div>
                </div>
            </div>
        </div>

        <div class="chart-container">
            <h2>Wykres wydatków (ostatnie 6 miesięcy)</h2>
            <canvas id="expensesChart"></canvas>
        </div>

        <?php if ($isGlobal): ?>
        <h2 style="margin-top: 3rem; text-align: center;">Członkowie Rodziny</h2>
        <div class="users-grid">
            <?php
            if (empty($users)) {
                echo "<p style='text-align: center; grid-column: 1/-1;'>Brak członków rodziny. <br><br> <a href='users.php'><button class='secondary'>Zarządzaj</button></a></p>";
            } else {
                foreach ($users as $user) {
                    echo "<a href='index.php?user={$user['id']}' class='user-card'>
                            <div class='user-avatar'>". mb_substr($user['name'], 0, 1) ."</div>
                            <div class='user-name'>{$user['name']}</div>
                          </a>";
                }
            }
            ?>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var expensesData = <?php 
                if ($isGlobal) {
                    echo json_encode(getMonthlyExpensesGlobal());
                } else {
                    echo json_encode(getMonthlyExpensesForUser($selectedUser['id']));
                }
            ?>;
            if(typeof drawChart === 'function') {
                drawChart(expensesData);
            }
        });
    </script>
</body>
</html>