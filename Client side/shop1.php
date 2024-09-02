<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Initialize error message variable
$error_message = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $cake = $_POST["cake"] ?? '';
    $weight = $_POST["weight"] ?? '';
    $price = $_POST["price"] ?? '';
    $user_id = $_SESSION['user']['id']; // Get the user ID from the session

    // Perform server-side validation
    if (empty($cake) || empty($weight) || empty($price)) {
        $error_message = "All fields are required.";
    } else {
        // Connect to your MySQL database
        $conn = mysqli_connect("localhost", "root", "", "shop_db");

        // Check the database connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Insert user data into the database
        $sql = "INSERT INTO food (cake, weight, price, user_id) VALUES ('$cake', '$weight', '$price', '$user_id')";
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
            $error_message = "Error: " . mysqli_error($conn);
            // Close the database connection
            mysqli_close($conn);
        }
    }
}

// Display the error message if exists
if ($error_message) {
    echo "<h2>Error</h2>";
    echo "<p>$error_message</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <link rel="stylesheet" type="text/css" href="shop.css">
</head>
<body>
    <header>
        <h1>Shop</h1>
    </header>

    <div class="main-content">
        <!-- Form for adding items -->
        <form action="shop.php" method="post">
            <label for="cake">Cake Name:</label>
            <input type="text" id="cake" name="cake" required>
            <label for="weight">Weight (kg):</label>
            <input type="number" id="weight" name="weight" step="0.01" required>
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required>
            <input type="submit" value="Add to Cart">
        </form>

        <!-- Display cart items -->
        <h2>Your Cart</h2>
        <table>
            <thead>
                <tr>
                    <th>Cake</th>
                    <th>Weight (kg)</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($_SESSION['cart'])): ?>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['cake']); ?></td>
                            <td><?php echo htmlspecialchars($item['weight']); ?></td>
                            <td><?php echo htmlspecialchars($item['price']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">Your cart is empty.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
