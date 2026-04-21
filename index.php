<?php include 'functions.php'; ?>

<?php
// Obsługa dodawania z Modala
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add_expense') {
        addExpense($_POST['userId'], floatval($_POST['amount']), $_POST['description'], $_POST['category'] ?? 'Inne');
    } elseif ($_POST['action'] == 'add_income') {
        addIncome($_POST['userId'], floatval($_POST['amount']), $_POST['description']);
    }
    
    $redirectUrl = 'index.php?month=' . urlencode($_GET['month'] ?? date('Y-m'));
    if (isset($_GET['user'])) $redirectUrl .= '&user=' . urlencode($_GET['user']);
    header("Location: $redirectUrl");
    exit;
}

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

// Obsługa przewijania miesięcy
$currentMonthStr = $_GET['month'] ?? date('Y-m');
$timestamp = strtotime($currentMonthStr . '-01');
$prevMonth = date('Y-m', strtotime('-1 month', $timestamp));
$nextMonth = date('Y-m', strtotime('+1 month', $timestamp));

$monthNames = ['01'=>'Styczeń', '02'=>'Luty', '03'=>'Marzec', '04'=>'Kwiecień', '05'=>'Maj', '06'=>'Czerwiec', '07'=>'Lipiec', '08'=>'Sierpień', '09'=>'Wrzesień', '10'=>'Październik', '11'=>'Listopad', '12'=>'Grudzień'];
$displayMonth = $monthNames[date('m', $timestamp)] . ' ' . date('Y', $timestamp);

// Dynamiczne powitanie
$hour = date('H');
$greeting = ($hour >= 5 && $hour < 18) ? 'Dzień dobry ☀️' : 'Dobry wieczór 🌙';

// Ostatnie transakcje
$allExpenses = readJson('data/expenses.json');
$recent = [];
foreach($allExpenses as $e) {
   if (substr($e['date'], 0, 7) == $currentMonthStr) {
       if ($isGlobal || $e['userId'] == $selectedUser['id']) {
           $recent[] = $e;
       }
   }
}
usort($recent, function($a,$b) { return strtotime($b['date']) - strtotime($a['date']); });
$recent = array_slice($recent, 0, 5);

