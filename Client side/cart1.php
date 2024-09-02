<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Database connection details
$host = "localhost";
$dbname = "shop_db";
$username_db = "root";
$password_db = "";

// Fetch user's cart items from the database
$user_id = $_SESSION['user']['id'];

try {
    $db = new PDO(
        "mysql:host=$host;dbname=$dbname",
        $username_db,
        $password_db
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->prepare("SELECT * FROM food WHERE user_id = :user_id");
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Close the database connection
    $db = null;
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Function to calculate the total value of items in the cart
function calculateTotal($items) {
    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'];
    }
    return $total;
}

// Function to display the quantity with appropriate unit
function displayQuantity($item) {
    // Convert the item name to lowercase for case-insensitive comparison
    $itemName = strtolower($item['item']);
    
    // List of items that should be displayed in "pcs"
    $pcsItems = ['donut', 'tea time cookies', 'chocolates'];
    
    // Check if the item name matches any of the specified types
    foreach ($pcsItems as $pcsItem) {
        if (strpos($itemName, $pcsItem) !== false) {
            return $item['quantity'] . ' pcs';
        }
    }
    
    // Default to kg if no match found
    return $item['quantity'] . ' kg';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" type="text/css" href="cart.css">
</head>
<body>
    <header>
        <h1>Shopping Cart</h1>
    </header>

    <div class="main-content">
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop through cart items and display -->
                <?php if (!empty($cart_items)): ?>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['item']); ?></td>
                            <td><?php echo displayQuantity($item); ?></td>
                            <td>Rs.<?php echo htmlspecialchars($item['price']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">Your cart is empty.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pay button with total value -->
        <div class="pay-section">
            <p>Total: Rs.<?php echo calculateTotal($cart_items); ?></p>
            <form action="payment.html" method="post">
                <input type="submit" value="Pay">
            </form>
        </div>
    </div>
</body>
</html>
