<nav class="bg-white shadow">
  <div class="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">
    <div class="relative flex h-16 items-center justify-between">
      <a href="index.php" class="flex flex-1 items-center gap-5">
        <div class="flex shrink-0 items-center">
          <img class="h-10 w-auto" src="./images/logo.svg" alt="food">
        </div>
        <h3 class="font-medium text-xl">Today Food</h3>
      </a>
      <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
        <!-- Profile dropdown -->
        <div class="relative ml-3">
          <div>
            <button type="button" class="relative flex rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
              <span class="absolute -inset-1.5"></span>
              <span class="sr-only">Open user menu</span>
              <img class="size-8 rounded-full" src="./images/profile.svg" alt="profile">
            </button>
          </div>
          
          <!-- Dropdown menu -->
          <div id="user-menu-dropdown" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
            <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>
            <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-1">Settings</a>
            <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2">Sign out</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</nav>

<!-- JavaScript to handle dropdown toggle and close on click outside -->
<script>
  const userMenuButton = document.getElementById('user-menu-button');
  const dropdownMenu = document.getElementById('user-menu-dropdown');

  // Toggle the dropdown menu visibility when clicking the profile button
  userMenuButton.addEventListener('click', function (event) {
    // Prevent the event from propagating to the document click handler
    event.stopPropagation();
    dropdownMenu.classList.toggle('hidden');
  });

  // Close the dropdown menu when clicking anywhere outside of the menu
  document.addEventListener('click', function (event) {
    const isClickInside = dropdownMenu.contains(event.target) || userMenuButton.contains(event.target);
    
    if (!isClickInside) {
      dropdownMenu.classList.add('hidden');
    }
  });
</script>
