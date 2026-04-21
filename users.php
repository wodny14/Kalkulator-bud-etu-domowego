<?php include 'functions.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        addUser($name);
        echo "<p>Użytkownik dodany.</p>";
    } else {
        echo "<p>Błąd: Nazwa nie może być pusta.</p>";
    }
}
?>

<html>
<head>
    <title>Użytkownicy - Kalkulator Budżetu Domowego</title>
</head>
<body>
    <h1>Użytkownicy</h1>
    <form method="post">
        <label for="name">Nazwa użytkownika:</label>
        <input type="text" id="name" name="name" required>
        <button type="submit">Dodaj użytkownika</button>
    </form>
    <br>
    <h2>Lista użytkowników</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nazwa</th>
        </tr>
        <?php
        $users = getUsers();
        foreach ($users as $user) {
            echo "<tr><td>{$user['id']}</td><td>{$user['name']}</td></tr>";
        }
        ?>
    </table>
    <br>
    <a href="index.php">Powrót do dashboard</a>
</body>
</html>