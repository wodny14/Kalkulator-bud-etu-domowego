<?php include 'functions.php'; ?>

<?php
$userIdForNav = $_GET['user'] ?? '';
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
<body class="fade-in" style="position: relative; overflow-x: hidden;">
    <!-- Wielki Zegar w tle -->
    <div id="bg-clock" class="clock-fade" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 20vw; font-weight: 900; color: rgba(255,255,255,0.02); z-index: -2; pointer-events: none; white-space: nowrap; user-select: none;"></div>
    
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
        <h1 style="margin-bottom: 2rem;">Pełna Historia Dochodów 💰</h1>

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
                
                if ($userIdForNav) {
                    $incomes = array_filter($incomes, function($i) use ($userIdForNav) {
                        return $i['userId'] == $userIdForNav;
                    });
                }
                
                if(empty($incomes)) {
                    echo "<tr><td colspan='4' style='text-align: center; padding: 3rem;'><div class='empty-state-icon'>💰</div><div style='color: var(--text-muted);'>Brak dochodów.</div></td></tr>";
                } else {
                    usort($incomes, function($a, $b) {
                        return strtotime($b['date']) - strtotime($a['date']);
                    });
                    
                    foreach ($incomes as $income) {
                        $userName = $userMap[$income['userId']] ?? 'Nieznany';
                        $descWithEmoji = getEmojiForDescription($income['description']);
                        $emoji = explode(' ', $descWithEmoji)[0];
                        $descText = substr(strstr($descWithEmoji," "), 1);

                        echo "<tr>
                                <td style='width: 50px;'><div style='background: rgba(255,255,255,0.05); padding: 0.5rem; border-radius: 50%; text-align: center; width: 40px; height: 40px; line-height: 24px;'>{$emoji}</div></td>
                                <td><div style='font-weight: 600;'>{$descText}</div><div style='font-size: 0.8rem; color: var(--text-muted);'>{$userName}</div></td>
                                <td style='text-align: right; color: var(--success-color); font-weight: 600;'>+{$income['amount']} PLN</td>
                                <td style='text-align: right; color: var(--text-muted); font-size: 0.85rem;'>{$income['date']}</td>
                              </tr>";
                    }
                }
                ?>
            </table>
        </div>
    </div>

    <?php include 'modal.php'; ?>
    <script>
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