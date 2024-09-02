<?php
session_start();
$response = ['success' => false];

if (isset($_POST['product_id']) && isset($_POST['weight'])) {
    $productId = $_POST['product_id'];
    $weight = $_POST['weight'];

    if (isset($_SESSION['user'])) {
        // User is logged in, add to the database
        $user_id = $_SESSION['user']['id'];
        // Add logic to insert the product to the database with $user_id, $productId, and $weight

        // Assuming the insert is successful
        $response['success'] = true;
    } else {
        // User is not logged in, add to session cart
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $_SESSION['cart'][] = [
            'product_id' => $productId,
            'weight' => $weight
        ];

        $response['success'] = true;
    }
}

echo json_encode($response);
?>
