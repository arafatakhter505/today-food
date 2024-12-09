<?php 
require_once 'function.php'; 
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
    <?php require "nav.php"; ?>

    <!-- Main Section for Food Submission and Display -->
    <section class="mx-auto max-w-7xl p-5 sm:p-6 lg:p-8 flex items-start gap-10 justify-between md:flex-row flex-col">
        
        <!-- Form Section for submitting food -->
        <div class="md:w-[1/2] w-full">
            <form action="" method="POST">
                <!-- Date Input Field -->
                <div class="mb-4">
                    <label for="date" class="block text-gray-700 font-medium mb-2">Date</label>
                    <input type="date" id="date" name="date" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="<?php echo date('Y-m-d'); ?>" readonly required />
                </div>

                <!-- Food Item Input Field -->
                <div class="mb-6">
                    <label for="item" class="block text-gray-700 font-medium mb-2">Item</label>
                    <input type="text" id="item" name="item" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required />
                </div>

                <!-- Submit Button -->
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
                    <?php foreach ($usersList as $user): ?>
                        <tr class="border-t">
                            <td class="py-2 px-4 text-gray-700"><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td class="py-2 px-4 text-gray-700"><?php echo htmlspecialchars(getUserFoodToday($user['username'], $foodLists)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Toast Notification for Success or Error -->
    <?php if ($showToast): ?>
        <div id="toast" class="fixed bottom-5 right-5 max-w-xs p-4 mb-4 text-white <?php echo $isSuccess ? 'bg-green-600' : 'bg-red-600'; ?> rounded-lg shadow-md opacity-0 transform translate-x-full transition-all duration-500">
            <div class="flex items-center">
                <!-- Success or Error Icon -->
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
            // Show toast notification for 3 seconds
            setTimeout(function () {
                const toast = document.getElementById("toast");
                toast.classList.remove("opacity-0");
                toast.classList.add("opacity-100", "translate-x-0");
            }, 100);

            setTimeout(function () {
                const toast = document.getElementById("toast");
                toast.classList.remove("opacity-100");
                toast.classList.add("opacity-0", "translate-x-full");
            }, 3000);
        </script>
    <?php endif; ?>
</body>

</html>
