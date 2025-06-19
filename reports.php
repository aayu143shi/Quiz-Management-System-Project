<?php

include "connect.php";
include "navbar.php";

$sql = "SELECT * FROM testcomplete";

$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Results</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.1.8/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.1.8/datatables.min.js"></script>
    <style>
        /* Navbar styling */
        .navbar {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            display: flex;
            justify-content: center;
            padding: 10px 0;
            position: fixed;
            top: 0;
            z-index: 1000;
        }

        .navbar a {
            color: white;
            padding: 10px 20px;
            margin: 0 10px;
            text-decoration: none;
            font-weight: bold;
        }

        .navbar a:hover {
            background-color: #45a049;
        }

        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            background-color: #f4f4f9;
        }

        
        .content {
            margin-top: 60px;
            width: 80%;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        .edit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>

<body>

    
    <div class="navbar">
        <a href="addtest.php">Add Test</a>
        <a href="alltests.php">My Test</a>
        <a href="importquestions.php">Import Questions</a>
    </div>

    <div class="content">
        <h2>Test Results</h2>

        <table id="recordstable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Test No</th>
                    <th>Date</th>
                    
                    <th>Operation</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['testno']); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td>
                            <a class="edit-btn" href="<?php echo SITTE_URL; ?>submit.php?id=<?php echo $row['userid']; ?>">view</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        jQuery(document).ready(function($) {
            $('#recordstable').DataTable();
        });
    </script>
</body>

</html>
