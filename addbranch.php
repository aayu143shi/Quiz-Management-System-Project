<?php
include "connect.php";

session_start();

if (!isset($_SESSION['admin_name'])) {

    header("Location: adminlogin.php");

    exit();
}

if (isset($_GET['id']) && isset($_GET['delete']) && $_GET['delete'] == 1) {
    $branch_id = $_GET['id'];

    $query = mysqli_query($con, "DELETE FROM branches WHERE id=$branch_id");
    if ($query) {
        echo "<script>
        window.location.href='" . SITTE_URL . "addbranch.php';
        </script>";
        die;
    }
}

if (isset($_GET['id']) && isset($_GET['status'])) {

    $branch_id = $_GET['id'];
    $status = $_GET['status'];


    $update_query = mysqli_query($con, "UPDATE branches SET status=$status WHERE id=$branch_id");

    if ($update_query) {
        echo "<script>
        window.location.href='" . SITTE_URL . "addbranch.php';
        </script>";
        die;
    }
}

$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $branch = $_POST['branch'];

    $status = $_POST['status'];

    $checkbranch = mysqli_query($con, "SELECT * FROM branches WHERE branch='$branch'");

    if (mysqli_num_rows($checkbranch) > 0) {
        echo "<script>
            alert('This Branch. Already Use Kindly Enter New No.');
            window.location.href='" . SITTE_URL . "addbranch.php';
            </script>";
        die;
    }

    $sql = "INSERT INTO branches (branch,status) VALUES ('$branch','$status')";

    if ($con->query($sql) === TRUE) {
        $successMessage = "New Branch added successfully";
    } else {
        echo "<p class='error'>Error: " . $sql . "<br>" . $con->error . "</p>";
    }
}

// $con->close(); // SUKH
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Test</title>
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
</head>

<body>

    <?php

    include "navbar.php";

    ?>
    <div class="content-body">
        <div class="container">
            <h2>Add Branch</h2>
            <form method="post">

                <?php if (!empty($successMessage)): ?>
                    <p class="success"><?php echo $successMessage; ?></p>
                <?php endif; ?>

                <label for="branch">Select Branch:</label>
                <input type="text" name="branch" id="branch" required>

                <label for="status">Status : </label>
                <select name="status" id="status">
                    <option value="1">Enable</option>
                    <option value="0">Disable</option>
                </select>

                <button type="submit">Add Branch</button>
            </form>
        </div>
    </div>
    <div class="content-body">
        <div class="container">
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Branch</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody style="text-align:center;">
                    <?php
                    $branch_data = mysqli_query($con, 'SELECT * FROM branches');
                    if (mysqli_num_rows($branch_data) > 0) {
                        $sr = 0;
                        while ($branch = mysqli_fetch_assoc($branch_data)) {

                            if ($branch['status']) {
                                $button = "<a href='/addbranch.php?id=$branch[id]&status=0'><button class='btn btn-success' style='background-color:red;'>Disable</button></a>";
                            } else {
                                $button = "<a href='/addbranch.php?id=$branch[id]&status=1'><button class='btn btn-success'>Enable</button></a>";
                            }

                            $button .= "<a href='/addbranch.php?id=$branch[id]&delete=1'><button class='btn btn-success' style='margin-left:5px;background-color:red;'>Delete</button></a>";

                            $sr++;
                            echo "<tr>
                            <td>$sr</td>
                            <td>$branch[branch]</td>
                            <td>$branch[status]</td>
                            <td>$button</td>
                            </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>