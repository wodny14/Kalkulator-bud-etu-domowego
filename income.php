<?php include 'functions.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['userId'];
    $amount = floatval($_POST['amount']);
    $description = trim($_POST['description']);
    if (!empty($userId) && $amount > 0 && !empty($description)) {
        addIncome($userId, $amount, $description);
        echo "<p>Dochód dodany.</p>";
    } else {
        echo "<p>Błąd: Wszystkie pola muszą być wypełnione, kwota większa od 0.</p>";
    }
}
?>

<html>
<head>
    <title>Dochody - Kalkulator Budżetu Domowego</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body>
    <h1>Dodaj dochód</h1>
    <form method="post">
        <label for="userId">Członek rodziny:</label>
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
        <button type="submit">Dodaj dochód</button>
    </form>
    <br>
    <h2>Lista dochodów</h2>
    <table border="1">
        <tr>
            <th>Członek rodziny</th>
            <th>Kwota</th>
            <th>Opis</th>
            <th>Data</th>
        </tr>
        <?php
        $incomes = readJson('data/incomes.json');
        $users = getUsers();
        $userMap = [];
        foreach ($users as $user) {
            $userMap[$user['id']] = $user['name'];
        }
        foreach ($incomes as $income) {
            $userName = $userMap[$income['userId']] ?? 'Nieznany';
            echo "<tr><td>{$userName}</td><td>{$income['amount']}</td><td>{$income['description']}</td><td>{$income['date']}</td></tr>";
        }
        ?>
    </table>
    <br>
    <a href="index.php?user=<?php echo $_GET['user'] ?? ''; ?>">Powrót do dashboard</a>
</body>
</html>