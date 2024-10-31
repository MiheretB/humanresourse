<?php
include 'db_connection.php'; // Ensure you have your DB connection here

if (isset($_GET['id'])) {
    $staff_id = $_GET['id'];

    // Fetch file data
    $query_file = "SELECT * FROM file WHERE staff_id = '$staff_id'";
    $result_file = mysqli_query($conn, $query_file);
    $file = mysqli_fetch_assoc($result_file);
}

if (isset($_POST['update_file'])) {
    $cvpath = !empty($_POST['cvpath']) ? $_POST['cvpath'] : $file['cvpath'];
    $filepath = !empty($_POST['filepath']) ? $_POST['filepath'] : $file['filepath'];
    $profileimage = !empty($_POST['profileimage']) ? $_POST['profileimage'] : $file['profileimage'];

    $update_file = "
        UPDATE file 
        SET cvpath='$cvpath', filepath='$filepath', profileimage='$profileimage' 
        WHERE staff_id='$staff_id'";
    mysqli_query($conn, $update_file);

    header("Location: edit_employee.php?id=$staff_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit File Information</title>
</head>

<body>
    <form method="POST">
        <h2>Edit File Information</h2>
        <label for="cvpath">CV Path:</label>
        <input type="text" name="cvpath" value="<?= htmlspecialchars($file['cvpath']) ?>"><br>

        <label for="filepath">File Path:</label>
        <input type="text" name="filepath" value="<?= htmlspecialchars($file['filepath']) ?>"><br>

        <label for="profileimage">Profile Image:</label>
        <input type="text" name="profileimage" value="<?= htmlspecialchars($file['profileimage']) ?>"><br>

        <button type="submit" name="update_file">Update File</button>
    </form>
</body>

</html>