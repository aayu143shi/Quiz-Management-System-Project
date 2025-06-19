<?php
include "connect.php";

session_start();
error_reporting(0);
require 'vendor/autoload.php';



use PhpOffice\PhpSpreadsheet\IOFactory;

use PhpOffice\PhpSpreadsheet\Spreadsheet;


if (isset($_POST['submit_xlbtn']) && isset($_FILES['xlsx_file'])) {
    $file = $_FILES['xlsx_file'];

    $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);

    if ($fileType != 'xlsx') {
        die("Please upload a valid XLSX file.");
    }

    $tempFilePath = 'uploads/' . $file['name'];

    if (!move_uploaded_file($file['tmp_name'], $tempFilePath)) {
        die("Failed to upload file.");
    }

    try {
        $spreadsheet = IOFactory::load($tempFilePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rowCount = 0;
        foreach ($sheet->getRowIterator() as $row) {
            $rowCount++;
            if ($rowCount == 1) continue;
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            // echo "<pre>";
            $data = [];
            foreach ($cellIterator as $cell) {
                if (!empty($cell->getValue())) {
                    $data[] = $cell->getValue();
                }
                // print_r($cell);
            }

            // echo "<pre>";
            // print_r($data);
            // count($data);
            // die();

            list($emp_name, $emp_code, $emp_head, $emp_status) = $data;

            if ($emp_status !== 1) {
                $emp_status = 0;
            }

            // echo "<pre>";
            // print_r($emp_status);
            // die();

            if (!empty($emp_name)) {

                $employees_sql = "SELECT * FROM employees WHERE name = ? AND emp_code= ?";

                $stmtCheck = $con->prepare($employees_sql);

                if (!$stmtCheck) {
                    die("Error preparing check statement: " . $con->error);
                }
                $stmtCheck->bind_param("ss", $emp_name, $emp_code);
                $stmtCheck->execute();
                $result = $stmtCheck->get_result();

                if ($result && $result->num_rows > 0) {
                } else {
                    $sql = "INSERT INTO employees (name, emp_code, emp_head, status) VALUES (?,?,?,?)";
                    $stmt = $con->prepare($sql);

                    $stmt->bind_param("ssss", $emp_name, $emp_code, $emp_head, $emp_status);

                    // echo "<pre>";
                    // print_r($stmt->execute());
                    // die;

                    if (!$stmt->execute()) {
                        echo "Error inserting row: " . $stmt->error;
                    }
                }
            }
        }
        echo "Data imported successfully!";
        echo '<script>
        window.location.href = "' . SITTE_URL . 'addemployee.php";
        </script>';
    } catch (Exception $e) {
        die('Error loading file: ' . $e->getMessage());
    } finally {
        unlink($tempFilePath);
    }
}


if (!isset($_SESSION['admin_name'])) {

    header("Location: adminlogin.php");

    exit();
}



if (isset($_GET['id']) && isset($_GET['delete']) && $_GET['delete'] == 1) {
    $emp_id = $_GET['id'];

    $query = mysqli_query($con, "DELETE FROM employees WHERE emp_code='$emp_id'");
    if ($query) {
        echo "<script>
        window.location.href='" . SITTE_URL . "addemployee.php';
        </script>";
        die;
    }
}

if (isset($_GET['id']) && isset($_GET['status'])) {

    $emp_id = $_GET['id'];
    $status = $_GET['status'];


    $update_query = mysqli_query($con, "UPDATE employees SET status=$status WHERE emp_code='$emp_id'");

    if ($update_query) {
        echo "<script>
        window.location.href='" . SITTE_URL . "addemployee.php';
        </script>";
        die;
    }
}

$successMessage = '';

