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
    // Handle file upload
    $cvpath = !empty($_FILES['cvpath']['name']) ? 'cv/' . basename($_FILES['cvpath']['name']) : $file['cv_path'];
    $filepath = !empty($_FILES['filepath']['name']) ? 'uploads/' . basename($_FILES['filepath']['name']) : $file['file_path'];
    $profileimage = !empty($_FILES['profileimage']['name']) ? 'profile_images/' . basename($_FILES['profileimage']['name']) : $file['profile_image_path'];

    // Move uploaded files to the uploads directory
    if (!empty($_FILES['cvpath']['name'])) {
        move_uploaded_file($_FILES['cvpath']['tmp_name'], $cvpath);
    }
    if (!empty($_FILES['filepath']['name'])) {
        move_uploaded_file($_FILES['filepath']['tmp_name'], $filepath);
    }
    if (!empty($_FILES['profileimage']['name'])) {
        move_uploaded_file($_FILES['profileimage']['tmp_name'], $profileimage);
    }

    // Update the file table
    $update_file = "
        UPDATE file 
        SET cv_path='$cvpath', file_path='$filepath', profile_image_path='$profileimage' 
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
    <link rel="stylesheet" href="styleedit.css">
</head>

<body>
    <div class="container">
        <form method="POST" enctype="multipart/form-data">
            <h2>Edit File Information</h2>
            <label for="cvpath">CV Path:</label>
            <input type="file" name="cvpath">
            <small>Current file: <?= htmlspecialchars($file['cv_path']) ?></small>

            <label for="filepath">File Path:</label>
            <input type="file" name="filepath">
            <small>Current file: <?= htmlspecialchars($file['file_path']) ?></small>

            <label for="profileimage">Profile Image:</label>
            <input type="file" name="profileimage">
            <small>Current file: <?= htmlspecialchars($file['profile_image_path']) ?></small>

            <button type="submit" name="update_file">Update File</button>
        </form>
    </div>
</body>

</html>