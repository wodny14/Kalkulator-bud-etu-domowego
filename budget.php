<?php include 'functions.php'; ?>

<?php
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = floatval($_POST['amount']);
    if ($amount > 0) {
        setBudget($amount);
        $message = "<div class='alert alert-success'>Budżet zaktualizowany.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Podaj kwotę większą od 0.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budżet - Kalkulator Budżetu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="index.php">Pulpit Rodzinny</a></li>
            <li><a href="income.php">Dochody (Wszyscy)</a></li>
            <li><a href="expenses.php">Wydatki (Wszyscy)</a></li>
            <li><a href="budget.php" class="active">Budżet</a></li>
            <li><a href="users.php">Członkowie</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Ustawienia Budżetu 📈</h1>
        <?php echo $message; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="amount">Miesięczny budżet rodziny (PLN):</label>
                <input type="number" step="0.01" id="amount" name="amount" required placeholder="np. 5000.00" value="<?php echo getBudget(); ?>">
            </div>
            <button type="submit">Zapisz budżet</button>
        </form>

        <div class="box" style="max-width: 500px; margin: 2rem auto;">
            <h2 style="text-align: center;">Stan budżetu na <?php echo date('Y-m'); ?></h2>
            <div class="stat-item">
                <span class="stat-label">Całkowity budżet:</span>
                <span class="stat-value"><?php echo getBudget(); ?> PLN</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Wydano do tej pory:</span>
                <span class="stat-value badge bad"><?php echo getSpentThisMonth(); ?> PLN</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Pozostało:</span>
                <span class="stat-value badge <?php echo getRemaining() >= 0 ? 'good' : 'bad'; ?>">
                    <?php echo getRemaining(); ?> PLN
                </span>
            </div>
            
            <div style="margin-top: 1.5rem;">
                <?php
                    $percent = getUsagePercent();
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
</body>
</html>