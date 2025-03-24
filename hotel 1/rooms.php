<?php
require 'includes/db.php';

$query = "SELECT * FROM rooms";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Rooms | Luxury Hotel</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-background">

    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto py-4 px-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl md:text-3xl font-bold text-primary">
                    <a href="index.php"><span class="text-accent">Luxury</span> Hotel</a>
                </h1>
                <nav class="hidden md:block">
                    <ul class="flex space-x-6">
                        <li><a href="index.php" class="nav-link">Home</a></li>
                        <li><a href="rooms.php" class="nav-link active">Rooms</a></li>
                        <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                    </ul>
                </nav>
                <button id="mobile-menu-button" class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden mt-4">
                <ul class="flex flex-col space-y-2">
                    <li><a href="index.php" class="nav-link block">Home</a></li>
                    <li><a href="rooms.php" class="nav-link active block">Rooms</a></li>
                    <li><a href="dashboard.php" class="nav-link block">Dashboard</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <div class="bg-primary py-12 mb-8">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Available Rooms</h1>
            <p class="text-gray-200">Find your perfect accommodation</p>
        </div>
    </div>

    <section class="container mx-auto px-6 py-8 fade-in">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-primary mb-4 md:mb-0">Our Rooms</h2>
            <div class="flex space-x-4">
                <select id="filter-type" class="form-input py-2 px-4 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="all">All Types</option>
                    <option value="standard">Standard</option>
                    <option value="deluxe">Deluxe</option>
                    <option value="luxury">Luxury</option>
                </select>
                <select id="filter-status" class="form-input py-2 px-4 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="all">All Status</option>
                    <option value="available">Available</option>
                    <option value="booked">Booked</option>
                </select>
            </div>
        </div>

        <div class="overflow-hidden shadow-md rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room Number</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="rooms-table-body">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($room = $result->fetch_assoc()) {
                            $statusClass = $room['status'] == 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                            
                            echo "<tr class='hover:bg-gray-50 transition-colors duration-150 ease-in-out room-row' 
                                      data-type='" . strtolower(htmlspecialchars($room['type'] ?? 'standard')) . "' 
                                      data-status='" . strtolower(htmlspecialchars($room['status'] ?? 'unavailable')) . "'>
                                    <td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>" . htmlspecialchars($room['room_number'] ?? 'N/A') . "</td>
                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>" . htmlspecialchars($room['type'] ?? 'N/A') . "</td>
                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>₹" . number_format($room['price'] ?? 0, 2) . "</td>
                                    <td class='px-6 py-4 whitespace-nowrap'>
                                        <span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full $statusClass'>
                                            " . ucfirst(htmlspecialchars($room['status'] ?? 'Unavailable')) . "
                                        </span>
                                    </td>
                                    <td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>";
                            
                            if ($room['status'] == 'available') {
                                echo "<a href='book-room.php?room_id={$room['id']}' class='btn scale-in'>Book Now</a>";
                            } else {
                                echo "<span class='text-red-500 font-medium'>Booked</span>";
                            }

                            echo "</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='px-6 py-4 text-center text-sm text-gray-500'>No rooms available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2025 Luxury Hotel. All Rights Reserved.</p>
            <div class="mt-4 flex justify-center space-x-6">
                <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                    <span class="sr-only">Facebook</span>
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path>
                    </svg>
                </a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                    <span class="sr-only">Instagram</span>
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                    </svg>
                </a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                    <span class="sr-only">Twitter</span>
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                    </svg>
                </a>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
        
        // Room filtering
        document.addEventListener('DOMContentLoaded', function() {
            const typeFilter = document.getElementById('filter-type');
            const statusFilter = document.getElementById('filter-status');
            const roomRows = document.querySelectorAll('.room-row');
            
            function filterRooms() {
                const typeValue = typeFilter.value;
                const statusValue = statusFilter.value;
                
                roomRows.forEach(row => {
                    const rowType = row.getAttribute('data-type');
                    const rowStatus = row.getAttribute('data-status');
                    
                    const typeMatch = typeValue === 'all' || rowType === typeValue;
                    const statusMatch = statusValue === 'all' || rowStatus === statusValue;
                    
                    if (typeMatch && statusMatch) {
                        row.classList.remove('hidden');
                        // Add animation
                        row.classList.add('scale-in');
                        setTimeout(() => {
                            row.classList.remove('scale-in');
                        }, 300);
                    } else {
                        row.classList.add('hidden');
                    }
                });
            }
            
            typeFilter.addEventListener('change', filterRooms);
            statusFilter.addEventListener('change', filterRooms);
            
            // Animation on load
            roomRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                row.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                
                setTimeout(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, 100 + (index * 50));
            });
        });
    </script>
</body>
</html>