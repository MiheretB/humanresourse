<?php
include 'db_connection.php'; // Ensure you have your DB connection here

if (isset($_GET['id'])) {
    $staff_id = $_GET['id'];

    // Fetch employee data from staff table
    $query_staff = "SELECT * FROM staff WHERE id = '$staff_id'";
    $result_staff = mysqli_query($conn, $query_staff);
    if (!$result_staff) {
        die("Error fetching staff data: " . mysqli_error($conn));
    }
    $staff = mysqli_fetch_assoc($result_staff);

    // Fetch file data
    $query_file = "SELECT * FROM file WHERE staff_id = '$staff_id'";
    $result_file = mysqli_query($conn, $query_file);
    if (!$result_file) {
        die("Error fetching file data: " . mysqli_error($conn));
    }
    $file = mysqli_fetch_assoc($result_file);

    // Fetch location data
    $query_location = "SELECT * FROM location WHERE staff_id = '$staff_id'";
    $result_location = mysqli_query($conn, $query_location);
    if (!$result_location) {
        die("Error fetching location data: " . mysqli_error($conn));
    }
    $location = mysqli_fetch_assoc($result_location);

    // Insert data into deletedemployee table
    $insert_deleted = "
        INSERT INTO deletedemployee (
            staff_id, firstname, middlename, lastname, sex, fullname, phone, username, password,
            cv_path, file_path, profile_image_path, room, shelf, row, fileno
        ) VALUES (
            '$staff_id',
            '" . mysqli_real_escape_string($conn, $staff['firstname']) . "',
            '" . mysqli_real_escape_string($conn, $staff['middlename']) . "',
            '" . mysqli_real_escape_string($conn, $staff['lastname']) . "',
            '" . mysqli_real_escape_string($conn, $staff['sex']) . "',
            '" . mysqli_real_escape_string($conn, $staff['fullname']) . "',
            '" . mysqli_real_escape_string($conn, $staff['phone']) . "',
            '" . mysqli_real_escape_string($conn, $staff['username']) . "',
            '" . mysqli_real_escape_string($conn, $staff['password']) . "',
            '" . mysqli_real_escape_string($conn, $file['cv_path'] ?? '') . "',
            '" . mysqli_real_escape_string($conn, $file['file_path'] ?? '') . "',
            '" . mysqli_real_escape_string($conn, $file['profile_image_path'] ?? '') . "',
            '" . mysqli_real_escape_string($conn, $location['room'] ?? '') . "',
            '" . mysqli_real_escape_string($conn, $location['shelf'] ?? '') . "',
            '" . mysqli_real_escape_string($conn, $location['row'] ?? '') . "',
            '" . mysqli_real_escape_string($conn, $location['fileno'] ?? '') . "'
        )";

    if (!mysqli_query($conn, $insert_deleted)) {
        die("Error inserting into deletedemployee table: " . mysqli_error($conn));
    }

    // Temporarily disable foreign key checks
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

    // Delete employee from file and location tables
    $delete_file = "DELETE FROM file WHERE staff_id = '$staff_id'";
    if (!mysqli_query($conn, $delete_file)) {
        die("Error deleting from file table: " . mysqli_error($conn));
    }

    $delete_location = "DELETE FROM location WHERE staff_id = '$staff_id'";
    if (!mysqli_query($conn, $delete_location)) {
        die("Error deleting from location table: " . mysqli_error($conn));
    }

    // Delete employee from staff table
    $delete_staff = "DELETE FROM staff WHERE id = '$staff_id'";
    if (!mysqli_query($conn, $delete_staff)) {
        die("Error deleting from staff table: " . mysqli_error($conn));
    }

    // Re-enable foreign key checks
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

    // Redirect to employee list page or a confirmation page
    header("Location: hrm_dashboard.php?deleted=true");
    exit();
}
