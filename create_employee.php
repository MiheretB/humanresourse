<?php
$firstname = "";
$middlename = "";
$lastname = "";
$sex = "";
$fileno = "";
$username = "";
$password = ""; // Store password in plain text for now
$room = "";
$shelf = "";
$row = "";
$line = "";
$phone = "";
$fullname = "";
$errormessage = "";
$successmessage = "";

// File paths
$file_path = "";
$profile_image_path = "";
$cv_path = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve POST data
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $sex = $_POST['sex'];
    $fileno = $_POST['fileno'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Plain text password
    $room = $_POST['room'];
    $shelf = $_POST['shelf'];
    $row = $_POST['row'];
    $line = $_POST['line'];
    $phone = $_POST['phone'];
    $fullname = $firstname . ' ' . $middlename . ' ' . $lastname; // Concatenate the full name

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = 'uploads/'; // Removed './'
        $file_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $file_path)) {
            $fileUploadMessage = "File is successfully uploaded.";
        } else {
            $fileUploadMessage = "There was an error moving the uploaded file.";
        }
    } else {
        $fileUploadMessage = "No file uploaded or there was an upload error.";
    }

    // Handle CV upload
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['cv']['tmp_name'];
        $fileName = $_FILES['cv']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = 'cv/'; // Removed './'
        $cv_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $cv_path)) {
            $cvUploadMessage = "CV is successfully uploaded.";
        } else {
            $cvUploadMessage = "There was an error moving the uploaded CV.";
        }
    } else {
        $cvUploadMessage = "No CV uploaded or there was an upload error.";
    }

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_image']['tmp_name'];
        $fileName = $_FILES['profile_image']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = 'profile_images/';
        $profile_image_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $profile_image_path)) {
            $imageUploadMessage = "Profile image is successfully uploaded.";
        } else {
            $imageUploadMessage = "There was an error moving the uploaded profile image.";
            echo 'Error: File not moved to ' . $profile_image_path;
        }
    } else {
        $imageUploadMessage = "No profile image uploaded or there was an upload error.";
        echo 'Error code: ' . $_FILES['profile_image']['error'];
    }

    // Validation
    if (empty($firstname) || empty($middlename) || empty($lastname) || empty($sex) || empty($fileno) || empty($username) || empty($password) || empty($room) || empty($shelf) || empty($row) || empty($line) || empty($phone)) {
        $errormessage = "All fields are required.";
    } else {
        // Database connection
        $conn = new mysqli("localhost", "root", "", "hrm");

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Start transaction
        $conn->begin_transaction();

        try {
            // Insert into staff table
            $sql = "INSERT INTO staff (firstname, middlename, lastname, sex, fullname, phone, username, password)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssss", $firstname, $middlename, $lastname, $sex, $fullname, $phone, $username, $password);

            if ($stmt->execute()) {
                $staff_id = $stmt->insert_id; // Get the ID of the inserted staff record

                // Insert into location table
                $sql_location = "INSERT INTO location (staff_id, `row`, fileno, line, room, shelf)
                VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_location = $conn->prepare($sql_location);
                $stmt_location->bind_param("isssss", $staff_id, $row, $fileno, $line, $room, $shelf);
                $stmt_location->execute();

                // Insert into file table
                $sql_file = "INSERT INTO file (staff_id, file_type, file_path, cv_path, profile_image_path)
                VALUES (?, 'document', ?, ?, ?)";
                $stmt_file = $conn->prepare($sql_file);
                $stmt_file->bind_param("isss", $staff_id, $file_path, $cv_path, $profile_image_path);
                $stmt_file->execute();

                // Commit transaction
                $conn->commit();
                $successmessage = "Employee added successfully.";

                // Redirect to HRM Dashboard and refresh page
                header("Location: hrm_dashboard.php");
                exit();
            } else {
                throw new Exception("Error inserting staff record: " . $stmt->error);
            }
        } catch (Exception $e) {
            $conn->rollback();
            $errormessage = $e->getMessage();
        }
    }
    // Close statements and connection
    if (isset($stmt)) $stmt->close();
    if (isset($stmt_location)) $stmt_location->close();
    if (isset($stmt_file)) $stmt_file->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Employee</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-group button {
            padding: 10px 15px;
            background-color: #007bff;
            border: none;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #0056b3;
        }

        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            position: relative;
        }

        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeeba;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert .btn-close {
            position: absolute;
            top: 5px;
            right: 10px;
            font-size: 1.25rem;
            line-height: 1;
            color: inherit;
            cursor: pointer;
            background: transparent;
            border: 0;
        }
    </style>
    <script>
        // Script to close alert messages
        document.addEventListener('DOMContentLoaded', function() {
            const closeButtons = document.querySelectorAll('.btn-close');
            closeButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    this.parentElement.style.display = 'none';
                });
            });
        });
    </script>
</head>

<body>
    <div class="container">
        <h2>Add New Employee</h2>

        <?php if (!empty($errormessage)) : ?>
            <div class="alert alert-warning">
                <?php echo htmlspecialchars($errormessage); ?>
                <button type="button" class="btn-close" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($successmessage)) : ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($successmessage); ?>
                <button type="button" class="btn-close" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="create_employee.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($firstname); ?>" required>
            </div>
            <div class="form-group">
                <label for="middlename">Middle Name</label>
                <input type="text" id="middlename" name="middlename" value="<?php echo htmlspecialchars($middlename); ?>">
            </div>
            <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($lastname); ?>" required>
            </div>
            <div class="form-group">
                <label for="sex">Sex</label>
                <select id="sex" name="sex" required>
                    <option value="Male" <?php echo ($sex == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($sex == 'Female') ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>
            <div class="form-group">
                <label for="fileno">File Number</label>
                <input type="text" id="fileno" name="fileno" value="<?php echo htmlspecialchars($fileno); ?>" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" required>
            </div>
            <div class="form-group">
                <label for="room">Room</label>
                <input type="text" id="room" name="room" value="<?php echo htmlspecialchars($room); ?>" required>
            </div>
            <div class="form-group">
                <label for="shelf">Shelf</label>
                <input type="text" id="shelf" name="shelf" value="<?php echo htmlspecialchars($shelf); ?>" required>
            </div>
            <div class="form-group">
                <label for="row">Row</label>
                <input type="text" id="row" name="row" value="<?php echo htmlspecialchars($row); ?>" required>
            </div>
            <div class="form-group">
                <label for="line">Line</label>
                <input type="text" id="line" name="line" value="<?php echo htmlspecialchars($line); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
            </div>
            <div class="form-group">
                <label for="file">Upload Document</label>
                <input type="file" id="file" name="file">
                <?php if (!empty($file_path)) : ?>
                    <small>Uploaded: <a href="<?php echo htmlspecialchars($file_path); ?>" target="_blank"><?php echo htmlspecialchars(basename($file_path)); ?></a></small>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="cv">Upload CV</label>
                <input type="file" id="cv" name="cv">
                <?php if (!empty($cv_path)) : ?>
                    <small>Uploaded: <a href="<?php echo htmlspecialchars($cv_path); ?>" target="_blank"><?php echo htmlspecialchars(basename($cv_path)); ?></a></small>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="profile_image">Profile Image</label>
                <input type="file" id="profile_image" name="profile_image" required>
            </div>
            <div class="form-group">
                <button type="submit">Add Employee</button>
            </div>
        </form>
    </div>
</body>

</html>