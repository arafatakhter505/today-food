<?php
// Start the session
session_start();

// Include database connection
require_once 'db_connection.php'; // Adjust the path as needed

// Define variables for error handling
$showToast = false;
$errorMessage = '';

// Function to validate and sanitize form input
function sanitizeInput($input)
{
    return htmlspecialchars(trim($input));
}

// Function to authenticate user
function authenticateUser($pdo, $username, $password)
{
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Process the login request if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input to prevent XSS and other attacks
    $username = sanitizeInput($_POST['username']);
    $password = sanitizeInput($_POST['password']);

    // Authenticate the user
    $user = authenticateUser($pdo, $username, $password);

    if ($user && password_verify($password, $user['password'])) {
        // Set session variables upon successful login
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $username;

        // Redirect to the protected page
        header('Location: index.php');
        exit();
    } else {
        // Set error message and show toast
        $showToast = true;
        $errorMessage = 'Invalid username or password';
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
    <title>Login</title>
</head>

<body class="min-h-screen bg-gray-50">

    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img class="mx-auto h-20 w-auto" src="./images/logo.svg" alt="food">
            <h2 class="mt-10 text-center text-2xl font-bold tracking-tight text-gray-900">Sign in to your account</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" action="#" method="POST">
                <!-- Username input -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-900">Username</label>
                    <div class="mt-2">
                        <input type="text" name="username" id="username" autocomplete="username" required
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:outline-indigo-600 sm:text-sm">
                    </div>
                </div>

                <!-- Password input -->
                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-gray-900">Password</label>
                    </div>
                    <div class="mt-2">
                        <input type="password" name="password" id="password" autocomplete="current-password" required
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:outline-indigo-600 sm:text-sm">
                    </div>
                </div>

                <!-- Submit button -->
                <div>
                    <button type="submit"
                        class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-indigo-600">
                        Sign in
                    </button>
                </div>
            </form>

            <p class="mt-10 text-center text-sm text-gray-500">
                Not a member? 
                <a href="register.php" class="font-semibold text-indigo-600 hover:text-indigo-500">Sign Up</a>
            </p>
        </div>
    </div>

    <!-- Toast Notification -->
    <?php if ($showToast): ?>
    <div id="toast" class="fixed bottom-5 right-5 max-w-xs p-4 mb-4 text-white bg-red-600 rounded-lg shadow-md opacity-0 transform translate-x-full transition-all duration-500">
        <div class="flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4L20 20M4 20L20 4"></path>
            </svg>

            <span><?php echo $errorMessage; ?></span>
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
