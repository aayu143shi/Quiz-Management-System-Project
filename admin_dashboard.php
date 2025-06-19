<?php
include "connect.php";

session_start();

if (!isset($_SESSION['admin_name'])) {
    header("Location: adminlogin.php");
    exit();
}

if (isset($_GET['d_id'])) {
    $stmt = $con->prepare("DELETE FROM questiontable WHERE id = ?");
    $stmt->bind_param("i", $_GET['d_id']);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit();
    }
}

$sql = "SELECT * FROM questiontable";
$result = $con->query($sql);

$config_sql = "SELECT * FROM config WHERE id=1";
$config_result = mysqli_query($con, $config_sql);

if (mysqli_num_rows($config_result) > 0) {
    $enabledata = mysqli_fetch_assoc($config_result);
    $enable = $enabledata['value'];
}

if (
    isset($_GET['configid']) && $_GET['configid'] == 1 &&
    isset($_GET['key']) && $_GET['key'] == 'enable' &&
    isset($_GET['value'])
) {
    $configid = $_GET['configid'];
    $key = $_GET['key'];
    $value = $_GET['value'];

    $configupdate_sql = "UPDATE config SET value='$value' WHERE id=$configid";
    if (mysqli_query($con, $configupdate_sql)) {
        echo "Record updated successfully";
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($con);
    }
}

if (isset($_POST['deletequestions_btn'])) {
    foreach ($_POST['questionselect'] as $id) {
        $deletequestion_sql = "DELETE FROM questiontable WHERE id=$id";
        mysqli_query($con, $deletequestion_sql);
    }
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.1.8/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.1.8/datatables.min.js"></script>
    <style>
        .content-body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn {
            padding: 8px 12px;
            border: none;
            color: #fff;
            cursor: pointer;
            margin-right: 5px;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-success {
            background-color: #28a745;
        }

        .btn-warning {
            background-color: #ffc107;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .table th {
            background-color: #343a40;
            color: #fff;
        }
    </style>
</head>

<body>
    <?php
    include "navbar.php";
    ?>

    <div class="content-body">
        <div class="container">
            <!-- <h2>Admin Dashboard</h2> -->

            <form action="" method="post">
                <div class="text-right mb-3" style="text-align: right;">
                    <input type="submit" class="btn btn-danger" value="Delete Questions" name="deletequestions_btn">
                </div>
                <table class="table" id="quationtable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="allselect"></th>
                            <th>S.No</th>
                            <th>Question</th>
                            <th>Option A</th>
                            <th>Option B</th>
                            <th>Option C</th>
                            <th>Option D</th>
                            <th>Answer</th>
                            <th>Category</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            $i = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                            <td><input type='checkbox' name='questionselect[]' value='{$row['id']}'></td>
                            <td>{$i}</td>
                            <td>{$row['question']}</td>
                            <td>{$row['optionA']}</td>
                            <td>{$row['optionB']}</td>
                            <td>{$row['optionC']}</td>
                            <td>{$row['optionD']}</td>
                            <td>{$row['answer']}</td>
                            <td>{$row['category']}</td>
                        </tr>";
                                $i++;
                            }
                        } else {
                            echo "<tr><td colspan='8'>No questions found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </form>
        </div>

    </div>

    <script>
        jQuery(document).ready(function($) {
            $('#allselect').click(function() {
                var allselect = $(this).prop('checked');
                if (allselect) {
                    $("input[name='questionselect[]']").prop('checked', true);
                } else {
                    $("input[name='questionselect[]']").prop('checked', false);
                }
            });

            $('#quationtable').DataTable({
                "scrollX": true,
                // "autoWidth": true,
                columnDefs: [{
                    orderable: false,
                    targets: 0
                }]
            });

        });
    </script>
</body>

</html>