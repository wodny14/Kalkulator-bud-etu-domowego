<?php include 'functions.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['userId'];
    $amount = floatval($_POST['amount']);
    $description = trim($_POST['description']);
    if (!empty($userId) && $amount > 0 && !empty($description)) {
        addExpense($userId, $amount, $description);
        echo "<p>Wydatek dodany.</p>";
    } else {
        echo "<p>Błąd: Wszystkie pola muszą być wypełnione, kwota większa od 0.</p>";
    }
}
?>

<html>
<head>
    <title>Wydatki - Kalkulator Budżetu Domowego</title>
</head>
<body>
    <h1>Dodaj wydatek</h1>
    <form method="post">
        <label for="userId">Użytkownik:</label>
        <select id="userId" name="userId" required>
            <?php
            $users = getUsers();
            foreach ($users as $user) {
                echo "<option value=\"{$user['id']}\">{$user['name']}</option>";
            }
            ?>
        </select>
        <br>
        <label for="amount">Kwota:</label>
        <input type="number" step="0.01" id="amount" name="amount" required>
        <br>
        <label for="description">Opis:</label>
        <input type="text" id="description" name="description" required>
        <br>
        <button type="submit">Dodaj wydatek</button>
    </form>
    <br>
    <h2>Lista wydatków</h2>
    <table border="1">
        <tr>
            <th>Użytkownik</th>
            <th>Kwota</th>
            <th>Opis</th>
            <th>Data</th>
        </tr>
        <?php
        $expenses = readJson('data/expenses.json');
        $users = getUsers();
        $userMap = [];
        foreach ($users as $user) {
            $userMap[$user['id']] = $user['name'];
        }
        foreach ($expenses as $expense) {
            $userName = $userMap[$expense['userId']] ?? 'Nieznany';
            echo "<tr><td>{$userName}</td><td>{$expense['amount']}</td><td>{$expense['description']}</td><td>{$expense['date']}</td></tr>";
        }
        ?>
    </table>
    <br>
    <a href="index.php">Powrót do dashboard</a>
</body>
</html>