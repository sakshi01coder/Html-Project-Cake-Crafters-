<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $message = $_POST["message"];
    
    // Perform server-side validation (similar to client-side validation)
    if (empty($name) || empty($email) || empty($phone) || empty($message)) {
        echo "All fields are required.";
    } else {
        // Connect to your MySQL database (replace with your database credentials)
        $conn = mysqli_connect("localhost", "root", "", "contact_db");
         
        // Check the database connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        
        
        // Insert user data into the database
        $sql = "INSERT INTO contacts (name,email,phone,message) VALUES ('$name', '$email', '$phone', '$message')";
        if (mysqli_query($conn, $sql)) {
            // Payment successful; display alert
            echo '<script>alert("Contact details registration successful");</script>';
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        
        // Close the database connection
        mysqli_close($conn);
    }
}
?>