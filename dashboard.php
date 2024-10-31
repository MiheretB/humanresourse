<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: Login.php?msg=Please%20login%20first");
    exit();
}

// Check user roles and grant access accordingly
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>

<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

    <?php if ($role == 'admin' || $role == 'hrmstaff'): ?>
        <a href="create_employee.php">Create Employee</a>
    <?php endif; ?>

    <!-- Other content specific to the user roles -->
    <?php if ($role == 'companystaff'): ?>
        <p>This is the company staff dashboard view.</p>
    <?php endif; ?>

    <!-- Add a logout link -->
    <a href="logout.php">Logout</a>
</body>

</html>