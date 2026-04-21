<?php include 'functions.php'; ?>

<?php
$userIdForNav = $_GET['user'] ?? '';
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['userId'];
    $amount = floatval($_POST['amount']);
    $description = trim($_POST['description']);
    if (!empty($userId) && $amount > 0 && !empty($description)) {
        addIncome($userId, $amount, $description);
        $message = "<div class='alert alert-success'>Dochód dodany pomyślnie.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Błąd: Wszystkie pola muszą być wypełnione, a kwota większa od 0.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dochody - Kalkulator Budżetu Domowego</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <ul>
            <?php if ($userIdForNav): ?>
            <li><a href="index.php?user=<?php echo $userIdForNav; ?>">Pulpit</a></li>
            <li><a href="income.php?user=<?php echo $userIdForNav; ?>" class="active">Dochody</a></li>
            <li><a href="expenses.php?user=<?php echo $userIdForNav; ?>">Wydatki</a></li>
            <?php else: ?>
            <li><a href="family.php">Wybór rodziny</a></li>
            <?php endif; ?>
            <li><a href="budget.php">Budżet</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Dodaj dochód 💰</h1>
        <?php echo $message; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="userId">Członek rodziny:</label>
                <select id="userId" name="userId" required>
                    <?php
                    $users = getUsers();
                    foreach ($users as $user) {
                        $selected = ($user['id'] == $userIdForNav) ? 'selected' : '';
                        echo "<option value=\"{$user['id']}\" {$selected}>{$user['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="amount">Kwota (PLN):</label>
                <input type="number" step="0.01" id="amount" name="amount" required placeholder="np. 3500.00">
            </div>
            
            <div class="form-group">
                <label for="description">Opis:</label>
                <input type="text" id="description" name="description" required placeholder="np. Wypłata">
            </div>
            
            <button type="submit">Zapisz dochód</button>
        </form>

        <h2 style="text-align: center; margin-top: 3rem;">Historia dochodów</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>Osoba</th>
                    <th>Kwota</th>
                    <th>Opis</th>
                    <th>Data</th>
                </tr>
                <?php
                $incomes = readJson('data/incomes.json');
                $userMap = [];
                foreach ($users as $user) {
                    $userMap[$user['id']] = $user['name'];
                }
                
                if(empty($incomes)) {
                    echo "<tr><td colspan='4' style='text-align: center; color: var(--text-muted);'>Brak zarejestrowanych dochodów.</td></tr>";
                } else {
                    usort($incomes, function($a, $b) {
                        return strtotime($b['date']) - strtotime($a['date']);
                    });
                    
                    foreach ($incomes as $income) {
                        $userName = $userMap[$income['userId']] ?? 'Nieznany';
                        echo "<tr>
                                <td>{$userName}</td>
                                <td style='color: var(--success-color); font-weight: 600;'>+{$income['amount']} PLN</td>
                                <td>{$income['description']}</td>
                                <td style='color: var(--text-muted); font-size: 0.875rem;'>{$income['date']}</td>
                              </tr>";
                    }
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>