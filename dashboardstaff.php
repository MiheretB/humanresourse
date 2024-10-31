<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php?msg=Please%20login%20first");
    exit();
}

$username = $_SESSION['username'];

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "hrm";

// Create connection
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to retrieve the staff profile based on the username
$sql = "SELECT * FROM staff WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $fullname = htmlspecialchars($row['fullname']);
    $employeefileno = htmlspecialchars($row['employeefileno']);
    $sex = htmlspecialchars($row['sex']);
    $shelf = htmlspecialchars($row['shelf']);
    $row_data = htmlspecialchars($row['row']);
} else {
    die("No data found");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .header {
            background-color: #003366;
            /* Dark Blue */
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .profile {
            background-color: #004d00;
            /* Dark Green */
            color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile h2 {
            margin-top: 0;
        }

        .profile p {
            margin: 10px 0;
            font-size: 18px;
        }

        .footer {
            background-color: #003366;
            /* Dark Blue */
            color: #fff;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Staff Dashboard</h1>
        </div>

        <div class="profile">
            <h2>Welcome, <?php echo $fullname; ?></h2>
            <p>File No: <?php echo $employeefileno; ?></p>
            <p>Sex: <?php echo $sex; ?></p>
            <p>Shelf: <?php echo $shelf; ?></p>
            <p>Row: <?php echo $row_data; ?></p>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2024 HRMS. All rights reserved.</p>
    </div>
</body>

</html>