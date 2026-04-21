<?php include 'functions.php'; ?>

<html>
<head>
    <title>Wybierz członka rodziny - Kalkulator Budżetu Domowego</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body>
    <nav>
        <ul>
            <li><a href="family.php" class="active">Wybór rodziny</a></li>
            <li><a href="users.php">Członkowie</a></li>
        </ul>
    </nav>
    <h1>Witaj w Kalkulatorze Budżetu Domowego! 👨‍👩‍👧‍👦</h1>
    <p>Wybierz członka rodziny, aby zobaczyć jego dashboard:</p>
    <ul>
        <?php
        $users = getUsers();
        if (empty($users)) {
            echo "<li>Brak członków rodziny. <a href='users.php'>Dodaj pierwszego członka</a></li>";
        } else {
            foreach ($users as $user) {
                echo "<li><a href='index.php?user={$user['id']}'>{$user['name']} 😊</a></li>";
            }
        }
        ?>
    </ul>
    <br>
    <a href="users.php">Zarządzaj członkami rodziny</a>
</body>
</html>