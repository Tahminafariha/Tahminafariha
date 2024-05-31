<?php
// enable php error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection details
$servername = 'localhost';
$username = 'root';
$password = '12345';
$dbname = 'student_management_system';

// Create a new MySQLi instance
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Function to display students
function displayStudents($search = '')
{
    global $conn;
    $sql = 'SELECT * FROM students';
    if (!empty($search)) {
        $sql .= " WHERE Name LIKE '%$search%' OR Section LIKE '%$search%' OR Gender LIKE '%$search%' OR Email LIKE '%$search%'";
    }
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table>';
        echo "<tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Section</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" .
                $row['ID'] .
                "</td>
                    <td>" .
                $row['Name'] .
                "</td>
                    <td>" .
                $row['Section'] .
                "</td>
                    <td>" .
                $row['Gender'] .
                "</td>
                    <td>" .
                $row['Email'] .
                "</td>
                    <td>
                        <button onclick=\"openEditModal('" .
                $row['ID'] .
                "', '" .
                $row['Name'] .
                "', '" .
                $row['Section'] .
                "', '" .
                $row['Gender'] .
                "', '" .
                $row['Email'] .
                "')\">Edit</button>
                        <button onclick=\"deleteStudent('" .
                $row['ID'] .
                "')\">Delete</button>
                    </td>
                </tr>";
        }
        echo '</table>';
    } else {
        echo '0 results';
    }
}

// Add a new student to the database
if (isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $section = $_POST['section'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];

    $sql = "INSERT INTO students (Name, Section, Gender, Email) VALUES ('$name', '$section', '$gender', '$email')";

    if ($conn->query($sql) === true) {
        echo 'Student added successfully';
    } else {
        echo 'Error adding student: ' . $conn->error;
    }
}

// Update an existing student in the database
if (isset($_POST['edit_student'])) {
    $id = $_POST['edit_id'];
    $name = $_POST['edit_name'];
    $section = $_POST['edit_section'];
    $gender = $_POST['edit_gender'];
    $email = $_POST['edit_email'];

    $sql = "UPDATE students SET Name='$name', Section='$section', Gender='$gender', Email='$email' WHERE ID='$id'";

    if ($conn->query($sql) === true) {
        echo 'Student updated successfully';
    } else {
        echo 'Error updating student: ' . $conn->error;
    }
}

// Delete a student from the database
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    $sql = "DELETE FROM students WHERE ID='$id'";

    if ($conn->query($sql) === true) {
        echo 'Student deleted successfully';
    } else {
        echo 'Error deleting student: ' . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Student Management System</title>
    <style>
        /* Styles for table, form, and modal */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .form-container {
            max-width: 300px;
            margin: 20px;
        }

        .form-container input[type=text],
        .form-container select {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .form-container input[type=submit] {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .form-container input[type=submit]:hover {
            background-color: #45a049;
        }

        /* Styles for modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
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

    <h2>Student Management System</h2>

    <!-- Add Student Form -->
    <div class="form-container">
        <h3>Add Student</h3>
        <form method="post" action="">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="section">Section:</label>
            <input type="text" id="section" name="section" required>
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>
            <input type="submit" value="Add Student" name="add_student">
        </form>
    </div>

    <!-- Search Student Form -->
    <div class="form-container">
        <h3>Search Student</h3>
        <form method="get" action="">
            <input type="text" id="search" name="search" placeholder="Search...">
            <input type="submit" value="Search">
        </form>
    </div>

    <div>
        <h3>Student List</h3>
        <?php
        // Check if search parameter is provided
        if (isset($_GET['search'])) {
            $search = $_GET['search'];
            displayStudents($search);
        } else {
            displayStudents();
        }
        ?>
    </div>

    <!-- Edit Student Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h3>Edit Student</h3>
            <form method="post" action="">
                <input type="hidden" id="edit_id" name="edit_id">
                <label for="edit_name">Name:</label>
                <input type="text" id="edit_name" name="edit_name" required>
                <label for="edit_section">Section:</label>
                <input type="text" id="edit_section" name="edit_section" required>
                <label for="edit_gender">Gender:</label>
                <select id="edit_gender" name="edit_gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <label for="edit_email">Email:</label>
                <input type="text" id="edit_email" name="edit_email" required>
                <input type="submit" value="Update Student" name="edit_student">
            </form>
        </div>
    </div>

    <script>
        // Open the Add Student modal
        function openAddModal() {
            document.getElementById("addModal").style.display = "block";
        }

        // Close the Add Student modal
        function closeAddModal() {
            document.getElementById("addModal").style.display = "none";
        }

        // Open the Edit Student modal and populate the form fields with existing data
        function openEditModal(id, name, section, gender, email) {
            document.getElementById("edit_id").value = id;
            document.getElementById("edit_name").value = name;
            document.getElementById("edit_section").value = section;
            document.getElementById("edit_gender").value = gender;
            document.getElementById("edit_email").value = email;
            document.getElementById("editModal").style.display = "block";
        }

        // Close the Edit Student modal
        function closeEditModal() {
            document.getElementById("editModal").style.display = "none";
        }


        // Delete a student
        function deleteStudent(id) {
            if (confirm("Are you sure you want to delete this student?")) {
                // Create a form dynamically
                var form = document.createElement("form");
                form.setAttribute("method", "post");
                form.setAttribute("action", "");

                // Create an input field for the student ID
                var inputId = document.createElement("input");
                inputId.setAttribute("type", "hidden");
                inputId.setAttribute("name", "delete_id");
                inputId.setAttribute("value", id);

                // Append the input field to the form
                form.appendChild(inputId);

                // Append the form to the document body
                document.body.appendChild(form);

                // Submit the form
                form.submit();
            }
        }
    </script>

</body>

</html>