// Obliczenia
$balance = $isGlobal ? getBalance($currentMonthStr) : getBalanceForUser($selectedUser['id'], $currentMonthStr);
$percent = $isGlobal ? getUsagePercent($currentMonthStr) : getUsagePercentForUser($selectedUser['id'], $currentMonthStr);
$glowClass = $percent >= 100 ? 'bad' : ($percent >= 80 ? 'warning' : 'good');
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isGlobal ? 'Pulpit Rodzinny' : 'Pulpit - ' . $selectedUser['name']; ?></title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script.js" defer></script>
</head>
<body style="position: relative; overflow-x: hidden;">
    <!-- Wielki Zegar w tle -->
    <div id="bg-clock" class="clock-fade" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 20vw; font-weight: 900; color: rgba(255,255,255,0.02); z-index: -2; pointer-events: none; white-space: nowrap; user-select: none;"></div>
    
    <!-- Glow Effect na tło -->
    <div class="background-glow <?php echo $glowClass; ?>"></div>

    <nav>
        <ul>
            <?php if ($isGlobal): ?>
                <li><a href="index.php" class="active">Pulpit Rodzinny</a></li>
                <li><a href="income.php">Dochody</a></li>
                <li><a href="expenses.php">Wydatki</a></li>
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
        <!-- Zmieniacz miesiąca -->
        <div class="month-switcher">
            <a href="?month=<?php echo $prevMonth; ?><?php echo !$isGlobal ? '&user='.$selectedUser['id'] : ''; ?>" class="month-btn">&larr;</a>
            <h2 style="margin: 0; min-width: 150px; text-align: center;"><?php echo $displayMonth; ?></h2>
            <a href="?month=<?php echo $nextMonth; ?><?php echo !$isGlobal ? '&user='.$selectedUser['id'] : ''; ?>" class="month-btn">&rarr;</a>
        </div>

        <p style="text-align: center; color: var(--text-muted); margin-bottom: 0.5rem;"><?php echo $greeting; ?></p>
        <?php if ($isGlobal): ?>
            <h1>Panel Rodziny</h1>
        <?php else: ?>
            <h1><?php echo $selectedUser['name']; ?></h1>
        <?php endif; ?>
        
        <div class="grid">
            <div class="box">
                <h2>Podsumowanie</h2>
                <div class="stat-item">
                    <span class="stat-label">Suma przychodów:</span>
                    <span class="stat-value badge good">
                        <?php echo $isGlobal ? getTotalIncome($currentMonthStr) : getTotalIncomeForUser($selectedUser['id'], $currentMonthStr); ?> PLN
                    </span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Suma wydatków:</span>
                    <span class="stat-value badge bad">
                        <?php echo $isGlobal ? getTotalExpenses($currentMonthStr) : getTotalExpensesForUser($selectedUser['id'], $currentMonthStr); ?> PLN
                    </span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Saldo:</span>
                    <span class="stat-value badge <?php echo $balance >= 0 ? 'good' : 'bad'; ?>">
                        <?php echo $balance; ?> PLN
                    </span>
                </div>
            </div>

            <div class="box">
                <h2>Budżet</h2>
                <div class="stat-item">
                    <span class="stat-label">Całkowity budżet:</span>
                    <span class="stat-value"><?php echo getBudget($currentMonthStr); ?> PLN</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Wydano:</span>
                    <span class="stat-value badge bad">
                        <?php echo $isGlobal ? getTotalExpenses($currentMonthStr) : getTotalExpensesForUser($selectedUser['id'], $currentMonthStr); ?> PLN
                    </span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Pozostało:</span>
                    <?php 
                        $remaining = $isGlobal ? getRemaining($currentMonthStr) : getRemainingForUser($selectedUser['id'], $currentMonthStr);
                    ?>
                    <span class="stat-value badge <?php echo $remaining >= 0 ? 'good' : 'bad'; ?>">
                        <?php echo $remaining; ?> PLN
                    </span>
                </div>
                <div style="margin-top: 1rem;">
                    <?php
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

        <div class="grid" style="grid-template-columns: 2fr 1fr;">
            <div class="chart-container" style="margin-top: 0;">
                <h2>Historia (6 miesięcy)</h2>
                <canvas id="expensesChart"></canvas>
            </div>
            <div class="chart-container" style="margin-top: 0;">
                <h2>Kategorie</h2>
                <canvas id="donutChart"></canvas>
            </div>
        </div>

        <h2 style="margin-top: 3rem; text-align: center;">Ostatnie transakcje</h2>
        <div class="table-container">
            <table>
                <?php
                if(empty($recent)) {
                    echo "<tr><td style='text-align: center; padding: 3rem;'><div class='empty-state-icon'>💸</div><div style='color: var(--text-muted);'>Brak transakcji w tym miesiącu.</div></td></tr>";
                } else {
                    $userMap = [];
                    foreach ($users as $user) $userMap[$user['id']] = $user['name'];

                    foreach ($recent as $expense) {
                        $userName = $userMap[$expense['userId']] ?? 'Nieznany';
                        $descWithEmoji = getEmojiForDescription($expense['description']);
                        echo "<tr>
                                <td style='width: 50px;'><div style='background: rgba(255,255,255,0.05); padding: 0.5rem; border-radius: 50%; text-align: center; width: 40px; height: 40px; line-height: 24px;'>".explode(' ', $descWithEmoji)[0]."</div></td>
                                <td><div style='font-weight: 600;'>".substr(strstr($descWithEmoji," "), 1)."</div><div style='font-size: 0.8rem; color: var(--text-muted);'>{$userName} • {$expense['date']} • {$expense['category']}</div></td>
                                <td style='text-align: right; color: var(--danger-color); font-weight: 600;'>-{$expense['amount']} PLN</td>
                              </tr>";
                    }
                }
                ?>
            </table>
            <?php if(!empty($recent)): ?>
            <div style="text-align: center; padding: 1rem;">
                <a href="expenses.php<?php echo !$isGlobal ? '?user='.$selectedUser['id'] : ''; ?>">Zobacz całą historię &rarr;</a>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($isGlobal && !empty($users)): ?>
        <h2 style="margin-top: 3rem; text-align: center;">Członkowie Rodziny</h2>
        <div class="users-grid">
            <?php
            foreach ($users as $user) {
                echo "<a href='index.php?user={$user['id']}' class='user-card'>
                        <div class='user-avatar'>". mb_substr($user['name'], 0, 1) ."</div>
                        <div class='user-name'>{$user['name']}</div>
                        </a>";
            }
            ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Pływający Przycisk Akcji (FAB) -->
    <button class="fab" onclick="openModal()">+</button>

    <!-- Glassmorphism Modal -->
    <div class="modal-overlay" id="addModal">
        <div class="modal-content">
            <h2 style="text-align: center; margin-bottom: 1.5rem;">Dodaj transakcję</h2>
            
            <div class="modal-tabs">
                <div class="modal-tab active" id="tabExpense" onclick="switchTab('expense')">Wydatek</div>
                <div class="modal-tab" id="tabIncome" onclick="switchTab('income')">Dochód</div>
            </div>

            <form method="post" action="" style="padding: 0; box-shadow: none; border: none; background: transparent;">
                <input type="hidden" name="action" id="formAction" value="add_expense">
                
                <div class="form-group">
                    <label for="userIdModal">Kto?</label>
                    <select id="userIdModal" name="userId" required>
                        <?php
                        foreach ($users as $user) {
                            $sel = ($selectedUser && $selectedUser['id'] == $user['id']) ? 'selected' : '';
                            echo "<option value=\"{$user['id']}\" {$sel}>{$user['name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="amountModal">Kwota (PLN):</label>
                    <input type="number" step="0.01" id="amountModal" name="amount" required placeholder="0.00">
                </div>

                <div class="form-group" id="categoryGroup">
                    <label for="categoryModal">Kategoria:</label>
                    <select id="categoryModal" name="category">
                        <option value="Jedzenie">Jedzenie</option>
                        <option value="Transport">Transport</option>
                        <option value="Rachunki">Rachunki</option>
                        <option value="Rozrywka">Rozrywka</option>
                        <option value="Zdrowie">Zdrowie</option>
                        <option value="Ubrania">Ubrania</option>
                        <option value="Inne" selected>Inne</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="descModal">Opis:</label>
                    <input type="text" id="descModal" name="description" required placeholder="np. Zakupy, Paliwo...">
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="button" class="secondary" onclick="closeModal()">Anuluj</button>
                    <button type="submit" id="submitBtn">Dodaj wydatek</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('addModal').classList.add('active'); }
        function closeModal() { document.getElementById('addModal').classList.remove('active'); }
        function switchTab(type) {
            document.getElementById('tabExpense').classList.remove('active');
            document.getElementById('tabIncome').classList.remove('active');
            
            if (type === 'expense') {
                document.getElementById('tabExpense').classList.add('active');
                document.getElementById('formAction').value = 'add_expense';
                document.getElementById('categoryGroup').style.display = 'block';
                document.getElementById('submitBtn').innerText = 'Dodaj wydatek';
                document.getElementById('submitBtn').style.backgroundColor = 'var(--danger-color)';
            } else {
                document.getElementById('tabIncome').classList.add('active');
                document.getElementById('formAction').value = 'add_income';
                document.getElementById('categoryGroup').style.display = 'none';
                document.getElementById('submitBtn').innerText = 'Dodaj dochód';
                document.getElementById('submitBtn').style.backgroundColor = 'var(--success-color)';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var expensesData = <?php echo json_encode($isGlobal ? getMonthlyExpensesGlobal() : getMonthlyExpensesForUser($selectedUser['id'])); ?>;
            var donutData = <?php echo json_encode(getExpensesByCategory($isGlobal ? null : $selectedUser['id'], $currentMonthStr)); ?>;
            if(typeof drawCharts === 'function') {
                drawCharts(expensesData, donutData);
            }
        });
        function updateClock() {
            const d = new Date();
            const clock = document.getElementById('bg-clock');
            if (clock) {
                clock.innerText = d.getHours().toString().padStart(2, '0') + ':' + 
                                  d.getMinutes().toString().padStart(2, '0') + ':' + 
                                  d.getSeconds().toString().padStart(2, '0');
            }
        }
        updateClock();
        setInterval(updateClock, 1000);
    </script>
</body>
</html>