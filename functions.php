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

?>