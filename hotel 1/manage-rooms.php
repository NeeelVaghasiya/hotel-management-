<?php
session_start();
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_name = $_POST['room_name'];
    $type = $_POST['type'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO rooms (room_name, type, price, status) VALUES (?, ?, ?, 'available')");
    $stmt->bind_param("ssd", $room_name, $type, $price);
    $stmt->execute();

    echo "<script>alert('✅ Room added successfully!'); window.location='manage-rooms.php';</script>";
}

$result = $conn->query("SELECT * FROM rooms");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms | Luxury Hotel</title>
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
                        <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                        <li><a href="manage-rooms.php" class="nav-link active">Manage Rooms</a></li>
                        <li><a href="manage-bookings.php" class="nav-link">Manage Bookings</a></li>
                        <li><a href="logout.php" class="nav-link">Logout</a></li>
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
                    <li><a href="dashboard.php" class="nav-link block">Dashboard</a></li>
                    <li><a href="manage-rooms.php" class="nav-link active block">Manage Rooms</a></li>
                    <li><a href="manage-bookings.php" class="nav-link block">Manage Bookings</a></li>
                    <li><a href="logout.php" class="nav-link block">Logout</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <div class="bg-primary py-12 mb-8">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Manage Rooms</h1>
            <p class="text-gray-200">Add, edit, and remove hotel rooms</p>
        </div>
    </div>

    <div class="container mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Room List -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden fade-in">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Room Inventory
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price ($)</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="rooms-table-body">
                                <?php 
                                if ($result && $result->num_rows > 0) {
                                    while ($room = $result->fetch_assoc()) { 
                                        $statusClass = $room['status'] == 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                ?>
                                    <tr class="room-row hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $room['id'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($room['room_name']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($room['type'] ?? 'Standard') ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₹<?= number_format($room['price'], 2) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                                <?= ucfirst($room['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button class="btn btn-danger delete-room" data-room-id="<?= $room['id'] ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php 
                                    }
                                } else {
                                ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No rooms found</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Add Room Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md overflow-hidden slide-in">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add New Room
                        </h2>
                    </div>
                    <div class="p-6">
                        <form method="POST" class="space-y-4">
                            <div>
                                <label for="room_name" class="block text-sm font-medium text-gray-700">Room Name</label>
                                <input type="text" name="room_name" id="room_name" placeholder="e.g. Room 101" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>
                            
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Room Type</label>
                                <select name="type" id="type" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    <option value="Standard">Standard</option>
                                    <option value="Deluxe">Deluxe</option>
                                    <option value="Luxury">Luxury Suite</option>
                                   
                                </select>
                            </div>
                            
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">Price per Night ($)</label>
                                <input type="number" name="price" id="price" placeholder="e.g. 99.99" step="0.01" min="0" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>
                            
                            <button type="submit" class="w-full btn bg-primary hover:bg-primary-dark transition-colors duration-300">
                                
                                Add Room
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden p-6 slide-in" style="animation-delay: 0.2s;">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Room Management Tips</h3>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Keep room names consistent (e.g., Room 101, Suite 202)</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Update room status promptly when bookings change</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Regularly review pricing based on demand and season</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-8 max-w-md w-full scale-in">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Confirm Deletion</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to delete this room? This action cannot be undone.</p>
            <div class="flex justify-end space-x-4">
                <button id="cancel-modal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">Cancel</button>
                <a id="confirm-delete" href="#" class="btn btn-danger">Delete Room</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-6 text-center">
            <p> All Rights Reserved By Neel Vaghasiya</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
        
        // Delete room confirmation
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('confirmation-modal');
            const cancelModal = document.getElementById('cancel-modal');
            const confirmDelete = document.getElementById('confirm-delete');
            const deleteButtons = document.querySelectorAll('.delete-room');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const roomId = this.getAttribute('data-room-id');
                    confirmDelete.href = `delete-room.php?id=${roomId}`;
                    modal.classList.remove('hidden');
                });
            });
            
            cancelModal.addEventListener('click', function() {
                modal.classList.add('hidden');
            });
            
            // Animation on load
            const roomRows = document.querySelectorAll('.room-row');
            roomRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                row.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                
                setTimeout(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, 100 + (index * 50));
            });
            
            // Form animation
            const form = document.querySelector('form');
            form.addEventListener('submit', function() {
                this.classList.add('scale-in');
            });
            
            // Input animations
            const inputs = document.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.classList.add('ring-2', 'ring-primary', 'ring-opacity-50');
                });
                
                input.addEventListener('blur', function() {
                    this.classList.remove('ring-2', 'ring-primary', 'ring-opacity-50');
                });
            });
        });
    </script>
</body>
</html>