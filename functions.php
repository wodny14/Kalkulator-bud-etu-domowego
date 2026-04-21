<?php

// Funkcje do obsługi JSON
function readJson($file) {
    if (!file_exists($file)) return [];
    $data = file_get_contents($file);
    return json_decode($data, true) ?: [];
}

function writeJson($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

// Użytkownicy
function addUser($name) {
    if (empty($name)) return false;
    $users = readJson('data/users.json');
    $id = count($users) + 1;
    $users[] = ['id' => $id, 'name' => $name];
    writeJson('data/users.json', $users);
    return $id;
}

function getUsers() {
    return readJson('data/users.json');
}

// Emotki
function getEmojiForDescription($desc) {
    $descLower = mb_strtolower($desc);
    $mapping = [
        'zakupy' => '🛒', 'biedronka' => '🐞', 'lidl' => '🛒', 'zabka' => '🐸', 'żabka' => '🐸',
        'paliwo' => '⛽', 'orlen' => '⛽', 'bp' => '⛽', 'shell' => '⛽', 'samochód' => '🚗', 'auto' => '🚗',
        'kino' => '🍿', 'netflix' => '🎬', 'spotify' => '🎧', 'rozrywka' => '🎮',
        'jedzenie' => '🍔', 'pizza' => '🍕', 'restauracja' => '🍽️', 'pyszne' => '🛵', 'mcdonald' => '🍟',
        'rachunki' => '📄', 'prąd' => '⚡', 'woda' => '💧', 'gaz' => '🔥', 'czynsz' => '🏠',
        'leki' => '💊', 'apteka' => '⚕️', 'zdrowie' => '❤️',
        'ubrania' => '👕', 'buty' => '👟', 'zalando' => '🛍️',
        'wypłata' => '💰', 'pensja' => '💵', 'premia' => '🎉', 'prezent' => '🎁'
    ];
    
    foreach ($mapping as $keyword => $emoji) {
        if (strpos($descLower, $keyword) !== false) {
            return $emoji . ' ' . $desc;
        }
    }
    return '📝 ' . $desc;
}

// Dochody
function addIncome($userId, $amount, $description) {
    if (empty($userId) || empty($amount) || empty($description)) return false;
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

// Wydatki (dodano kategorię)
function addExpense($userId, $amount, $description, $category = 'Inne') {
    if (empty($userId) || empty($amount) || empty($description)) return false;
    $expenses = readJson('data/expenses.json');
    $expenses[] = [
        'userId' => $userId,
        'amount' => $amount,
        'description' => $description,
        'category' => $category,
        'date' => date('Y-m-d')
    ];
    writeJson('data/expenses.json', $expenses);
    return true;
}

// STATYSTYKI MIESIĘCZNE
function getCurrentMonthStr($month = null) {
    return $month ? $month : date('Y-m');
}

function getTotalIncome($month = null) {
    $targetMonth = getCurrentMonthStr($month);
    $incomes = readJson('data/incomes.json');
    $total = 0;
    foreach ($incomes as $income) {
        if (substr($income['date'], 0, 7) == $targetMonth) $total += $income['amount'];
    }
    return $total;
}

function getTotalExpenses($month = null) {
    $targetMonth = getCurrentMonthStr($month);
    $expenses = readJson('data/expenses.json');
    $total = 0;
    foreach ($expenses as $exp) {
        if (substr($exp['date'], 0, 7) == $targetMonth) $total += $exp['amount'];
    }
    return $total;
}

function getBalance($month = null) {
    return getTotalIncome($month) - getTotalExpenses($month);
}

// Budżet
function setBudget($amount) {
    if ($amount <= 0) return false;
    $month = date('Y-m');
    $budgets = readJson('data/budget.json');
    
    $found = false;
    foreach ($budgets as &$b) {
        if ($b['month'] == $month) {
            $b['amount'] = $amount;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $budgets[] = ['month' => $month, 'amount' => $amount];
    }
    writeJson('data/budget.json', $budgets);
    return true;
}

function getBudget($month = null) {
    $targetMonth = getCurrentMonthStr($month);
    $budgets = readJson('data/budget.json');
    foreach ($budgets as $b) {
        if ($b['month'] == $targetMonth) return $b['amount'];
    }
    return 0;
}

function getRemaining($month = null) {
    return getBudget($month) - getTotalExpenses($month);
}

function getUsagePercent($month = null) {
    $budget = getBudget($month);
    if ($budget == 0) return 0;
    return (getTotalExpenses($month) / $budget) * 100;
}

// Dla konkretnego użytkownika w danym miesiącu
function getTotalIncomeForUser($userId, $month = null) {
    $targetMonth = getCurrentMonthStr($month);
    $incomes = readJson('data/incomes.json');
    $total = 0;
    foreach ($incomes as $income) {
        if ($income['userId'] == $userId && substr($income['date'], 0, 7) == $targetMonth) {
            $total += $income['amount'];
        }
    }
    return $total;
}

function getTotalExpensesForUser($userId, $month = null) {
    $targetMonth = getCurrentMonthStr($month);
    $expenses = readJson('data/expenses.json');
    $total = 0;
    foreach ($expenses as $expense) {
        if ($expense['userId'] == $userId && substr($expense['date'], 0, 7) == $targetMonth) {
            $total += $expense['amount'];
        }
    }
    return $total;
}

function getBalanceForUser($userId, $month = null) {
    return getTotalIncomeForUser($userId, $month) - getTotalExpensesForUser($userId, $month);
}

function getRemainingForUser($userId, $month = null) {
    return getBudget($month) - getTotalExpensesForUser($userId, $month);
}

function getUsagePercentForUser($userId, $month = null) {
    $budget = getBudget($month);
    if ($budget == 0) return 0;
    return (getTotalExpensesForUser($userId, $month) / $budget) * 100;
}

// Kategorie (Donut Chart)
function getExpensesByCategory($userId = null, $month = null) {
    $targetMonth = getCurrentMonthStr($month);
    $expenses = readJson('data/expenses.json');
    $categories = [];
    
    foreach ($expenses as $exp) {
        if (substr($exp['date'], 0, 7) == $targetMonth) {
            if ($userId !== null && $exp['userId'] != $userId) continue;
            
            $cat = isset($exp['category']) && !empty($exp['category']) ? $exp['category'] : 'Inne';
            if (!isset($categories[$cat])) $categories[$cat] = 0;
            $categories[$cat] += $exp['amount'];
        }
    }
    
    $result = [];
    foreach ($categories as $k => $v) {
        $result[] = ['category' => $k, 'amount' => $v];
    }
    return $result;
}

// Historia na wykresy słupkowe
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