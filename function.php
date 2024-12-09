<?php
require_once 'db_connection.php';

// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$showToast = false;
$isSuccess = true;
$toastMessage = "";

// Fetch data
$usersList = getUsers($pdo);
$foodLists = getFoodListsForToday($pdo);
$userId = getUserIdByUsername($pdo, $username);
$userHasAddedFoodToday = checkUserFoodToday($pdo, $userId);

// Handle form submission for adding a new food item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$userHasAddedFoodToday) {
    $date = $_POST['date'];
    $item = $_POST['item'];

    list($showToast, $isSuccess, $toastMessage) = insertFoodItem($pdo, $date, $item, $userId);

    // After the form is submitted, redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

/**
 * Fetch food lists for today
 */
function getFoodListsForToday($pdo) {
    $today = date('Y-m-d');
    $sql = "SELECT food_lists.item, users.username 
            FROM users 
            JOIN food_lists ON food_lists.user_id = users.id 
            WHERE DATE(food_lists.date) = :today";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':today' => $today]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetch the food item of a user for today
 */
function getUserFoodToday($username, $foodLists) {
    foreach ($foodLists as $food) {
        if ($food['username'] === $username) {
            return $food['item'];
        }
    }
    return 'No food added yet'; // In case the user hasn't added any food
}

/**
 * Fetch all users
 */
function getUsers($pdo) {
    $sql = "SELECT full_name, username FROM users";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetch user ID by username
 */
function getUserIdByUsername($pdo, $username) {
    $sql = "SELECT id FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ? $user['id'] : null;
}

/**
 * Check if the user has already added food today
 */
function checkUserFoodToday($pdo, $userId) {
    $today = date('Y-m-d');
    $sql = "SELECT COUNT(*) FROM food_lists WHERE user_id = :user_id AND DATE(date) = :today";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $userId, ':today' => $today]);

    return $stmt->fetchColumn() > 0;
}

/**
 * Insert a new food item into the database
 */
function insertFoodItem($pdo, $date, $item, $userId) {
    $sql = "INSERT INTO food_lists (date, item, user_id) VALUES (:date, :item, :user_id)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':item', $item);
        $stmt->bindParam(':user_id', $userId);

        $stmt->execute();
        return [true, true, "Data has been added successfully!"];
    } catch (PDOException $e) {
        return [true, false, "Database error: " . $e->getMessage()];
    }
}
?>
