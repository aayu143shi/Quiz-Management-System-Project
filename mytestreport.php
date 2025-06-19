<?php
include "connect.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

unset($_SESSION['testdetails']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['view'])) {
        $fromDate = $_POST['fromDate'];
        $toDate = $_POST['toDate'];
        // echo "<script>alert('Viewing My Test Report from $fromDate to $toDate');</script>";

        $sql = "SELECT * FROM sys_param WHERE date BETWEEN '$fromDate 00:00:00' AND '$toDate 23:59:59'";
        $result = mysqli_query($con, $sql);

        unset($_SESSION['testdetails']);

        $_SESSION['testdetails'] = [];
        if (mysqli_num_rows($result) > 0) {
            while ($testdetails = mysqli_fetch_assoc($result)) {
                $_SESSION['testdetails'][] = $testdetails;
            }
        } else {
?>
            <script>
                alert('Test Not Found.');
                window.location.href = "<?php echo SITTE_URL ?>mytestreport.php";
            </script>
        <?php
            die();
        }
    }

    if (isset($_POST['export'])) {
        $fromDate = $_POST['fromDate'];
        $toDate = $_POST['toDate'];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Branch');
        $sheet->setCellValue('B1', 'Date');
        $sheet->setCellValue('C1', 'Valid Time');
        $sheet->setCellValue('D1', 'Question Ask');
        $sheet->setCellValue('E1', 'Question Categories');
        $sheet->setCellValue('F1', 'Test Number');

        $sql = "SELECT * FROM sys_param WHERE date BETWEEN '$fromDate 00:00:00' AND '$toDate 23:59:59'";

        // print_r($sql);
        // die();

        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) > 0) {
            $row = 2;

            while ($testdetails = mysqli_fetch_assoc($result)) {
                // echo "<pre>";
                // print_r($testdetails);
                // die();

                $categories = implode(' | ',unserialize($testdetails['categories']));

                $sheet->setCellValue('A' . $row, $testdetails['branch']);
                $sheet->setCellValue('B' . $row, $testdetails['date']);
                $sheet->setCellValue('C' . $row, $testdetails['validtime']);
                $sheet->setCellValue('D' . $row, $testdetails['questionask']);
                $sheet->setCellValue('E' . $row, $categories);
                $sheet->setCellValue('F' . $row, $testdetails['testno']);
                $row++;
            }
        } else {
        ?>
            <script>
                alert('Test Not Found.');
                window.location.href = "<?php echo SITTE_URL ?>mytestreport.php";
            </script>
<?php
            die();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="mytestreport.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Test Report</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.1.8/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.1.8/datatables.min.js"></script>
    <style>
        .content-body {
            font-family: Arial, sans-serif;
            /* display: flex; */
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }

        .container {
            /* max-width: 400px; */
            /* width: 100%; */
            background-color: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .title {
            font-size: 1.2em;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-group label {
            margin-bottom: 5px;
        }

        .form-group input {
            padding: 8px;
            font-size: 1em;
            width: 100%;
            max-width: 200px;
            box-sizing: border-box;
            text-align: center;
        }

        .btn {
            margin-top: 10px;
            padding: 8px 16px;
            font-size: 1em;
            cursor: pointer;
            border: none;
            background-color: rgb(20, 64, 105);
            color: #fff;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: rgb(20, 64, 105);
        }

        .note {
            font-size: 0.8em;
            color: #666;
            text-align: center;
        }
    </style>

</head>

<body>
    <?php
    include "navbar.php";
    ?>

    <div class="content-body">
        <div class="container">
            <div class="title">Generate Test Report</div>
            <?php
            $sr = 0;
            if (isset($_SESSION['testdetails']) && !empty($_SESSION['testdetails'])) {
                echo '<a href="' . SITTE_URL . 'mytestreport.php" class="btn">Back</a>';

                echo "<table id='mytestreportfetchtable'>
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Branch</th>
                        <th>Date</th>
                        <th>Valid Time</th>
                        <th>Question Ask</th>
                        <th>Question Category</th>
                        <th>Test Number</th>
                    </tr>
                </thead>
                <tbody>";
                foreach ($_SESSION['testdetails'] as $data) {

                    $categories = implode(' | ',unserialize($data['categories']));

                    // echo "<pre>";
                    // print_r($categories);

                    $sr++;
                    echo "<tr>";
                    echo "<td>$sr</td>";
                    echo "<td>$data[branch]</td>";
                    echo "<td>$data[date]</td>";
                    echo "<td>$data[validtime]</td>";
                    echo "<td>$data[questionask]</td>";
                    echo "<td>$categories</td>";
                    echo "<td>$data[testno]</td>";
                    echo "</tr>";
                }
                echo "</tbody>
                </table>";

                echo "<script>
                jQuery(document).ready(function($) {
                    $('#mytestreportfetchtable').DataTable();
                });
                </script>";
            } else { ?>
                <form method="post" style="display: flex;flex-direction: column;justify-content: center;">
                    <div class="form-group">
                        <label for="fromDate">From Date</label>
                        <input type="date" id="fromDate" name="fromDate" value="<?php echo Date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="toDate">To Date</label>
                        <input type="date" id="toDate" name="toDate" value="<?php echo Date('Y-m-d'); ?>" required>
                    </div>
                    <div style="padding: 0% 40%;display: flex;flex-direction: column;justify-content: center;">
                        <button type="submit" name="view" class="btn" style="display: flex;flex-direction: column;justify-content: center;align-items:center;">View</button>
                        <!-- <div class="note">By default, current date</div> -->
                        <button type="submit" name="export" class="btn" style="display: flex;flex-direction: column;justify-content: center;align-items:center;">Export</button>
                    </div>
                </form>

            <?php }
            ?>
        </div>
    </div>

</body>

</html>