if (isset($_POST['submit']) && isset($_POST['name']) && isset($_POST['emp_code']) && isset($_POST['emp_head']) && isset($_POST['status'])) {
    $name = $_POST['name'];
    $emp_code = $_POST['emp_code'];
    $emp_head = $_POST['emp_head'];
    $status = $_POST['status'];

    $checkemployee = mysqli_query($con, "SELECT * FROM employees WHERE emp_code='$emp_code'");

    if (mysqli_num_rows($checkemployee) > 0) {
        echo "<script>
            alert('This Employee Code is already exist');
            window.location.href='" . SITTE_URL . "addemployee.php';
            </script>";
        die;
    }


    // echo "<pre>";
    // print_r($date);
    // die();

    $sql = "INSERT INTO employees (name,emp_code,emp_head,status) VALUES ('$name','$emp_code','$emp_head','$status')";

    if ($con->query($sql) === TRUE) {
        $successMessage = "New Employee added successfully";
    } else {
        echo "<p class='error'>Error: " . $sql . "<br>" . $con->error . "</p>";
    }
}

// $con->close(); // SuKH
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employees</title>

    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"> -->

    <style>
        .content-body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            /* min-height: 100vh; */
            margin: 0;
        }

        .container {
            background-color: #ffffff;
            width: 90%;
            max-width: 500px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            margin: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin: 10px 0 5px;
            color: #555;
        }

        input[type="date"],
        input[type="text"],
        input[type="number"],
        select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
        }

        button {
            background-color: rgb(20, 64, 105);
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            margin-top: 15px;
            cursor: pointer;
        }

        button:hover {
            background-color: rgb(20, 64, 105);
        }


        .success {
            background-color: green;
            color: white;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .error {
            color: red;
            text-align: center;
        }

        @media (max-width: 500px) {
            .container {
                width: 100%;
                padding: 15px;
            }

            h2 {
                font-size: 20px;
            }

            input,
            select,
            button {
                font-size: 14px;
            }
        }
    </style>



    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>

</head>

<body>

    <?php

    include "navbar.php";

    ?>
    <div class="content-body">
        <div class="container">
            <h2>Add Employees</h2>
            <form action="addemployee.php" method="post">

                <?php if (!empty($successMessage)): ?>
                    <p class="success"><?php echo $successMessage; ?></p>
                <?php endif; ?>

                <label for="name">Employee Name:</label>
                <input type="text" name="name" id="name" required>

                <label for="emp_code">Employee Code:</label>
                <input type="text" name="emp_code" id="emp_code" required>

                <label for="emp_head">Buisness Head:</label>
                <input type="text" name="emp_head" id="emp_head" required>

                <label for="status">Status:</label>
                <select name="status" id="status" required>
                    <option value="1">Enable</option>
                    <option value="0">Disable</option>
                </select>

                <button type="submit" name="submit">Add Employee</button>
            </form>

            <br>

            <form method="POST" enctype="multipart/form-data" onsubmit="process()">
                <label for="xlsx_file">Please select an XLSX file to upload.</label>
                <input type="file" name="xlsx_file" id="xlsx_file" required>
                <button type="submit" name="submit_xlbtn" id="xl_import_btn">Import</button>
            </form>
        </div>
    </div>


    <div class="content-body">
        <div class="container" style="max-width: 100%;">
            <table style="width: 100%;" id="employee_datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee Name</th>
                        <th>Employee Code</th>
                        <th>Buisness Head</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody style="text-align:center;">
                    <?php
                    $emp_data = mysqli_query($con, 'SELECT * FROM employees');
                    if (mysqli_num_rows($emp_data) > 0) {
                        $sr = 0;
                        while ($emp = mysqli_fetch_assoc($emp_data)) {

                            if ($emp['status']) {
                                $button = "<a href='/addemployee.php?id=$emp[emp_code]&status=0'><button class='btn btn-success' style='background-color:red;'>Disable</button></a>";
                            } else {
                                $button = "<a href='/addemployee.php?id=$emp[emp_code]&status=1'><button class='btn btn-success'>Enable</button></a>";
                            }

                            $button .= "<a href='/addemployee.php?id=$emp[emp_code]&delete=1'><button class='btn btn-success' style='margin-left:5px;background-color:red;'>Delete</button></a>";

                            $sr++;
                            echo "<tr>
                            <td>$sr</td>
                            <td>$emp[name]</td>
                            <td>$emp[emp_code]</td>
                            <td>$emp[emp_head]</td>
                            <td>$emp[status]</td>
                            <td>$button</td>
                            </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>


    <script>
        function process() {
            this.style.display = "none";
        }

        var table = new DataTable("#employee_datatable");
    </script>
</body>

</html>