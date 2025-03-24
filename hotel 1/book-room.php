<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to book a room.'); window.location='login.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST['room_id'];
    $user_id = $_SESSION['user_id'];
    $check_in_date = $_POST['check_in_date'] ?? date('Y-m-d');
    $check_out_date = $_POST['check_out_date'] ?? date('Y-m-d', strtotime('+1 day'));

    // Check if the room is already booked
    $check_room = $conn->prepare("SELECT status FROM rooms WHERE id = ?");
    $check_room->bind_param("i", $room_id);
    $check_room->execute();
    $check_room->store_result();
    $check_room->bind_result($room_status);
    $check_room->fetch();
    $check_room->close();

    if ($room_status === 'booked') {
        echo "<script>alert('❌ This room is already booked!'); window.location='rooms.php';</script>";
        exit();
    }

    // Book the room (Insert booking & mark as booked)
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, booking_date) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $user_id, $room_id);
    $stmt->execute();
    $stmt->close();

    // Update room status
    $update = $conn->prepare("UPDATE rooms SET status = 'booked' WHERE id = ?");
    $update->bind_param("i", $room_id);
    $update->execute();
    $update->close();

    echo "<script>alert('✅ Room booked successfully!'); window.location='my-bookings.php';</script>";
}

// Get available rooms
$query = "SELECT * FROM rooms WHERE status = 'available'";
$result = $conn->query($query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Room | Luxury Hotel</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="js/script.js"></script>
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
                        <li><a href="book-room.php" class="nav-link active">Book Room</a></li>
                        <li><a href="my-bookings.php" class="nav-link">My Bookings</a></li>
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
                    <li><a href="book-room.php" class="nav-link active block">Book Room</a></li>
                    <li><a href="my-bookings.php" class="nav-link block">My Bookings</a></li>
                    <li><a href="logout.php" class="nav-link block">Logout</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <div class="bg-primary py-12 mb-8">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Book Your Perfect Stay</h1>
            <p class="text-gray-200">Choose from our selection of premium rooms</p>
        </div>
    </div>

    <!-- Notification Messages -->
    <?php if (isset($success_message)): ?>
        <div class="container mx-auto px-6 mb-8">
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded fade-in" role="alert">
                <p class="font-medium">Success!</p>
                <p><?= $success_message ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="container mx-auto px-6 mb-8">
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded fade-in" role="alert">
                <p class="font-medium">Error!</p>
                <p><?= $error_message ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Available Rooms Section -->
    <section class="container mx-auto px-6 py-8">
        <h2 class="text-2xl font-bold text-primary mb-8 text-center">Available Rooms</h2>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while ($room = $result->fetch_assoc()): ?>
                    <div class="card group hover:shadow-xl transition-all duration-300 <?= ($selected_room_id == $room['id']) ? 'ring-2 ring-primary' : '' ?>">
                        <div class="relative overflow-hidden">
                            <img src="<?= isset($room['image']) ? $room['image'] : 'assets/images/room' . $room['id'] . '.jpg' ?>" 
                                 onerror="this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1740&q=80'"
                                 alt="<?= htmlspecialchars($room['room_name']) ?>" 
                                 class="w-full h-64 object-cover transform group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute top-4 right-4 bg-accent text-white px-3 py-1 rounded-full text-sm font-medium">
                                Available
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2 text-primary"><?= htmlspecialchars($room['room_name']) ?></h3>
                            <p class="text-gray-600 mb-4"><?= htmlspecialchars($room['type'] ?? 'Standard Room') ?></p>
                            
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-xl font-bold text-accent">₹<?= number_format($room['price'], 2) ?><span class="text-sm text-gray-500">/night</span></span>
                            </div>
                            
                            <button class="w-full btn book-btn" 
                                    data-room-id="<?= $room['id'] ?>" 
                                    data-room-name="<?= htmlspecialchars($room['room_name']) ?>"
                                    data-room-price="<?= $room['price'] ?>">
                                Book Now
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Rooms Available</h3>
                <p class="text-gray-500 mb-6">Sorry, all our rooms are currently booked. Please check back later.</p>
                <a href="index.php" class="btn">Return to Home</a>
            </div>
        <?php endif; ?>
    </section>

    <!-- Booking Modal -->
    <div id="booking-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-8 max-w-md w-full scale-in">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Confirm Your Booking</h3>
                <button id="close-modal" class="text-gray-400 hover:text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form method="POST" id="booking-form" class="space-y-4">
                <input type="hidden" name="room_id" id="room-id">
                
                <div>
                    <p class="text-gray-700 mb-2">You are about to book:</p>
                    <p class="text-lg font-semibold text-primary" id="room-name-display"></p>
                    <p class="text-accent font-bold" id="room-price-display"></p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="check_in_date" class="block text-sm font-medium text-gray-700 mb-1">Check-in Date</label>
                        <input type="date" name="check_in_date" id="check_in_date" required
                               min="<?= date('Y-m-d') ?>"
                               value="<?= date('Y-m-d') ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>
                    
                    <div>
                        <label for="check_out_date" class="block text-sm font-medium text-gray-700 mb-1">Check-out Date</label>
                        <input type="date" name="check_out_date" id="check_out_date" required
                               min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                               value="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>
                </div>
                
                <div>
                    <label for="special_requests" class="block text-sm font-medium text-gray-700 mb-1">Special Requests (optional)</label>
                    <textarea name="special_requests" id="special_requests" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                              placeholder="Any special requests or requirements?"></textarea>
                </div>
                
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500 mb-4">By clicking "Confirm Booking", you agree to our terms and conditions.</p>
                    
                    <div class="flex space-x-4">
                        <button type="button" id="cancel-booking" class="flex-1 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Confirm Booking
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-6 text-center">
            <p> All Rights Reserved By Neel Vaghasiya</p>
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = document.getElementById("booking-modal");
            const roomIdInput = document.getElementById("room-id");
            const roomNameDisplay = document.getElementById("room-name-display");
            const roomPriceDisplay = document.getElementById("room-price-display");
            const closeModal = document.getElementById("close-modal");
            const cancelBooking = document.getElementById("cancel-booking");
            const checkInDate = document.getElementById("check_in_date");
            const checkOutDate = document.getElementById("check_out_date");
            
            // Open modal when book button is clicked
            document.querySelectorAll(".book-btn").forEach(button => {
                button.addEventListener("click", function() {
                    const roomId = this.dataset.roomId;
                    const roomName = this.dataset.roomName;
                    const roomPrice = this.dataset.roomPrice;
                    
                    roomIdInput.value = roomId;
                    roomNameDisplay.textContent = roomName;
                    roomPriceDisplay.textContent = `$${parseFloat(roomPrice).toFixed(2)} per night`;
                    
                    modal.classList.remove("hidden");
                    modal.classList.add("fade-in");
                });
            });
            
            // Close modal
            function closeModalHandler() {
                modal.classList.add("hidden");
            }
            
            closeModal.addEventListener("click", closeModalHandler);
            cancelBooking.addEventListener("click", closeModalHandler);
            
            // Close modal when clicking outside
            modal.addEventListener("click", function(e) {
                if (e.target === modal) {
                    closeModalHandler();
                }
            });
            
            // Ensure check-out date is after check-in date
            checkInDate.addEventListener("change", function() {
                const checkInValue = new Date(this.value);
                const nextDay = new Date(checkInValue);
                nextDay.setDate(nextDay.getDate() + 1);
                
                const formattedDate = nextDay.toISOString().split('T')[0];
                checkOutDate.min = formattedDate;
                
                if (new Date(checkOutDate.value) <= checkInValue) {
                    checkOutDate.value = formattedDate;
                }
            });
            
            // Calculate total price based on dates
            function updateTotalPrice() {
                const checkIn = new Date(checkInDate.value);
                const checkOut = new Date(checkOutDate.value);
                const roomPrice = parseFloat(document.querySelector(".book-btn").dataset.roomPrice);
                
                // Calculate number of nights
                const timeDiff = checkOut.getTime() - checkIn.getTime();
                const nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
                
                if (nights > 0 && !isNaN(roomPrice)) {
                    const totalPrice = nights * roomPrice;
                    roomPriceDisplay.textContent = `$${roomPrice.toFixed(2)} per night × ${nights} nights = $${totalPrice.toFixed(2)}`;
                }
            }
            
            checkInDate.addEventListener("change", updateTotalPrice);
            checkOutDate.addEventListener("change", updateTotalPrice);
            
            // Form validation
            const bookingForm = document.getElementById("booking-form");
            bookingForm.addEventListener("submit", function(e) {
                const checkIn = new Date(checkInDate.value);
                const checkOut = new Date(checkOutDate.value);
                
                if (checkOut <= checkIn) {
                    e.preventDefault();
                    alert("Check-out date must be after check-in date");
                }
            });
            
            // Animation for room cards
            const roomCards = document.querySelectorAll('.card');
            roomCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 + (index * 50));
            });
        });
    </script>
</body>
</html>