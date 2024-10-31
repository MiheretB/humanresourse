<?php
include 'db_connection.php'; // Include your DB connection

$update_success = false; // Flag to check if the update was successful

// Check if 'id' is provided in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch staff data
    $query_staff = "SELECT * FROM staff WHERE id = '$id'";
    $result_staff = mysqli_query($conn, $query_staff);
    if ($result_staff) {
        $staff = mysqli_fetch_assoc($result_staff);
    }
}

if (isset($_POST['update_staff'])) {
    // Handle staff data update
    $firstname = !empty($_POST['firstname']) ? $_POST['firstname'] : $staff['firstname'];
    $middlename = !empty($_POST['middlename']) ? $_POST['middlename'] : $staff['middlename'];
    $lastname = !empty($_POST['lastname']) ? $_POST['lastname'] : $staff['lastname'];
    $sex = !empty($_POST['sex']) ? $_POST['sex'] : $staff['sex'];
    $username = !empty($_POST['username']) ? $_POST['username'] : $staff['username'];
    $password = !empty($_POST['password']) ? $_POST['password'] : $staff['password']; // Consider hashing the password
    $phone = !empty($_POST['phone']) ? $_POST['phone'] : $staff['phone'];

    // Update the staff table
    $update_staff = "
        UPDATE staff 
        SET firstname='$firstname', middlename='$middlename', lastname='$lastname', sex='$sex', username='$username', password='$password', phone='$phone' 
        WHERE id='$id'";
    if (mysqli_query($conn, $update_staff)) {
        $update_success = true; // Set the success flag
    }

    // Refresh the page to update the displayed data
    header("Location: edit_employee.php?id=$id&success=1");
    exit();
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $update_success = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 60%;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin: 10px 0 5px;
            font-weight: 600;
            color: #495057;
        }

        input[type="text"],
        input[type="password"] {
            padding: 12px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            margin-bottom: 20px;
            outline: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #80bdff;
            box-shadow: 0 0 5px rgba(38, 143, 255, 0.2);
        }

        button {
            padding: 12px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #002999;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .success-message {
            padding: 15px;
            margin-bottom: 20px;
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            text-align: center;
        }

        .form-inline {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .form-inline form {
            flex: 1;
            margin-right: 10px;
        }

        .form-inline form:last-child {
            margin-right: 0;
        }

        .form-inline button {
            background-color: #005233;
        }

        .form-inline button:hover {
            background-color: #218838;
        }
    </style>
    <script>
        function showSuccessMessage() {
            if (document.getElementById('success-message')) {
                setTimeout(function() {
                    document.getElementById('success-message').style.display = 'none';
                }, 5000);
            }
        }
    </script>
</head>

<body onload="showSuccessMessage()">

    <div class="container">
        <h2>Edit Staff Information</h2>

        <?php if ($update_success): ?>
            <div id="success-message" class="success-message">Updated Successfully!</div>
        <?php endif; ?>

        <?php if (isset($staff)): ?>
            <form method="POST" action="">
                <label for="firstname">First Name:</label>
                <input type="text" name="firstname" value="<?= htmlspecialchars($staff['firstname']) ?>">

                <label for="middlename">Middle Name:</label>
                <input type="text" name="middlename" value="<?= htmlspecialchars($staff['middlename']) ?>">

                <label for="lastname">Last Name:</label>
                <input type="text" name="lastname" value="<?= htmlspecialchars($staff['lastname']) ?>">

                <label for="sex">Sex:</label>
                <input type="text" name="sex" value="<?= htmlspecialchars($staff['sex']) ?>">

                <label for="username">Username:</label>
                <input type="text" name="username" value="<?= htmlspecialchars($staff['username']) ?>">

                <label for="password">Password:</label>
                <input type="password" name="password" value="<?= htmlspecialchars($staff['password']) ?>">

                <label for="phone">Phone:</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($staff['phone']) ?>">

                <button type="submit" name="update_staff">Update Staff</button>
            </form>
            <div class="form-inline">
                <form method="GET" action="edit_file.php">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    <button type="submit">Edit File</button>
                </form>

                <form method="GET" action="edit_location.php">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    <button type="submit">Edit Location</button>
                </form>
            </div>
        <?php else: ?>
            <p>No staff data found for the provided ID.</p>
        <?php endif; ?>
    </div>

</body>

</html>