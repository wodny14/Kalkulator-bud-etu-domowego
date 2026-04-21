<?php include 'functions.php'; ?>

<?php
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        addUser($name);
        $message = "<div class='alert alert-success'>Nowy członek rodziny dodany.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Błąd: Imię nie może być puste.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Użytkownicy - Kalkulator Budżetu Domowego</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="family.php">Wybór rodziny</a></li>
            <li><a href="users.php" class="active">Zarządzaj członkami</a></li>
            <li><a href="budget.php">Ustawienia Budżetu</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Zarządzanie rodziną 👨‍👩‍👧‍👦</h1>
        <?php echo $message; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="name">Imię nowego członka rodziny:</label>
                <input type="text" id="name" name="name" required placeholder="np. Anna">
            </div>
            <button type="submit">Dodaj członka rodziny</button>
        </form>

        <h2 style="text-align: center; margin-top: 3rem;">Lista członków rodziny</h2>
        <div class="table-container" style="max-width: 600px; margin: 0 auto;">
            <table>
                <tr>
                    <th style="width: 80px; text-align: center;">ID</th>
                    <th>Imię</th>
                    <th style="text-align: right;">Akcja</th>
                </tr>
                <?php
                $users = getUsers();
                if(empty($users)) {
                    echo "<tr><td colspan='3' style='text-align: center; color: var(--text-muted);'>Brak zarejestrowanych osób.</td></tr>";
                } else {
                    foreach ($users as $user) {
                        echo "<tr>
                                <td style='text-align: center; color: var(--text-muted);'>#{$user['id']}</td>
                                <td style='font-weight: 500;'>{$user['name']}</td>
                                <td style='text-align: right;'>
                                    <a href='index.php?user={$user['id']}' style='font-size: 0.875rem;'>Przejdź na pulpit &rarr;</a>
                                </td>
                              </tr>";
                    }
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>