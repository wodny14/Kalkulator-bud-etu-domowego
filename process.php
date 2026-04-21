<?php
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    
    $userId = $_POST['userId'];
    
    if ($userId === 'ADD_NEW' && !empty($_POST['newUserName'])) {
        $userId = addUser(trim($_POST['newUserName']));
    }

    if ($_POST['action'] == 'add_expense') {
        addExpense($userId, floatval($_POST['amount']), $_POST['description'], $_POST['category'] ?? 'Inne');
    } elseif ($_POST['action'] == 'add_income') {
        addIncome($userId, floatval($_POST['amount']), $_POST['description']);
    }
    
    $redirectUrl = $_POST['return_url'] ?? 'index.php';
    header("Location: $redirectUrl");
    exit;
}
?>
