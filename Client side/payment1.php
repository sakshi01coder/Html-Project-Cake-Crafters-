<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $card_num = $_POST["card_num"];
    $date_num = $_POST["date_num"];
    $cvv_num = $_POST["cvv_num"];
    $name_num = $_POST["name_num"];
    
    // Perform server-side validation (similar to client-side validation)
    if (empty($card_num) || empty($date_num) || empty($cvv_num) || empty($name_num)) {
        echo "All fields are required.";
    } else {
        // Connect to your MySQL database (replace with your database credentials)
        $conn = mysqli_connect("localhost", "root", "", "payment_db");
        
        // Check the database connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        
        // Hash the CVV for security (you should use a stronger hashing method)
        $hashed_cvv = password_hash($cvv_num, PASSWORD_BCRYPT);
        
        // Insert user data into the database
        $sql = "INSERT INTO payments (card_num, date_num, cvv_num, name_num) VALUES ('$card_num', '$date_num', '$hashed_cvv', '$name_num')";
        if (mysqli_query($conn, $sql)) {
            // Close the database connection
            mysqli_close($conn);

            // Redirect to index.html
            header('Location: index.html');
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}
?>
