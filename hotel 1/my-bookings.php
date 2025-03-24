<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    die("Please log in to view your bookings.");
}

$user_id = $_SESSION['user_id'];

// Fetch user's bookings
$query = "SELECT bookings.id AS booking_id, rooms.room_name, rooms.type, rooms.price, rooms.status, bookings.booking_date 
          FROM bookings 
          INNER JOIN rooms ON bookings.room_id = rooms.id 
          WHERE bookings.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings | Luxury Hotel</title>
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
                        <li><a href="rooms.php" class="nav-link">Rooms</a></li>
                        <li><a href="my-bookings.php" class="nav-link active">My Bookings</a></li>
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
                    <li><a href="index.php" class="nav-link block">Home</a></li>
                    <li><a href="rooms.php" class="nav-link block">Rooms</a></li>
                    <li><a href="my-bookings.php" class="nav-link active block">My Bookings</a></li>
                    <li><a href="logout.php" class="nav-link block">Logout</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <div class="bg-primary py-12 mb-8">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">My Bookings</h1>
            <p class="text-gray-200">Manage your hotel reservations</p>
        </div>
    </div>

    <section class="container mx-auto px-6 py-8 fade-in">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Your Booked Rooms
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room Number</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="bookings-table-body">
                        <?php
                        if ($result->num_rows > 0) {
                            while ($booking = $result->fetch_assoc()) {
                                $statusClass = $booking['status'] == 'booked' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800';
                        ?>
                            <tr class="booking-row hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($booking['room_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($booking['type']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₹<?= number_format($booking['price'], 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($booking['booking_date']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                        <?= ucfirst(htmlspecialchars($booking['status'])) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <?php if ($booking['status'] == 'booked') { ?>
                                        <a href="cancel-booking.php?booking_id=<?= $booking['booking_id'] ?>&room_id=<?= $booking['room_name'] ?>" 
                                           class="btn btn-danger cancel-booking"
                                           data-booking-id="<?= $booking['booking_id'] ?>"
                                           data-room-id="<?= $booking['room_name'] ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Cancel
                                        </a>
                                    <?php } else { ?>
                                        <span class="text-green-500 font-medium">Available</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    <div class="py-8">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-gray-500 text-lg">No bookings found</p>
                                        <a href="rooms.php" class="btn mt-4 inline-block">Browse Available Rooms</a>
                                    </div>
                                </td>
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
                <button id="cancel-modal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">No, Keep It</button>
                <a id="confirm-cancel" href="#" class="btn btn-danger">Yes, Cancel Booking</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-6 text-center">
            <p>All Rights Reserved By Neel Vaghasiya</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
        
        // Cancellation confirmation
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('confirmation-modal');
            const cancelModal = document.getElementById('cancel-modal');
            const confirmCancel = document.getElementById('confirm-cancel');
            const cancelButtons = document.querySelectorAll('.cancel-booking');
            
            cancelButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const bookingId = this.getAttribute('data-booking-id');
                    const roomId = this.getAttribute('data-room-id');
                    
                    confirmCancel.href = `cancel-booking.php?booking_id=${bookingId}&room_id=${roomId}`;
                    modal.classList.remove('hidden');
                });
            });
            
            cancelModal.addEventListener('click', function() {
                modal.classList.add('hidden');
            });
            
            // Animation on load
            const bookingRows = document.querySelectorAll('.booking-row');
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