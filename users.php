<?php include 'functions.php'; ?>

<?php
$message = '';
$showForm = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        addUser($name);
        $message = "<div class='alert alert-success'>Nowy członek rodziny dodany pomyślnie.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Imię nie może być puste.</div>";
        $showForm = true; // Pokaż form, gdy jest błąd
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie - Kalkulator Budżetu</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .add-card {
            cursor: pointer;
            border: 2px dashed var(--border-color);
            background-color: transparent;
            box-shadow: none;
        }
        .add-card:hover {
            border-color: var(--primary-color);
            background-color: rgba(255,255,255,0.02);
        }
        .add-avatar {
            background: rgba(255,255,255,0.05);
            color: var(--text-muted);
            box-shadow: none;
            font-size: 2.5rem;
            font-weight: 300;
        }
        .add-card:hover .add-avatar {
            color: var(--primary-color);
            background: rgba(10, 132, 255, 0.1);
        }
    </style>
</head>
<body class="fade-in" style="position: relative; overflow-x: hidden;">
    <!-- Wielki Zegar w tle -->
    <div id="bg-clock" class="clock-fade" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 20vw; font-weight: 900; color: rgba(255,255,255,0.02); z-index: -2; pointer-events: none; white-space: nowrap; user-select: none;"></div>
    
    <nav>
        <ul>
            <li><a href="index.php">Pulpit Rodzinny</a></li>
            <li><a href="income.php">Dochody (Wszyscy)</a></li>
            <li><a href="expenses.php">Wydatki (Wszyscy)</a></li>
            <li><a href="budget.php">Budżet</a></li>
            <li><a href="users.php" class="active">Członkowie</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1 style="margin-bottom: 3rem;">Członkowie Rodziny 👨‍👩‍👧‍👦</h1>
        <?php echo $message; ?>

        <div class="users-grid">
            <?php
            $users = getUsers();
            foreach ($users as $user) {
                echo "<a href='index.php?user={$user['id']}' class='user-card'>
                        <div class='user-avatar'>". mb_substr($user['name'], 0, 1) ."</div>
                        <div class='user-name'>{$user['name']}</div>
                      </a>";
            }
            ?>
            
            <!-- Netflix style 'Add' card -->
            <div class="user-card add-card" id="addBtn" onclick="toggleAddForm()">
                <div class="user-avatar add-avatar">+</div>
                <div class="user-name" style="color: var(--text-muted);">Dodaj profil</div>
            </div>
        </div>

        <div id="addFormContainer" style="display: <?php echo $showForm ? 'block' : 'none'; ?>; margin-top: 3rem; animation: fadeIn 0.3s;">
            <h2 style="text-align: center;">Dodaj nową osobę</h2>
            <form method="post">
                <div class="form-group">
                    <label for="name">Imię nowego członka:</label>
                    <input type="text" id="name" name="name" required placeholder="np. Anna" autofocus>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <button type="button" class="secondary" onclick="toggleAddForm()">Anuluj</button>
                    <button type="submit">Dodaj osobę</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleAddForm() {
            var form = document.getElementById('addFormContainer');
            var btn = document.getElementById('addBtn');
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
                btn.style.display = 'none';
                document.getElementById('name').focus();
            } else {
                form.style.display = 'none';
                btn.style.display = 'flex';
            }
        }
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