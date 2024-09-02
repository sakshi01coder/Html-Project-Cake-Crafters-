<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Perform server-side validation
    if (empty($name) || empty($email) || empty($password)) {
        echo "All fields are required.";
    } else {
        // Connect to MySQL database
        $conn = mysqli_connect("localhost", "root", "", "registration_db");

        // Check the database connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert user data into the database
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')";
        if (mysqli_query($conn, $sql)) {
            // Account created successfully; perform redirection
            header("Location: login.html");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        // Close the database connection
        mysqli_close($conn);
    }
}
?>
