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
    <title>Użytkownicy - Kalkulator Budżetu Domowego</title>    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script></head>
<body>
    <h1>Członkowie rodziny</h1>
    <form method="post">
        <label for="name">Imię członka rodziny:</label>
        <input type="text" id="name" name="name" required>
        <button type="submit">Dodaj członka rodziny</button>
    </form>
    <br>
    <h2>Lista członków rodziny</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Imię</th>
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