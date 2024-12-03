<?php
require_once 'db_connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // If not logged in, redirect to login page
    header('Location: login.php');
    exit();  // Exit after redirect to avoid further script execution
}

// Get the username from the session
$username = $_SESSION['username'];

// Fetch the current user ID using the username
$userId = getUserIdByUsername($pdo, $username);

// Fetch food list data for the logged-in user
$foodLists = getFoodListsForUser($pdo, $userId);

/**
 * Fetch user ID by username
 *
 * @param PDO $pdo
 * @param string $username
 * @return int|null
 */
function getUserIdByUsername(PDO $pdo, string $username): ?int {
    $sql = "SELECT id FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ? (int)$user['id'] : null; // Return user ID or null if not found
}

/**
 * Fetch food lists for a specific user
 *
 * @param PDO $pdo
 * @param int $userId
 * @return array
 */
function getFoodListsForUser(PDO $pdo, int $userId): array {
    $sql = "SELECT date, item FROM food_lists WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $userId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return food list array
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./images/logo.svg" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Profile</title>
</head>
<body>
    <?php require "nav.php"; // Include navigation ?>

    <div class="overflow-x-auto mx-auto max-w-7xl p-5 sm:p-6 lg:p-8">
        <table class="min-w-full table-auto border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Date</th>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Item</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($foodLists as $food): ?>
                    <tr class="border-t">
                        <td class="px-4 py-2"><?php echo htmlspecialchars($food['date']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($food['item']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
