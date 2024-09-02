<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$host = "localhost";
$dbname = "shop_db";
$username_db = "root";
$password_db = "";

// Function to calculate the total value of items in the cart
function calculateTotal($items) {
    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'];
    }
    return $total;
}

// Function to display the quantity with the appropriate unit
function displayQuantity($item) {
    $itemName = strtolower($item['item']);
    $pcsItems = ['donut', 'tea time cookies', 'chocolates'];
    
    foreach ($pcsItems as $pcsItem) {
        if (strpos($itemName, $pcsItem) !== false) {
            return $item['quantity'] . ' pcs';
        }
    }
    
    return $item['quantity'] . ' kg';
}

try {
    $db = new PDO(
        "mysql:host=$host;dbname=$dbname",
        $username_db,
        $password_db
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Initialize the cart items array
    $cart_items = [];

    // Check if the user is logged in
    if (isset($_SESSION['user'])) {
        $user_id = $_SESSION['user']['id'];

        // Handle deletion of an item from the cart
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
            $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : null;
            
            if ($item_id) {
                $stmt = $db->prepare("DELETE FROM food WHERE id = :item_id AND user_id = :user_id");
                $stmt->bindParam(":item_id", $item_id);
                $stmt->bindParam(":user_id", $user_id);
                $stmt->execute();
                
                // Redirect to the same page to reflect changes
                header('Location: cart.php');
                exit();
            }
        }

        // Fetch user's cart items from the database
        $stmt = $db->prepare("SELECT * FROM food WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // User is not logged in, use session cart
        if (isset($_SESSION['cart'])) {
            $cart_items = $_SESSION['cart'];
        }
    }

    // Close the database connection
    $db = null;
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
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
                    <th>Action</th> <!-- Add Action column -->
                </tr>
            </thead>
            <tbody>
                <!-- Loop through cart items and display -->
                <?php if (!empty($cart_items)): ?>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['item']); ?></td>
                            <td><?php echo displayQuantity($item); ?></td>
                            <td>Rs. <?php echo htmlspecialchars($item['price']); ?></td>
                            <td>
                                <!-- Form to delete an item -->
                                <form method="post" action="cart.php">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                    <input type="submit" value="Delete">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Your cart is empty.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pay button with total value -->
        <div class="pay-section">
            <p>Total: Rs. <?php echo calculateTotal($cart_items); ?></p>
            <form action="payment.html" method="post">
                <input type="submit" value="Pay">
            </form>
        </div>
    </div>
</body>
</html>

<?php
// Handle the checkout process
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pay'])) {
    if (!isset($_SESSION['user'])) {
        // Redirect to login if user is not logged in
        header('Location: login.php');
        exit();
    } else {
        // Proceed to payment
        // Existing payment logic
    }
}
?>
