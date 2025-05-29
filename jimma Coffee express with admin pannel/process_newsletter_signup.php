<?php
// process_newsletter_signup.php

include 'components/connection.php'; // Your database connection
session_start(); // Start session to store messages

// Check if the form was submitted
if (isset($_POST['subscribe_newsletter'])) {
    $email = filter_var($_POST['newsletter_email'], FILTER_SANITIZE_EMAIL);

    // Basic validation
    if (empty($email)) {
        $_SESSION['warning_msg'] = 'Email address is required!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['warning_msg'] = 'Invalid email format!';
    } else {
        try {
            // Check if email already exists
            $check_email = $conn->prepare("SELECT id FROM `newsletter_subscriptions` WHERE email = ? LIMIT 1");
            $check_email->execute([$email]);

            if ($check_email->rowCount() > 0) {
                $_SESSION['info_msg'] = 'This email is already subscribed!'; // Use info_msg for existing
            } else {
                // Insert new email
                $insert_subscription = $conn->prepare("INSERT INTO `newsletter_subscriptions` (email) VALUES (?)");
                $insert_subscription->execute([$email]);
                $_SESSION['success_msg'] = 'Thank you for subscribing to our newsletter!';
            }
        } catch (PDOException $e) {
            // Log the error for debugging (check your server's PHP error logs)
            error_log("Newsletter subscription error: " . $e->getMessage());
            // Provide a generic error message to the user
            $_SESSION['error_msg'] = 'An error occurred. Please try again later.';
        }
    }
}

// Redirect back to the page the user came from (or a default page like index.php)
$referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header('Location: ' . $referer);
exit;
?>