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
        $message = "<div class='alert alert-danger'>Błąd: Uzupełnij wszystkie pola.</div>";
    }
}
$users = getUsers();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dochody - Kalkulator Budżetu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <ul>
            <?php if ($userIdForNav): ?>
                <li><a href="index.php?user=<?php echo $userIdForNav; ?>">Pulpit</a></li>
            <?php else: ?>
                <li><a href="index.php">Pulpit Rodzinny</a></li>
            <?php endif; ?>
            <li><a href="income.php<?php echo $userIdForNav ? "?user=$userIdForNav" : ""; ?>" class="active"><?php echo $userIdForNav ? "Dochody" : "Dochody (Wszyscy)"; ?></a></li>
            <li><a href="expenses.php<?php echo $userIdForNav ? "?user=$userIdForNav" : ""; ?>"><?php echo $userIdForNav ? "Wydatki" : "Wydatki (Wszyscy)"; ?></a></li>
            <li><a href="budget.php">Budżet</a></li>
            <li><a href="users.php">Członkowie</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Dodaj dochód 💰</h1>
        <?php echo $message; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="userId">Kto otrzymał dochód?</label>
                <select id="userId" name="userId" required>
                    <option value="" disabled selected>Wybierz osobę...</option>
                    <?php
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
                <label for="description">Z jakiego tytułu?</label>
                <input type="text" id="description" name="description" required placeholder="np. Wypłata, Premia">
            </div>
            
            <button type="submit">Zapisz dochód</button>
        </form>

        <h2 style="text-align: center; margin-top: 3rem;">Historia dochodów (Rodzina)</h2>
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
                    echo "<tr><td colspan='4' style='text-align: center; color: var(--text-muted);'>Brak dochodów.</td></tr>";
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
                                <td style='color: var(--text-muted); font-size: 0.85rem;'>{$income['date']}</td>
                              </tr>";
                    }
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>