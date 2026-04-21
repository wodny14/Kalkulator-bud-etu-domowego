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

        <form method="post" action="process.php" style="padding: 0; box-shadow: none; border: none; background: transparent; margin: 0;">
            <input type="hidden" name="action" id="formAction" value="add_expense">
            <!-- URL powrotu, żeby wrócić na stronę, na której byliśmy -->
            <input type="hidden" name="return_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
            
            <div class="form-group">
                <label for="userIdModal">Kto?</label>
                <select id="userIdModal" name="userId" required onchange="checkNewUser()">
                    <?php
                    $usersModal = getUsers();
                    $currentUser = $_GET['user'] ?? null;
                    
                    if (empty($usersModal)) {
                        echo "<option value=\"\" disabled selected>Brak osób - dodaj nową</option>";
                    } else {
                        foreach ($usersModal as $u) {
                            $sel = ($currentUser && $currentUser == $u['id']) ? 'selected' : '';
                            echo "<option value=\"{$u['id']}\" {$sel}>{$u['name']}</option>";
                        }
                    }
                    ?>
                    <option value="ADD_NEW" style="color: var(--primary-color); font-weight: 600;">+ Dodaj nową osobę...</option>
                </select>
            </div>
            
            <div class="form-group" id="newUserGroup" style="display: none; animation: fadeIn 0.2s;">
                <label for="newUserNameModal">Imię nowej osoby:</label>
                <input type="text" id="newUserNameModal" name="newUserName" placeholder="np. Tomek">
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
                <button type="submit" id="submitBtn" style="background-color: var(--danger-color);">Dodaj wydatek</button>
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
    
    function checkNewUser() {
        var sel = document.getElementById('userIdModal');
        var group = document.getElementById('newUserGroup');
        var input = document.getElementById('newUserNameModal');
        if (sel.value === 'ADD_NEW') {
            group.style.display = 'block';
            input.setAttribute('required', 'required');
            input.focus();
        } else {
            group.style.display = 'none';
            input.removeAttribute('required');
        }
    }
</script>
