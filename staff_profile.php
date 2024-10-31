<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

// Database connection
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

// Get logged in username
$user = $_SESSION['username'];

// Prepare and execute SQL statement to retrieve user details
$sql = "SELECT s.id, s.firstname, s.middlename, s.lastname, s.fullname, s.sex, s.username, 
        s.phone, l.row, l.line, l.room, l.shelf, l.fileno, f.file_path, f.cv_path, f.profile_image_path
        FROM staff s 
        LEFT JOIN location l ON s.id = l.staff_id 
        LEFT JOIN file f ON s.id = f.staff_id 
        WHERE s.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows > 0) {
    $staff = $result->fetch_assoc();
} else {
    die("User not found.");
}

// Close connection
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Profile</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Base styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-top: 0;
            color: #333;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: -15px;
        }

        .col-sm-4,
        .col-sm-8 {
            padding: 15px;
        }

        .col-sm-4 {
            flex: 0 0 33.3333%;
            max-width: 33.3333%;
        }

        .col-sm-8 {
            flex: 0 0 66.6666%;
            max-width: 66.6666%;
        }

        .profile-image {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 18px;
            border: 2px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            color: #fff;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
        }


        .btn-primary {
            background-color: #007bff;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-info {
            background-color: #17a2b8;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-success {
            background-color: #28a745;
        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);

        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;

        }

        .close:hover,
        .close:focus {

            text-decoration: none;
            cursor: pointer;

        }
    </style>
</head>

<body>
    <div class="container my-5">
        <h2><?php echo htmlspecialchars($staff['fullname']); ?></h2>
        <div class="row mb-3">
            <div class="col-sm-4">
                <?php if (!empty($staff['profile_image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($staff['profile_image_path']); ?>" alt="Profile Image" class="profile-image">
                <?php else: ?>
                    <img src="default_profile_image.jpg" alt="Default Profile Image" class="profile-image">
                <?php endif; ?>
            </div>
            <div class="col-sm-8">
                <table>
                    <tr>
                        <th>First Name</th>
                        <td><?php echo htmlspecialchars($staff['firstname']); ?></td>
                    </tr>
                    <tr>
                        <th>Middle Name</th>
                        <td><?php echo htmlspecialchars($staff['middlename']); ?></td>
                    </tr>
                    <tr>
                        <th>Last Name</th>
                        <td><?php echo htmlspecialchars($staff['lastname']); ?></td>
                    </tr>
                    <tr>
                        <th>Full Name</th>
                        <td><?php echo htmlspecialchars($staff['fullname']); ?></td>
                    </tr>
                    <tr>
                        <th>Sex</th>
                        <td><?php echo htmlspecialchars($staff['sex']); ?></td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td><?php echo htmlspecialchars($staff['phone']); ?></td>
                    </tr>
                    <tr>
                        <th>Location</th>
                        <td>
                            <button class="btn btn-secondary" onclick="openLocationModal()">View Location</button>
                        </td>
                    </tr>
                    <tr>
                        <th>Files</th>
                        <td>
                            <button class="btn btn-info" onclick="openFileModal()">View Files</button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <!-- File Modal -->
    <div id="fileModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeFileModal()">&times;</span>
            <h3>Files:<?php echo htmlspecialchars($staff['fullname']); ?></h3>
            <p><a href="<?php echo htmlspecialchars($staff['file_path']); ?>">View File</a></p>
            <p><a href="<?php echo htmlspecialchars($staff['cv_path']); ?>">View CV</a></p>
        </div>
    </div>

    <!-- Location Modal -->
    <div id="locationModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeLocationModal()">&times;</span>
            <h3>Located on: <?php echo htmlspecialchars($staff['fullname']); ?></h3>
            <p>File No: <?php echo htmlspecialchars($staff['fileno']); ?></p>
            <p>Room: <?php echo htmlspecialchars($staff['room']); ?></p>
            <p>Shelf: <?php echo htmlspecialchars($staff['shelf']); ?></p>
            <p>Row: <?php echo htmlspecialchars($staff['row']); ?></p>
            <p>Line: <?php echo htmlspecialchars($staff['line']); ?></p>
        </div>
    </div>

    <script>
        function openFileModal() {
            document.getElementById('fileModal').style.display = 'block';
        }

        function closeFileModal() {
            document.getElementById('fileModal').style.display = 'none';
        }

        function openLocationModal() {
            document.getElementById('locationModal').style.display = 'block';
        }

        function closeLocationModal() {
            document.getElementById('locationModal').style.display = 'none';
        }
    </script>
</body>

</html>