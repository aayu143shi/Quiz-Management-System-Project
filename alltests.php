<?php



include "connect.php";
session_start();

$sql = "SELECT * FROM sys_param";

$result = $con->query($sql);


if (isset($_GET['testid']) && !empty($_GET['testid'])) {
    $value = $_GET['value'];

    $testupdate_query = "UPDATE sys_param SET status='$value', enabledtime=NOW() WHERE id=$_GET[testid]";

    if (mysqli_query($con, $testupdate_query)) {

        echo "Record updated successfully";

        header("Location: alltests.php");

        exit();

    } else {

        echo "Error updating record: " . mysqli_error($conn);

    }

}



if (isset($_GET['deltestid']) && !empty($_GET['deltestid'])) {



    $deltestid = $_GET['deltestid'];



    $query = "DELETE FROM sys_param WHERE id=$deltestid";

    if (mysqli_query($con, $query)) {

        header("Location: alltests.php");

        exit();

    } else {

        echo "Error updating record: " . mysqli_error($conn);

    }

}

?>



<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>View Tests</title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.1.8/datatables.min.css" rel="stylesheet">

    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.1.8/datatables.min.js"></script>

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



        .btn-danger {

            background-color: #dc3545;

        }





        .container {

            width: 90%;

            max-width: 800px;

            background-color: #ffffff;

            padding: 20px;

            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);

            border-radius: 8px;

        }



        h2 {

            text-align: center;

            color: #333;

        }



        table {

            width: 100%;

            border-collapse: collapse;

            margin-top: 20px;

        }



        th,

        td {

            padding: 12px;

            border-bottom: 1px solid #ddd;

            text-align: left;

        }



        th {

            background-color: #4CAF50;

            color: white;

        }



        td {

            color: #333;

        }



        .status-enable {

            color: green;

            font-weight: bold;

        }



        .status-disable {

            color: red;

            font-weight: bold;

        }



        @media (max-width: 600px) {

            .container {

                width: 100%;

                padding: 15px;

            }



            table,

            th,

            td {

                font-size: 14px;

            }



            th,

            td {

                padding: 8px;

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

        <h2>Test Details</h2>



        <?php if ($result->num_rows > 0): ?>

            <table id="testdetailstable">

                <thead>

                    <tr>

                        <th>Date</th>

                        <th>Validtime</th>

                        <th>Question Ask</th>

                        <th>Test No</th>

                        <th>Status</th>
                    </tr>

                </thead>

                <tbody>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr>

                            <td><?php echo htmlspecialchars($row["date"]); ?></td>

                            <td><?php echo htmlspecialchars($row["validtime"]); ?></td>

                            <td><?php echo htmlspecialchars($row["questionask"]); ?></td>

                            <td><?php echo htmlspecialchars($row["testno"]); ?></td>

                            <td>
                                <!-- <a href="<?php echo SITTE_URL; ?>alltests.php?testid=<?php echo $row['id']; ?>&value=1" class="btn <?php echo ($row["status"] == 1) ? 'btn-primary' : 'btn-secondary' ?>" style='display: inline-block; margin: 7px;'>Enable</a> -->

                                <a href="<?php echo SITTE_URL; ?>alltests.php?testid=<?php echo $row['id']; ?>&value=0" class="btn <?php echo ($row["status"] == 0) ? 'btn-secondary' : 'btn-primary' ?>" style='display: inline-block; margin: 7px;'>Disable</a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <p style="text-align: center; color: #555;">No test details found.</p>

        <?php endif; ?>



    </div>
</div>


    <?php $con->close(); ?>



    <script>

        jQuery(document).ready(function($) {

            $('#testdetailstable').DataTable();

        })

    </script>

</body>



</html>