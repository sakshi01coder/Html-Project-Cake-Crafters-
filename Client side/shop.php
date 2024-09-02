<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $item = isset($_POST["item"]) ? $_POST["item"] : null;
    $quantity = isset($_POST["quantity"]) ? $_POST["quantity"] : null;
    $price = isset($_POST["price"]) ? $_POST["price"] : null;
    $user_id = $_SESSION['user']['id']; // Get the user ID from the session

    // Perform server-side validation (similar to client-side validation)
    if (empty($item) || empty($quantity) || empty($price)) {
        echo "All fields are required.";
    } else {
        // Connect to your MySQL database (replace with your database credentials)
        $conn = mysqli_connect("localhost", "root", "", "shop_db");

        // Check the database connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Escape inputs to prevent SQL injection
        $item = mysqli_real_escape_string($conn, $item);
        $quantity = mysqli_real_escape_string($conn, $quantity);
        $price = mysqli_real_escape_string($conn, $price);

        // Insert user data into the database
        $sql = "INSERT INTO food (item, quantity, price, user_id) VALUES ('$item', '$quantity', '$price', '$user_id')";
        if (mysqli_query($conn, $sql)) {
            // Retrieve the inserted data
            $inserted_id = mysqli_insert_id($conn);
            $sql_select = "SELECT * FROM food WHERE id = $inserted_id";
            $result = mysqli_query($conn, $sql_select);
            $row = mysqli_fetch_assoc($result);

            // Store the retrieved data in the session
            $_SESSION['cart'][] = $row;

            // Close the database connection
            mysqli_close($conn);

            // Redirect to the shop page after successfully adding to the cart
            header('Location: shop.php');
            exit();
        } else {
            // Capture the error message
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
            // Close the database connection
            mysqli_close($conn);
            // Display the error message
            echo $error_message;
        }
    }
} else {
    // Redirect back to shop page if the form is not submitted
    header('Location: shop.html');
    exit();
}
?>
