<?php
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

// Initialize variables
$firstname = "";
$middlename = "";
$lastname = "";
$sex = "";
$employeefileno = "";
$username = "";
$room = "";
$shelf = "";
$row = "";
$line = "";
$fullname = "";
$errormessage = "";
$successmessage = "";

// Handle form submission for editing an employee
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_employee'])) {
    $id = $_POST['id'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $sex = $_POST['sex'];
    $employeefileno = $_POST['employeefileno'];
    $room = $_POST['room'];
    $shelf = $_POST['shelf'];
    $row = $_POST['row'];
    $line = $_POST['line'];
    $fullname = $firstname . ' ' . $middlename . ' ' . $lastname;

    // Handle file upload
    $dest_path = ""; // Initialize file path
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitize file name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        // Directory where files will be saved
        $uploadFileDir = './uploads/';
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $fileUploadMessage = "File is successfully uploaded.";
        } else {
            $fileUploadMessage = "There was an error moving the uploaded file.";
        }
    } else {
        $fileUploadMessage = "No file uploaded or there was an upload error.";
    }

    // Validation
    if (empty($firstname) || empty($middlename) || empty($lastname) || empty($sex) || empty($employeefileno) || empty($room) || empty($shelf) || empty($row) || empty($line)) {
        $errormessage = "All fields are required.";
    } else {
        // SQL update statement
        $sql = "UPDATE staff SET firstname=?, middlename=?, lastname=?, sex=?, room=?, shelf=?, row=?, line=?, fullname=?, employeefileno=?, file_path=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssssssssssi", $firstname, $middlename, $lastname, $sex, $room, $shelf, $row, $line, $fullname, $employeefileno, $dest_path, $id);

            // Execute the statement
            if ($stmt->execute()) {
                $successmessage = "Employee updated successfully. " . $fileUploadMessage;
            } else {
                $errormessage = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errormessage = "Error preparing statement: " . $conn->error;
        }
    }
}

// Fetch employee data to populate the edit form
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM staff WHERE id=?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $employee = $result->fetch_assoc();
        $stmt->close();
    } else {
        $errormessage = "Error preparing statement: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container my-5">
        <h2>Edit Employee</h2>
        <?php
        if (!empty($errormessage)) {
            echo "
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>$errormessage</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
            ";
        }
        if (!empty($successmessage)) {
            echo "
            <div class='alert alert-success alert-dismissible fade show' role='alert'>
                <strong>$successmessage</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
            ";
        }
        ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($employee['id']); ?>">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">First Name</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="firstname" value="<?php echo htmlspecialchars($employee['firstname']); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Middle Name</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="middlename" value="<?php echo htmlspecialchars($employee['middlename']); ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Last Name</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="lastname" value="<?php echo htmlspecialchars($employee['lastname']); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Sex</label>
                <div class="col-sm-6">
                    <select class="form-select" name="sex" required>
                        <option value="Male" <?php echo $employee['sex'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $employee['sex'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Room</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="room" value="<?php echo htmlspecialchars($employee['room']); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Shelf</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="shelf" value="<?php echo htmlspecialchars($employee['shelf']); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Row</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="row" value="<?php echo htmlspecialchars($employee['row']); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Line</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="line" value="<?php echo htmlspecialchars($employee['line']); ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Employee File No</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="employeefileno" value="<?php echo htmlspecialchars($employee['employeefileno']); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Attach File</label>
                <div class="col-sm-6">
                    <input type="file" class="form-control" name="file">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-9">
                    <button type="submit" name="edit_employee" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>