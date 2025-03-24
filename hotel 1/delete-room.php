<?php
session_start();
require 'includes/db.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access denied. Only admins can delete rooms.'); window.location='index.php';</script>";
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Check if room exists
    $check = $conn->prepare("SELECT id FROM rooms WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows > 0) {
        // Check if room has bookings
        $bookingCheck = $conn->prepare("SELECT id FROM bookings WHERE room_id = ?");
        $bookingCheck->bind_param("i", $id);
        $bookingCheck->execute();
        $bookingCheck->store_result();
        
        if ($bookingCheck->num_rows > 0) {
            // Delete associated bookings first
            $deleteBookings = $conn->prepare("DELETE FROM bookings WHERE room_id = ?");
            $deleteBookings->bind_param("i", $id);
            $deleteBookings->execute();
        }
        
        // Now delete the room
        $deleteRoom = $conn->prepare("DELETE FROM rooms WHERE id = ?");
        $deleteRoom->bind_param("i", $id);
        $deleteRoom->execute();
        
        echo "<script>
            window.location='manage-rooms.php';
            setTimeout(function() {
                window.showNotification('Room deleted successfully', 'success');
            }, 500);
        </script>";
    } else {
        echo "<script>
            window.location='manage-rooms.php';
            setTimeout(function() {
                window.showNotification('Room not found', 'error');
            }, 500);
        </script>";
    }
} else {
    header("Location: manage-rooms.php");
}
?>