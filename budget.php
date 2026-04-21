<?php include 'functions.php'; ?>

<?php
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = floatval($_POST['amount']);
    if ($amount > 0) {
        setBudget($amount);
        $message = "<div class='alert alert-success'>Budżet miesięczny został ustawiony.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Błąd: Kwota musi być większa od 0.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budżet - Kalkulator Budżetu Domowego</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="family.php">Wybór rodziny</a></li>
            <li><a href="users.php">Członkowie</a></li>
            <li><a href="budget.php" class="active">Ustawienia Budżetu</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Miesięczny budżet domowy 📈</h1>
        <?php echo $message; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="amount">Kwota budżetu dla całej rodziny (PLN):</label>
                <input type="number" step="0.01" id="amount" name="amount" required placeholder="np. 5000.00" value="<?php echo getBudget(); ?>">
            </div>
            <button type="submit">Zapisz budżet</button>
        </form>

        <div class="box" style="max-width: 500px; margin: 2rem auto;">
            <h2 style="text-align: center;">Stan budżetu na (<?php echo date('Y-m'); ?>)</h2>
            <div class="stat-item">
                <span>Całkowity budżet:</span>
                <span><?php echo getBudget(); ?> PLN</span>
            </div>
            <div class="stat-item">
                <span>Wydano przez wszystkich:</span>
                <span class="badge bad"><?php echo getSpentThisMonth(); ?> PLN</span>
            </div>
            <div class="stat-item">
                <span>Pozostało w budżecie:</span>
                <span class="badge <?php echo getRemaining() >= 0 ? 'good' : 'bad'; ?>">
                    <?php echo getRemaining(); ?> PLN
                </span>
            </div>
            
            <div class="stat-item" style="border-bottom:none; margin-top:1rem; flex-direction: column;">
                <?php
                    $percent = getUsagePercent();
                    $barColor = $percent >= 100 ? 'var(--danger-color)' : ($percent >= 80 ? 'var(--warning-color)' : 'var(--success-color)');
                    $width = min($percent, 100);
                ?>
                <div style="width: 100%; background: var(--bg-color); height: 12px; border-radius: 6px; overflow: hidden;">
                    <div style="width: <?php echo $width; ?>%; background: <?php echo $barColor; ?>; height: 100%; transition: width 0.5s ease-out;"></div>
                </div>
                <div style="width: 100%; text-align: right; font-size: 0.875rem; color: var(--text-muted); margin-top: 0.5rem;">
                    Wykorzystano: <?php echo round($percent, 2); ?>%
                </div>
            </div>
        </div>
    </div>
</body>
</html>