<?php 
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $username = $_POST["username"]; 
    $password = $_POST["password"]; 

    // Database connection details
    $host = "localhost"; 
    $dbname = "registration_db"; 
    $username_db = "root"; 
    $password_db = ""; 

    try { 
        $db = new PDO( 
            "mysql:host=$host;dbname=$dbname", 
            $username_db, 
            $password_db
        ); 
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

        // Check if the user exists in the database 
        $stmt = $db->prepare("SELECT * FROM users WHERE name = :username");
        $stmt->bindParam(":username", $username); 
        $stmt->execute(); 
        $user = $stmt->fetch(PDO::FETCH_ASSOC); 

        if ($user) { 
            // Verify the password 
            if (password_verify($password, $user["password"])) { 
                $_SESSION["user"] = $user;
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["cart"] = []; // Clear cart on new login

                // Fetch the user's cart items from the database
                $conn = new PDO(
                    "mysql:host=$host;dbname=shop_db", 
                    $username_db, 
                    $password_db
                );
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt_cart = $conn->prepare("SELECT * FROM food WHERE user_id = :user_id");
                $stmt_cart->bindParam(":user_id", $user["id"]);
                $stmt_cart->execute();
                $cart_items = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);

                foreach ($cart_items as $item) {
                    $_SESSION["cart"][] = $item;
                }

                echo '<script type="text/javascript"> 
                    alert("Welcome to GFG shopping website"); 
                    window.location.href = "shop.html";  
                </script>'; 
            } else { 
                echo "<h2>Login Failed</h2>"; 
                echo "Invalid email or password."; 
            } 
        } else { 
            echo "<h2>Login Failed</h2>"; 
            echo "User doesn't exist"; 
        } 
    } catch (PDOException $e) { 
        echo "Connection failed: " . $e->getMessage(); 
    } 
} 
?>
