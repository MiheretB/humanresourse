<?php
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "hrm";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search term from query parameters
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare SQL query with search term
$sql = "SELECT s.id, s.firstname, s.middlename, s.lastname, s.sex, s.fullname, s.username, 
        s.password, s.phone, l.row, l.line, l.room, l.shelf, l.fileno, f.file_path, f.cv_path, f.profile_image_path
        FROM staff s 
        LEFT JOIN location l ON s.id = l.staff_id 
        LEFT JOIN file f ON s.id = f.staff_id";

if ($searchTerm) {
    $searchTerm = $conn->real_escape_string($searchTerm);
    $sql .= " WHERE CONCAT(s.id, s.firstname, s.middlename, s.lastname, s.sex, l.room, l.shelf, l.row, l.line, l.fileno, s.fullname, s.username) LIKE '%$searchTerm%'";
}

$result = $conn->query($sql);

// Check for query errors
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Set headers to indicate the content type and attachment
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=employee_report.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Output the column headings
fputcsv($output, array('ID', 'First Name', 'Middle Name', 'Last Name', 'Sex', 'Full Name', 'Phone', 'Row', 'Line', 'Room', 'Shelf', 'File No'));

// Fetch and output each row of the table as a CSV line
while ($row = $result->fetch_assoc()) {
    fputcsv($output, array(
        $row['id'],
        $row['firstname'],
        $row['middlename'],
        $row['lastname'],
        $row['sex'],
        $row['fullname'],
        $row['phone'],
        $row['row'],
        $row['line'],
        $row['room'],
        $row['shelf'],
        $row['fileno']
    ));
}

// Close the output stream and database connection
fclose($output);
$conn->close();
exit();
