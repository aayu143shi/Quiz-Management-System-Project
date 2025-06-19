<?php
include "connect.php";

session_start();

if (!isset($_SESSION['admin_name'])) {

    header("Location: adminlogin.php");

    exit();
}
$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // $date = $_POST['date'];
    $date = date("Y-m-d");
    $time = $_POST['time'];
    $date = $date . " " . $time;
    $validtime = $_POST['validtime'];
    $questionask = $_POST['questionask'];
    $testno = $_POST['testno'];
    // $status = $_POST['status'];
    $status = 1;
    $select_branch = $_POST['select_branch'];
    $question_categories = serialize($_POST['question_categories']);

    // print_r($question_categories);
    // die;

    if (empty($testno)) {
        return;
    }


    $checktest = mysqli_query($con, "SELECT * FROM sys_param WHERE testno=$testno");

    if (mysqli_num_rows($checktest) > 0) {
        echo "<script>
            alert('This Test No. Already Use Kindly Enter New No.');
            window.location.href='" . SITTE_URL . "addtest.php';
            </script>";
        die;
    }


    // echo "<pre>";
    // print_r($date);
    // die();

    $sql = "INSERT INTO sys_param (branch,categories,date, validtime, questionask, testno, status) VALUES ('$select_branch','$question_categories','$date','$validtime', '$questionask', '$testno', '$status')";

    if ($con->query($sql) === TRUE) {
        $successMessage = "New test added successfully";
    } else {
        echo "<p class='error'>Error: " . $sql . "<br>" . $con->error . "</p>";
    }
}


$branches = mysqli_query($con, "SELECT id,branch FROM branches GROUP BY branch");

$questioncategories = mysqli_query($con, "SELECT id,category FROM questiontable GROUP BY category");

$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Test</title>

    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"> -->

    <style>
        .content-body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>

    <?php

    include "navbar.php";

    ?>
    <div class="content-body">
        <div class="container">
            <h2>Generate Test</h2>
            <form action="addtest.php" method="post">

                <?php if (!empty($successMessage)): ?>
                    <p class="success"><?php echo $successMessage; ?></p>
                <?php endif; ?>

                <label for="select_branch">Select Branch:</label>
                <select name="select_branch" id="select_branch" required>
                    <option value="">Select Here</option>
                    <?php
                    while ($branch = mysqli_fetch_assoc($branches)) {
                        echo "<option value='$branch[branch]'>$branch[branch]</option>";
                        // echo "<option value='$branch[id]'>$branch[branch]</option>";
                    }
                    ?>
                </select>

                <label for="date">Date:</label>
                <input type="date" value="<?php echo date('Y-m-d'); ?>" disabled>

                <label for="date">Time:</label>
                <input type="time" name="time" id="timeInput" required>

                <label for="validtime">Valid Time:</label>
                <input type="text" name="validtime" id="validtime" required>

                <label for="question_categories">Select Question Categories:</label>
                <!-- <select name="question_categories[]" id="question_categories" multiple required>
                    <option value="">Select Here</option>
                    <?php
                    // while ($cat = mysqli_fetch_assoc($questioncategories)) {
                    //     echo "<option value='$cat[category]'>$cat[category]</option>";
                    // }
                    ?>
                </select> -->

                <div style="height: 150px;overflow-y:scroll;border:2px solid;">
                    <?php
                    while ($cat = mysqli_fetch_assoc($questioncategories)) {
                        echo '<p><input type="checkbox" name="question_categories[]" id="' . $cat['id'] . '" value="' . $cat['category'] . '"> <label for="' . $cat['id'] . '">' . $cat['category'] . '</lable></p>';
                    }
                    ?>
                </div>



                <label for="question_ask">Question Ask:</label>
                <input type="text" name="questionask" id="questionask" required>

                <div style="display: flex; align-items:center;gap:5px;">
                    <div style="flex-grow: 1;">
                        <label for="test_no">Test No:</label>
                        <input type="number" name="testno" id="testno" required readonly>
                    </div>
                    <div style="display: flex; align-items:center;">
                        <button type="button" id="generate_testno_btn" class="btn btn-success">Generate Test No</button>
                    </div>
                </div>

                <!-- <label for="status">Status:</label>
                 <select name="status" id="status" required>
                     <option value="1">enable</option>
                     <option value="0">disable</option>
                 </select> -->

                <button type="submit">Add Test</button>
            </form>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            $("#generate_testno_btn").click(function() {

                var data = {
                    action: "generate_test_number"
                };

                $.ajax({
                    url: "/ajax.php",
                    type: "POST",
                    data: data,
                    success: function(res) {

                        $("#testno").val(res);
                    }

                })
            })

            jQuery('#question_categories').select2();

        })
    </script>

</body>

</html>