<?php

// Funkcje do obsługi JSON: zapis i odczyt
function readJson($file) {
    if (!file_exists($file)) {
        return [];
    }
    $data = file_get_contents($file);
    return json_decode($data, true) ?: [];
}

function writeJson($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

// Dodanie użytkownika
function addUser($name) {
    if (empty($name)) {
        return false; // Zabezpieczenie przed pustymi danymi
    }
    $users = readJson('data/users.json');
    $id = count($users) + 1; // Prosty przydział ID
    $users[] = ['id' => $id, 'name' => $name];
    writeJson('data/users.json', $users);
    return $id;
}

// Pobranie listy użytkowników
function getUsers() {
    return readJson('data/users.json');
}

// Dodanie dochodu
function addIncome($userId, $amount, $description) {
    if (empty($userId) || empty($amount) || empty($description)) {
        return false; // Zabezpieczenie przed pustymi danymi
    }
    $incomes = readJson('data/incomes.json');
    $incomes[] = [
        'userId' => $userId,
        'amount' => $amount,
        'description' => $description,
        'date' => date('Y-m-d')
    ];
    writeJson('data/incomes.json', $incomes);
    return true;
}

// Dodanie wydatku
function addExpense($userId, $amount, $description) {
    if (empty($userId) || empty($amount) || empty($description)) {
        return false; // Zabezpieczenie przed pustymi danymi
    }
    $expenses = readJson('data/expenses.json');
    $expenses[] = [
        'userId' => $userId,
        'amount' => $amount,
        'description' => $description,
        'date' => date('Y-m-d')
    ];
    writeJson('data/expenses.json', $expenses);
    return true;
}

// Pobranie sumy przychodów
function getTotalIncome() {
    $incomes = readJson('data/incomes.json');
    $total = 0;
    foreach ($incomes as $income) {
        $total += $income['amount'];
    }
    return $total;
}

// Pobranie sumy wydatków
function getTotalExpenses() {
    $expenses = readJson('data/expenses.json');
    $total = 0;
    foreach ($expenses as $expense) {
        $total += $expense['amount'];
    }
    return $total;
}

// Pobranie salda
function getBalance() {
    return getTotalIncome() - getTotalExpenses();
}

// Ustawienie budżetu miesięcznego
function setBudget($amount) {
    if ($amount <= 0) {
        return false; // Zabezpieczenie przed nieprawidłowymi wartościami
    }
    $month = date('Y-m');
    $budget = ['month' => $month, 'amount' => $amount];
    writeJson('data/budget.json', [$budget]); // Jeden wpis na miesiąc
    return true;
}

// Pobranie budżetu na bieżący miesiąc
function getBudget() {
    $budgets = readJson('data/budget.json');
    $currentMonth = date('Y-m');
    foreach ($budgets as $b) {
        if ($b['month'] == $currentMonth) {
            return $b['amount'];
        }
    }
    return 0;
}

// Obliczenie wydatków w bieżącym miesiącu
function getSpentThisMonth() {
    $expenses = readJson('data/expenses.json');
    $currentMonth = date('Y-m');
    $total = 0;
    foreach ($expenses as $exp) {
        if (substr($exp['date'], 0, 7) == $currentMonth) {
            $total += $exp['amount'];
        }
    }
    return $total;
}

// Obliczenie pozostałego budżetu
function getRemaining() {
    return getBudget() - getSpentThisMonth();
}

// Obliczenie procentu wykorzystania budżetu
function getUsagePercent() {
    $budget = getBudget();
    if ($budget == 0) {
        return 0;
    }
    return (getSpentThisMonth() / $budget) * 100;
}

// Funkcje dla konkretnego użytkownika
function getTotalIncomeForUser($userId) {
    $incomes = readJson('data/incomes.json');
    $total = 0;
    foreach ($incomes as $income) {
        if ($income['userId'] == $userId) {
            $total += $income['amount'];
        }
    }
    return $total;
}

function getTotalExpensesForUser($userId) {
    $expenses = readJson('data/expenses.json');
    $total = 0;
    foreach ($expenses as $expense) {
        if ($expense['userId'] == $userId) {
            $total += $expense['amount'];
        }
    }
    return $total;
}

function getBalanceForUser($userId) {
    return getTotalIncomeForUser($userId) - getTotalExpensesForUser($userId);
}

function getSpentThisMonthForUser($userId) {
    $expenses = readJson('data/expenses.json');
    $currentMonth = date('Y-m');
    $total = 0;
    foreach ($expenses as $exp) {
        if ($exp['userId'] == $userId && substr($exp['date'], 0, 7) == $currentMonth) {
            $total += $exp['amount'];
        }
    }
    return $total;
}

function getRemainingForUser($userId) {
    return getBudget() - getSpentThisMonthForUser($userId);
}

function getUsagePercentForUser($userId) {
    $budget = getBudget();
    if ($budget == 0) {
        return 0;
    }
    return (getSpentThisMonthForUser($userId) / $budget) * 100;
}

// Funkcja do wykresu: miesięczne wydatki dla użytkownika (ostatnie 6 miesięcy)
function getMonthlyExpensesForUser($userId) {
    $expenses = readJson('data/expenses.json');
    $monthly = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $total = 0;
        foreach ($expenses as $exp) {
            if ($exp['userId'] == $userId && substr($exp['date'], 0, 7) == $month) {
                $total += $exp['amount'];
            }
        }
        $monthly[] = ['month' => $month, 'amount' => $total];
    }
    return $monthly;
}

function getMonthlyExpensesGlobal() {
    $expenses = readJson('data/expenses.json');
    $monthly = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $total = 0;
        foreach ($expenses as $exp) {
            if (substr($exp['date'], 0, 7) == $month) {
                $total += $exp['amount'];
            }
        }
        $monthly[] = ['month' => $month, 'amount' => $total];
    }
    return $monthly;
}

?>