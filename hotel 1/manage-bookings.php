<?php
session_start();
require 'includes/db.php';

// Fetch all bookings
$result = $conn->query("SELECT bookings.id, users.username, rooms.id AS room_id, rooms.room_name, bookings.booking_date 
                        FROM bookings 
                        JOIN users ON bookings.user_id = users.id 
                        JOIN rooms ON bookings.room_id = rooms.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings | Luxury Hotel</title>
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
                        <li><a href="manage-rooms.php" class="nav-link">Manage Rooms</a></li>
                        <li><a href="manage-bookings.php" class="nav-link active">Manage Bookings</a></li>
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
                    <li><a href="manage-rooms.php" class="nav-link block">Manage Rooms</a></li>
                    <li><a href="manage-bookings.php" class="nav-link active block">Manage Bookings</a></li>
                    <li><a href="logout.php" class="nav-link block">Logout</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <div class="bg-primary py-12 mb-8">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Manage Bookings</h1>
            <p class="text-gray-200">View and manage all hotel bookings</p>
        </div>
    </div>

    <section class="container mx-auto px-6 py-8 fade-in">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-primary mb-4 md:mb-0">All Bookings</h2>
            <div class="flex space-x-4">
                <input type="text" id="search-input" placeholder="Search by username..." class="form-input py-2 px-4 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                <button id="search-button" class="btn">Search</button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="bookings-table-body">
                        <?php 
                        if ($result && $result->num_rows > 0) {
                            while ($booking = $result->fetch_assoc()) { 
                        ?>
                            <tr class="booking-row hover:bg-gray-50 transition-colors duration-150 ease-in-out" data-username="<?= strtolower(htmlspecialchars($booking['username'])) ?>">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($booking['id']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($booking['username']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($booking['room_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($booking['booking_date']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="delete-booking.php?booking_id=<?= $booking['id'] ?>&room_id=<?= $booking['room_id'] ?>" 
                                       class="btn btn-danger flex items-center space-x-1 delete-booking"
                                       data-booking-id="<?= $booking['id'] ?>"
                                       data-room-id="<?= $booking['room_id'] ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span>Cancel Booking</span>
                                    </a>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No bookings found</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Confirmation Modal -->
    <div id="confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-8 max-w-md w-full scale-in">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Confirm Cancellation</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to cancel this booking? This action cannot be undone.</p>
            <div class="flex justify-end space-x-4">
                <button id="cancel-modal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">Cancel</button>
                <a id="confirm-delete" href="#" class="btn btn-danger">Confirm Cancellation</a>
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
        
        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const searchButton = document.getElementById('search-button');
            const bookingRows = document.querySelectorAll('.booking-row');
            
            function searchBookings() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                
                bookingRows.forEach(row => {
                    const username = row.getAttribute('data-username');
                    
                    if (username.includes(searchTerm) || searchTerm === '') {
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
            
            searchButton.addEventListener('click', searchBookings);
            searchInput.addEventListener('keyup', function(event) {
                if (event.key === 'Enter') {
                    searchBookings();
                }
            });
            
            // Confirmation modal
            const modal = document.getElementById('confirmation-modal');
            const cancelModal = document.getElementById('cancel-modal');
            const confirmDelete = document.getElementById('confirm-delete');
            const deleteButtons = document.querySelectorAll('.delete-booking');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const bookingId = this.getAttribute('data-booking-id');
                    const roomId = this.getAttribute('data-room-id');
                    
                    confirmDelete.href = `delete-booking.php?booking_id=${bookingId}&room_id=${roomId}`;
                    modal.classList.remove('hidden');
                });
            });
            
            cancelModal.addEventListener('click', function() {
                modal.classList.add('hidden');
            });
            
            // Animation on load
            bookingRows.forEach((row, index) => {
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