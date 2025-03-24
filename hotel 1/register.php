<?php
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);
    
    if ($stmt->execute()) {
        echo "<script>alert('✅ Registration successful! Please login.'); window.location='login.php';</script>";
    } else {
        $error_message = "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Luxury Hotel</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-primary to-accent min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-xl shadow-2xl overflow-hidden fade-in">
        <div class="bg-primary py-6 px-8">
            <h2 class="text-2xl font-bold text-white text-center flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Create an Account
            </h2>
        </div>
        
        <div class="py-8 px-8">
            <?php if (isset($error_message)) { ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded scale-in" role="alert">
                    <p><?= $error_message ?></p>
                </div>
            <?php } ?>
            
            <form method="POST" class="space-y-6">
                <div class="group">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text" name="username" id="username" required
                               class="focus:ring-primary focus:border-primary block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 text-gray-900 transition-all duration-150 ease-in-out"
                               placeholder="Enter your username">
                    </div>
                </div>
                
                <div class="group">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input type="email" name="email" id="email" required
                               class="focus:ring-primary focus:border-primary block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 text-gray-900 transition-all duration-150 ease-in-out"
                               placeholder="Enter your email">
                    </div>
                </div>
                
                <div class="group">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" required
                               class="focus:ring-primary focus:border-primary block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 text-gray-900 transition-all duration-150 ease-in-out"
                               placeholder="Create a password">
                    </div>
                </div>
                
                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-150 ease-in-out">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-dark group-hover:text-white transition-colors duration-150 ease-in-out" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </span>
                        Register
                    </button>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="login.php" class="font-medium text-primary hover:text-primary-dark transition-colors duration-150 ease-in-out">
                        Sign in
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Input animations
            const inputs = document.querySelectorAll('input');
            
            inputs.forEach(input => {
                const group = input.closest('.group');
                
                input.addEventListener('focus', function() {
                    group.classList.add('scale-in');
                    setTimeout(() => {
                        group.classList.remove('scale-in');
                    }, 300);
                });
                
                // Validate email format
                if (input.type === 'email') {
                    input.addEventListener('blur', function() {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (this.value && !emailRegex.test(this.value)) {
                            this.classList.add('border-red-500');
                            
                            // Add error message if it doesn't exist
                            let errorMessage = group.querySelector('.error-message');
                            if (!errorMessage) {
                                errorMessage = document.createElement('p');
                                errorMessage.className = 'text-red-500 text-xs mt-1 error-message';
                                errorMessage.textContent = 'Please enter a valid email address';
                                group.appendChild(errorMessage);
                            }
                        } else {
                            this.classList.remove('border-red-500');
                            
                            // Remove error message if it exists
                            const errorMessage = group.querySelector('.error-message');
                            if (errorMessage) {
                                errorMessage.remove();
                            }
                        }
                    });
                }
                
                // Password strength indicator
                if (input.type === 'password') {
                    input.addEventListener('input', function() {
                        const password = this.value;
                        let strength = 0;
                        
                        // Remove existing strength indicator
                        const existingIndicator = group.querySelector('.password-strength');
                        if (existingIndicator) {
                            existingIndicator.remove();
                        }
                        
                        if (password.length === 0) return;
                        
                        // Create strength indicator
                        const strengthIndicator = document.createElement('div');
                        strengthIndicator.className = 'password-strength mt-2';
                        
                        // Check password strength
                        if (password.length >= 8) strength += 1;
                        if (/[A-Z]/.test(password)) strength += 1;
                        if (/[0-9]/.test(password)) strength += 1;
                        if (/[^A-Za-z0-9]/.test(password)) strength += 1;
                        
                        // Set indicator color and text
                        let strengthText, strengthColor;
                        switch (strength) {
                            case 0:
                            case 1:
                                strengthText = 'Weak';
                                strengthColor = 'bg-red-500';
                                break;
                            case 2:
                            case 3:
                                strengthText = 'Medium';
                                strengthColor = 'bg-yellow-500';
                                break;
                            case 4:
                                strengthText = 'Strong';
                                strengthColor = 'bg-green-500';
                                break;
                        }
                        
                        strengthIndicator.innerHTML = `
                            <div class="flex items-center justify-between">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="${strengthColor} h-2.5 rounded-full" style="width: ${(strength / 4) * 100}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 ml-2">${strengthText}</span>
                            </div>
                        `;
                        
                        group.appendChild(strengthIndicator);
                    });
                }
            });
            
            // Form submission animation
            const form = document.querySelector('form');
            form.addEventListener('submit', function() {
                if (this.checkValidity()) {
                    const button = this.querySelector('button[type="submit"]');
                    button.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Registering...';
                    button.disabled = true;
                }
            });
        });
    </script>
</body>
</html>