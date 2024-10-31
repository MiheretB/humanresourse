<?php
include 'db_connection.php'; // Ensure you have your DB connection here

if (isset($_GET['id'])) {
    $staff_id = $_GET['id'];

    // Fetch location data
    $query_location = "SELECT * FROM location WHERE staff_id = '$staff_id'";
    $result_location = mysqli_query($conn, $query_location);
    $location = mysqli_fetch_assoc($result_location);
}

if (isset($_POST['update_location'])) {
    $fileno = !empty($_POST['fileno']) ? $_POST['fileno'] : $location['fileno'];
    $shelf = !empty($_POST['shelf']) ? $_POST['shelf'] : $location['shelf'];
    $row = !empty($_POST['row']) ? $_POST['row'] : $location['row'];
    $line = !empty($_POST['line']) ? $_POST['line'] : $location['line'];
    $room = !empty($_POST['room']) ? $_POST['room'] : $location['room'];

    $update_location = "
        UPDATE location 
        SET fileno='$fileno', shelf='$shelf', row='$row', line='$line', room='$room' 
        WHERE staff_id='$staff_id'";
    mysqli_query($conn, $update_location);

    header("Location: edit_employee.php?id=$staff_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Location Information</title>
</head>

<body>
    <form method="POST">
        <h2>Edit Location Information</h2>
        <label for="fileno">File No:</label>
        <input type="text" name="fileno" value="<?= htmlspecialchars($location['fileno']) ?>"><br>

        <label for="shelf">Shelf:</label>
        <input type="text" name="shelf" value="<?= htmlspecialchars($location['shelf']) ?>"><br>

        <label for="row">Row:</label>
        <input type="text" name="row" value="<?= htmlspecialchars($location['row']) ?>"><br>

        <label for="line">Line:</label>
        <input type="text" name="line" value="<?= htmlspecialchars($location['line']) ?>"><br>

        <label for="room">Room:</label>
        <input type="text" name="room" value="<?= htmlspecialchars($location['room']) ?>"><br>

        <button type="submit" name="update_location">Update Location</button>
    </form>
</body>

</html>