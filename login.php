<?php
session_start(); // Ensure this is at the top with no space above it

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hrm";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // First, check in the 'user' table (plain text password)
    $query = "SELECT * FROM user WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if ($user['password'] === $password) {  // Plain text comparison
            $_SESSION['user'] = $user;
            echo "User found in the 'user' table, redirecting...";

            if ($user['role'] == 'admin') {
                header('Location: admin_dashboard.php');
            } elseif ($user['role'] == 'hrmstaff') {
                header('Location: hrm_dashboard.php');
            }
            exit;
        }
    } else {
        // If not found in 'user', check in the 'staff' table (hashed password)
        $query = "SELECT * FROM staff WHERE username='$username'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {
            $staff = mysqli_fetch_assoc($result);

            if (password_verify($password, $staff['password'])) {  // Hashed password verification
                $_SESSION['staff'] = $staff;
                echo "User found in the 'staff' table, redirecting...";

                // Redirect to staff profile
                header('Location: staff_profile.php');
                exit;
            }
        }
    }

    $error = "Invalid username or password.";
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HRMS</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Login</h1>
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <?php if (isset($error)) { ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>
    </div>
</body>

</html>
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Database connection
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "hrm";

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // First check in the user table (Admin and HRM Staff)
    $sql = "SELECT * FROM user WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Login successful for Admin or HRM Staff
        $_SESSION['username'] = $username;
        $user = $result->fetch_assoc();
        $_SESSION['role'] = $user['role']; // Assuming 'role' is a column in the user table
        if ($_SESSION['role'] == 'Admin') {
            header("Location: admin_dashboard.php");
        } elseif ($_SESSION['role'] == 'HRM Staff') {
            header("Location: hrm_dashboard.php");
        }
        exit();
    }

    // If not found in the user table, check in the staff table (Company Staff)
    $sql = "SELECT * FROM staff WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Login successful for Company Staff
        $_SESSION['username'] = $username;
        header("Location: staff_profile.php");
        exit();
    } else {
        // If neither table has the user, show an error message
        $errormessage = "Invalid username or password.";
    }

    $stmt->close();
    $conn->close();
}
?>