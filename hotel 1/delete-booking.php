<?php
session_start();
require 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to cancel a booking.'); window.location='login.php';</script>";
    exit();
}

if (isset($_GET['booking_id']) && isset($_GET['room_id'])) {
    $booking_id = $_GET['booking_id'];
    $room_id = $_GET['room_id'];
    
    // Check if booking exists
    $check = $conn->prepare("SELECT id, user_id FROM bookings WHERE id = ?");
    $check->bind_param("i", $booking_id);
    $check->execute();
    $check->store_result();
    $check->bind_result($id, $user_id);
    $check->fetch();
    
    // Verify booking exists and belongs to current user (or user is admin)
    if ($check->num_rows > 0 && ($user_id == $_SESSION['user_id'] || $_SESSION['role'] === 'admin')) {
        // Delete the booking
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        $result = $stmt->execute();
        
        if ($result) {
            // Update room status to "available"
            $updateRoom = $conn->prepare("UPDATE rooms SET status = 'available' WHERE id = ?");
            $updateRoom->bind_param("i", $room_id);
            $updateRoom->execute();
            
            // Redirect based on user role
            if ($_SESSION['role'] === 'admin') {
                echo "<script>
                    window.location='manage-bookings.php';
                    setTimeout(function() {
                        window.showNotification('Booking cancelled successfully', 'success');
                    }, 500);
                </script>";
            } else {
                echo "<script>
                    window.location='my-bookings.php';
                    setTimeout(function() {
                        window.showNotification('Booking cancelled successfully', 'success');
                    }, 500);
                </script>";
            }
        } else {
            echo "<script>alert('Error cancelling booking. Please try again.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Invalid booking or you do not have permission to cancel this booking.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
}
?>