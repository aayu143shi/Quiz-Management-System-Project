<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

include "connect.php";
session_start();
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

use PhpOffice\PhpSpreadsheet\Spreadsheet;





if (isset($_POST['submit']) && isset($_FILES['xlsx_file'])) {

    $file = $_FILES['xlsx_file'];



    $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);

    if ($fileType != 'xlsx') {

        die("Please upload a valid XLSX file.");
    }



    $tempFilePath = 'uploads/' . $file['name'];

    if (!move_uploaded_file($file['tmp_name'], $tempFilePath)) {

        die("Failed to upload file.");
    }


    // echo $tempFilePath ;
    // die;

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

            // if (empty($data)) {

            //     echo "No data found in row $rowCount<br>";

            //     continue; // Skip to the next row

            // }


            list($question, $optionA, $optionB, $optionC, $optionD, $answer, $category) = $data;

            // echo "<pre>";

            // print_r($data);
            // print_r($question);

            // die();

            if (!empty($question)) {

                $questiontable_sql = "SELECT * FROM questiontable WHERE question = ? AND category = ?";

                $stmtCheck = $con->prepare($questiontable_sql);

                if (!$stmtCheck) {

                    die("Error preparing check statement: " . $con->error);
                }

                $stmtCheck->bind_param("ss", $question, $category);

                $stmtCheck->execute();

                $result = $stmtCheck->get_result();

                if ($result && $result->num_rows > 0) {

                    echo "Question already exist <br>";
                } else {

                    $sql = "INSERT INTO questiontable (question, optionA, optionB, optionC,optionD,answer,category) VALUES (?,?,?,?,?,?,?)";

                    $stmt = $con->prepare($sql);

                    $stmt->bind_param("sssssss", $question, $optionA, $optionB, $optionC, $optionD, $answer, $category);

                    // $stmt->execute();

                    if (!$stmt->execute()) {
                        echo "Error inserting row: " . $stmt->error;
                    }
                }
            }
        }

        echo "Data imported successfully!";

?>
        <script>
            window.location.href = "<?php echo SITTE_URL;  ?>admin_dashboard.php";
        </script>

<?php

        // header('location : admin_dashboard.php');

        // exit;

    } catch (Exception $e) {

        die('Error loading file: ' . $e->getMessage());
    } finally {

        unlink($tempFilePath);
    }
}

?>



<!DOCTYPE html>

<html lang="en">



<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Import Questions</title>

    <style>
        .content-body {

            font-family: Arial, sans-serif;

            display: flex;

            justify-content: center;

            align-items: center;

            height: 100vh;

            margin: 0;

            background-color: f0f0f0;

        }



        .form-container {

            max-width: 500px;

            width: 90%;

            padding: 20px;

            border: 1px solid #ddd;

            border-radius: 8px;

            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);

        }



        .form-group {

            margin-bottom: 15px;

        }



        label {

            display: block;

            margin-bottom: 5px;

            font-weight: bold;

        }



        input[type="text"],

        input[type="date"],

        input[type="number"] {

            width: 100%;

            padding: 8px;

            border: 1px solid #ddd;

            border-radius: 4px;

        }



        .submit {

            width: 100%;

            padding: 10px;

            background-color: rgb(20, 64, 105);

            color: white;

            border: none;

            border-radius: 4px;

            cursor: pointer;

            font-size: 16px;

        }



        .submit:hover {

            background-color: rgb(20, 64, 105);

        }



        .label-info {

            font-size: 14px;

            color: #555;

        }
    </style>

</head>



<body>

    <?php
    include "navbar.php";
    ?>

    <div class="content-body">
        <div class="form-container">



            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">

                <div class="form-group">

                    <label>Please select an XLSX file to upload.</label>

                    <input type="file" name="xlsx_file" accept=".xlsx" required>

                </div>





                <button type="submit" class="submit" name="submit">Upload and Import</button>

            </form>

        </div>

    </div>
</body>



</html>