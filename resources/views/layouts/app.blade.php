<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FusionBuild')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        mustardOrange: '#FFB400',
                        darkBlue: '#002147',
                        black: '#000000',
                        white: '#FFFFFF',
                        grayLight: '#F3F4F6',
                        grayDarker: '#E0E2E7',
                    }
                }
            }
        };
    </script>
</head>
<body class="bg-darkBlue text-white font-sans flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="bg-black shadow-md p-4 fixed top-0 w-full z-50 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <!-- Hamburger Menu Button (Shows on Small Screens) -->
            <button id="menuButton" class="md:hidden text-white focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 5h16v2H4V5zm0 6h16v2H4v-2zm0 6h16v2H4v-2z" clip-rule="evenodd"/>
                </svg>
            </button>
            <a>
                <img src="{{ asset('images/fusion_build_logo_2.png') }}" alt="FusionBuild" class="h-10">
            </a>
        </div>
        
        <div class="relative flex items-center space-x-2">
    <!-- Welcome message -->
    <span class="hidden md:inline-block text-white capitalize">Welcome, {{ session('firstname') }}</span>

    <!-- Profile Button Container -->
<div class="relative">
<button id="dropdownButton" class="flex items-center space-x-1 focus:outline-none 
    border-2 border-mustardOrange rounded-full p-1 
    hover:bg-mustardOrange/80 transition">

        <div class="w-8 h-8 bg-mustardOrange text-black flex items-center justify-center rounded-full text-base font-bold capitalize">
            {{ substr(session('firstname'), 0, 1) }}
        </div>
        <span class="text-white text-xs">▼</span>
    </button>

    <!-- Improved Dropdown Menu -->
    <div id="dropdownMenu" class="absolute right-0 mt-2 w-64 bg-white text-black rounded-lg shadow-lg hidden p-2">
        <a href="/Profile" class="block px-4 py-2 hover:bg-grayLight rounded">Profile</a>
        <a href="#" class="block px-4 py-2 hover:bg-grayLight rounded">Settings</a>
        <a href="/logout" class="block px-4 py-2 hover:bg-grayLight rounded">Logout</a>
    </div>
</div>


    </nav>

    <div class="flex flex-grow">

        <!-- Sidebar (Hidden on Small Screens) -->
        <aside id="sidebar" class="w-64 bg-black text-white p-6 shadow-lg fixed top-16 left-0 h-[calc(100vh-64px)] overflow-y-auto hidden md:block z-50">
            <ul>
                <li><a href="/Dashboard" class="block py-2 px-4 hover:bg-darkBlue rounded">Dashboard</a></li>
                <li><a href="/projects" class="block py-2 px-4 hover:bg-darkBlue rounded">Projects</a></li>
                <li>
                    <button id="financialDropdownButton" class="w-full text-left py-2 px-4 flex justify-between items-center hover:bg-darkBlue rounded">
                        Financial Overview
                        <span id="financialArrow" class="text-xs">▼</span>
                    </button>
                    <ul id="financialDropdown" class="hidden pl-4">
                        <li><a href="/income" class="block py-2 px-4 hover:bg-darkBlue rounded">Income</a></li>
                        <li><a href="/expenses" class="block py-2 px-4 hover:bg-darkBlue rounded">Expenses</a></li>
                    </ul>
                </li>
            </ul>
        </aside>

        <!-- Page Content -->
        <main class="bg-grayDarker flex-grow md:ml-64 mt-16 overflow-y-auto h-[calc(100vh-64px)]">
            @yield('content')
        </main>

    </div>

    <script>
        // Sidebar Toggle for Mobile
        document.getElementById("menuButton").addEventListener("click", function() {
            let sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("hidden");
        });

        // Close sidebar when clicking outside (for mobile)
        document.addEventListener("click", function(event) {
            let sidebar = document.getElementById("sidebar");
            let menuButton = document.getElementById("menuButton");

            if (!sidebar.contains(event.target) && !menuButton.contains(event.target) && !sidebar.classList.contains("md:block")) {
                sidebar.classList.add("hidden");
            }
        });

        // Dropdown toggle for profile menu
        document.getElementById("dropdownButton").addEventListener("click", function(event) {
            event.stopPropagation();
            let dropdownMenu = document.getElementById("dropdownMenu");
            dropdownMenu.classList.toggle("hidden");
        });

        document.addEventListener("click", function(event) {
            let dropdownMenu = document.getElementById("dropdownMenu");
            if (!document.getElementById("dropdownButton").contains(event.target)) {
                dropdownMenu.classList.add("hidden");
            }
        });

        // Toggle financial dropdown
        document.getElementById("financialDropdownButton").addEventListener("click", function() {
            let dropdown = document.getElementById("financialDropdown");
            let arrow = document.getElementById("financialArrow");

            dropdown.classList.toggle("hidden");

            // Toggle arrow direction
            if (dropdown.classList.contains("hidden")) {
                arrow.innerHTML = "▼";
            } else {
                arrow.innerHTML = "▲";
            }
        });
    </script>

</body>
</html>
