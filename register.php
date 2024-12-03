<?php
require_once 'db_connection.php';

$showToast = false;
$isSuccess = true;
$toastMessage = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize form inputs
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));
    $fullName = htmlspecialchars(trim($_POST['full_name']));
    
    // Initialize validation flags
    $usernameError = "";
    $passwordError = "";
    $fullNameError = "";
    
    // Validate username
    if (empty($username)) {
        $usernameError = "Username is required.";
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        $usernameError = "Username must be between 3 and 20 characters.";
    } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        $usernameError = "Username can only contain letters, numbers, and underscores.";
    } else {
        // Check if username already exists in the database
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            $usernameError = "Username is already taken.";
        }
    }

    // Validate password
    if (empty($password)) {
        $passwordError = "Password is required.";
    }

    // Validate full name
    if (empty($fullName)) {
        $fullNameError = "Full name is required.";
    }

    // If there are no errors, proceed with registration
    if (empty($usernameError) && empty($passwordError) && empty($fullNameError)) {
        // Hash the password securely
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        // Prepare SQL query to insert user into database
        $sql = "INSERT INTO users (username, password, full_name) VALUES (:username, :password, :full_name)";
    
        // Insert the user data into the database
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':password' => $hashedPassword,
                ':full_name' => $fullName
            ]);

            $showToast = true;
            $isSuccess = true;
            $toastMessage = "Registration successful!";

            // Redirect to login page after successful registration
            header('Location: login.php');
            exit();
        } catch (PDOException $e) {
            $showToast = true;
            $isSuccess = false;
            $toastMessage = "Database error: " . $e->getMessage();
        }
    } else {
        $showToast = true;
        $isSuccess = false;
        $toastMessage = $usernameError ?: $passwordError ?: $fullNameError;
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
    <title>Register</title>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Register</h2>

        <!-- Registration Form -->
        <form action="" method="POST" class="space-y-6">

            <!-- Full Name -->
            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" name="full_name" id="full_name" required
                    class="mt-2 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Full Name"
                    value="<?= htmlspecialchars($fullName ?? '') ?>">
                <small class="text-red-500"><?= $fullNameError ?? '' ?></small>
            </div>

            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" id="username" required
                    class="mt-2 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Username"
                    value="<?= htmlspecialchars($username ?? '') ?>">
                <small class="text-red-500"><?= $usernameError ?? '' ?></small>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required
                    class="mt-2 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Password">
                <small class="text-red-500"><?= $passwordError ?? '' ?></small>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    Register
                </button>
            </div>
        </form>

    </div>

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
            <span><?php echo $toastMessage; ?></span>
        </div>
    </div>

    <script>
        // Show the toast notification for 3 seconds
        setTimeout(function() {
            var toast = document.getElementById('toast');
            toast.classList.remove('opacity-0', 'translate-x-full');
            toast.classList.add('opacity-100', 'translate-x-0');

            // Hide the toast after 3 seconds
            setTimeout(function() {
                toast.classList.remove('opacity-100', 'translate-x-0');
                toast.classList.add('opacity-0', 'translate-x-full');
            }, 3000);
        }, 100);
    </script>
    <?php endif; ?>
</body>
</html>
