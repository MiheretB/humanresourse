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

// Updated SQL query to join tables
$sql = "SELECT s.id, s.firstname, s.middlename, s.lastname, s.sex,  s.fullname, s.username, 
        s.password, s.phone, l.row, l.line, l.room, l.shelf,l.fileno, f.file_path, f.cv_path, f.profile_image_path
        FROM staff s 
        LEFT JOIN location l ON s.id = l.staff_id 
        LEFT JOIN file f ON s.id = f.staff_id";

$result = $conn->query($sql);

// Check for query errors
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Additional styles for the modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>List of Employee</h2>
        <input type="text" id="searchInput" class="search-input" placeholder="Search for a client..."><br>
        <form id="reportForm" action="generate_report.php" method="get">
            <input type="hidden" id="searchTermInput" name="search" value="">
            <button type="submit" class="btn btn-success">Generate Report</button>
        </form>
        <br>
        <table class="table" id="employeeTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Sex</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Phone Number</th>
                    <th>Files</th>
                    <th>Location</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch and display data
                while ($row = $result->fetch_assoc()) {
                    echo "
                    <tr>
                        <td>{$row['id']}</td>
                        <td>{$row['firstname']}</td>
                        <td>{$row['middlename']}</td>
                        <td>{$row['lastname']}</td>
                        <td>{$row['sex']}</td>
                        <td>{$row['fullname']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['phone']}</td>
                        <td><button class='btn btn-info' onclick='openModal({$row['id']})'>View Files</button></td>
                        <td><button class='btn btn-secondary' onclick='openLocationModal({$row['id']})'>View Location</button></td>
                        <td>
                            <div class='action-btns'>
                                <a class='btn btn-primary' href='edit_employee.php?id={$row['id']}'>Edit</a>
                                <a class='btn btn-danger' href='delete_employee.php?id={$row['id']}'>Delete</a>
                            </div>
                        </td>
                    </tr>
                    <div id='modal{$row['id']}' class='modal'>
                        <div class='modal-content'>
                            <span class='close' onclick='closeModal({$row['id']})'>&times;</span>
                            <h3>Files for {$row['fullname']}</h3>
                            <p><a href='{$row['file_path']}'>View File</a></p>
                            <p><a href='{$row['cv_path']}'>View CV</a></p>
                        </div>
                    </div>
                    <div id='locationModal{$row['id']}' class='modal'>
                        <div class='modal-content'>
                            <span class='close' onclick='closeLocationModal({$row['id']})'>&times;</span>
                            <h3>Location Details for {$row['fullname']}</h3>
                            <p>fileno: {$row['fileno']}</p>
                            <p>Room: {$row['room']}</p>
                            <p>Shelf: {$row['shelf']}</p>
                            <p>Row: {$row['row']}</p>
                            <p>Line: {$row['line']}</p>

                        </div>
                    </div>";
                }

                // Close connection
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function openModal(id) {
            document.getElementById('modal' + id).style.display = 'block';
        }

        function closeModal(id) {
            document.getElementById('modal' + id).style.display = 'none';
        }

        function openLocationModal(id) {
            document.getElementById('locationModal' + id).style.display = 'block';
        }

        function closeLocationModal(id) {
            document.getElementById('locationModal' + id).style.display = 'none';
        }

        document.getElementById('searchInput').addEventListener('keyup', function() {
            var searchTerm = this.value.toLowerCase();
            var tableRows = document.querySelectorAll('#employeeTable tbody tr');

            tableRows.forEach(function(row) {
                var rowText = row.textContent.toLowerCase();
                if (rowText.indexOf(searchTerm) > -1) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Update the hidden input field in the report form
            document.getElementById('searchTermInput').value = searchTerm;
        });
    </script>
</body>

</html>