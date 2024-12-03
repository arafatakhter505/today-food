<?php
require_once 'db_connection.php';

// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit();  // Stop further script execution after the redirect
}

$username = $_SESSION['username'];
$showToast = false;
$isSuccess = true;
$toastMessage = "";

// Fetch food lists data for today from the database
$foodLists = getFoodListsForToday($pdo);

// Fetch the current user ID using the username
$userId = getUserIdByUsername($pdo, $username);

// Check if the user has already added a food item for today
$userHasAddedFoodToday = checkUserFoodToday($pdo, $userId);

// Handle form submission for adding new food items
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$userHasAddedFoodToday) {
    $date = $_POST['date'];
    $item = $_POST['item'];

    // Insert the food item into the database
    list($showToast, $isSuccess, $toastMessage) = insertFoodItem($pdo, $date, $item, $userId);
    
    // After the form is submitted, redirect the page to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

/**
 * Fetch food lists for today
 *
 * @param PDO $pdo
 * @return array
 */
function getFoodListsForToday($pdo) {
    $sql = "SELECT food_lists.item, users.full_name 
            FROM users 
            JOIN food_lists ON food_lists.user_id = users.id 
            WHERE DATE(food_lists.date) = CURDATE()";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetch user ID by username
 *
 * @param PDO $pdo
 * @param string $username
 * @return int|null
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
 *
 * @param PDO $pdo
 * @param int $userId
 * @return bool
 */
function checkUserFoodToday($pdo, $userId) {
    $sql = "SELECT COUNT(*) FROM food_lists WHERE user_id = :user_id AND DATE(date) = CURDATE()";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $userId]);

    return $stmt->fetchColumn() > 0;  // Returns true if user has added food today, false otherwise
}

/**
 * Insert a new food item into the database
 *
 * @param PDO $pdo
 * @param string $date
 * @param string $item
 * @param int $userId
 * @return array
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./images/logo.svg" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Today Food</title>
</head>
<body>
    <?php require "nav.php" ?>

    <section class="mx-auto max-w-7xl p-5 sm:p-6 lg:p-8 flex items-start gap-10 justify-between md:flex-row flex-col">
        <!-- Form Section -->
        <div class="md:w-[1/2] w-full">
            <form action="" method="POST">
                <div class="mb-4">
                    <label for="date" class="block text-gray-700 font-medium mb-2">Date</label>
                    <input type="date" id="date" name="date" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="<?php echo date('Y-m-d'); ?>" readonly required />
                </div>

                <div class="mb-6">
                    <label for="item" class="block text-gray-700 font-medium mb-2">Item</label>
                    <input type="text" id="item" name="item" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required />
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="w-full px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500" <?php echo $userHasAddedFoodToday ? 'disabled' : ''; ?>>
                        <?php echo $userHasAddedFoodToday ? 'Already Submitted Today' : 'Submit'; ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- Food List Table -->
        <div class="md:w-[1/2] w-full">
            <table class="min-w-full bg-white border border-gray-300 shadow-md">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="py-2 px-4 text-left text-gray-600 font-semibold">Name</th>
                        <th class="py-2 px-4 text-left text-gray-600 font-semibold">Item</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($foodLists as $food): ?>
                    <tr class="border-t">
                        <td class="py-2 px-4 text-gray-700"><?php echo htmlspecialchars($food['full_name']); ?></td>
                        <td class="py-2 px-4 text-gray-700"><?php echo htmlspecialchars($food['item']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Toast Notification -->
    <?php if ($showToast): ?>
    <div id="toast" class="fixed bottom-5 right-5 max-w-xs p-4 mb-4 text-white <?php echo $isSuccess ? 'bg-green-600' : 'bg-red-600'; ?> rounded-lg shadow-md opacity-0 transform translate-x-full transition-all duration-500">
        <div class="flex items-center">
            <?php if ($isSuccess): ?>
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <?php else: ?>
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4L20 20M4 20L20 4"></path>
            </svg>
            <?php endif; ?>
            <span><?php echo htmlspecialchars($toastMessage); ?></span>
        </div>
    </div>

    <script>
        // Show the toast notification for 3 seconds
        setTimeout(function() {
            var toast = document.getElementById("toast");
            toast.classList.remove("opacity-0");
            toast.classList.add("opacity-100");
            toast.classList.add("translate-x-0");
        }, 100);
        setTimeout(function() {
            var toast = document.getElementById("toast");
            toast.classList.remove("opacity-100");
            toast.classList.add("opacity-0");
            toast.classList.add("translate-x-full");
        }, 3000);
    </script>
    <?php endif; ?>
</body>
</html>
