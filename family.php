<?php include 'functions.php'; ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wybierz członka rodziny - Kalkulator Budżetu Domowego</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <nav>
        <ul>
            <li><a href="family.php" class="active">Wybór rodziny</a></li>
            <li><a href="users.php">Zarządzaj członkami</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Kalkulator Budżetu Domowego 📊</h1>
        <p style="text-align: center; color: var(--text-muted);">Wybierz członka rodziny, aby zobaczyć jego pulpit:</p>
        
        <div class="users-grid">
            <?php
            $users = getUsers();
            if (empty($users)) {
                echo "<div class='empty-state'>
                        <p>Brak członków rodziny.</p>
                        <a href='users.php' style='display:inline-block; margin-top:1rem;'>
                            <button>Dodaj pierwszego członka</button>
                        </a>
                      </div>";
            } else {
                foreach ($users as $user) {
                    echo "<a href='index.php?user={$user['id']}' class='user-card' style='text-decoration: none;'>
                            <div class='user-avatar'>". mb_substr($user['name'], 0, 1) ."</div>
                            <div class='user-name'>{$user['name']}</div>
                          </a>";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>