<?php
if (!isset($_SESSION['admin_name'])) {
    // header("Location: adminlogin.php");
    echo "<script>window.location.href='/adminlogin.php'</script>";
    exit();
}

$filename = explode('/', $_SERVER['SCRIPT_NAME']);
$filename = $filename[count($filename) - 1];

?>

<style>
    .navbar {
        display: flex;
        align-items: center;
        background-color: rgb(20, 64, 105);
        padding: 10px;
    }

    .navbar a {
        color: white;
        padding: 10px 15px;
        text-decoration: none;
        font-weight: bold;
    }

    .navbar a:hover {
        background-color: rgb(20, 64, 107);
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropbtn {
        color: white;
        padding: 10px 15px;
        text-decoration: none;
        font-weight: bold;
        cursor: pointer;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        top: 100%;
        /* Positions the dropdown directly below the button */
        left: 0;
        background-color: #f9f9f9;
        min-width: 200px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        white-space: nowrap;
        /* Ensures each link stays on a single line */
    }

    .dropdown-content a:hover {
        background-color: #ddd;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }
</style>

<div class="navbar">
    <div class="dropdown">
        <a href="#" class="dropbtn">Entry</a>
        <div class="dropdown-content">
            <a href="addbranch.php" style="<?php echo ($filename == 'addbranch.php') ? 'background-color: #ddd' : '' ?>">Add Branch</a>
            <a href="addemployee.php" style="<?php echo ($filename == 'addemployee.php') ? 'background-color: #ddd' : '' ?>">Add Employee</a>
            <a href="addtest.php" style="<?php echo ($filename == 'addtest.php') ? 'background-color: #ddd' : '' ?>">Generate Test</a>
            <a href="importquestions.php" style="<?php echo ($filename == 'importquestions.php') ? 'background-color: #ddd' : '' ?>">Import Questions</a>

        </div>
    </div>
    <div class="dropdown">
        <a href="#" class="dropbtn">Report</a>
        <div class="dropdown-content">
            <!-- <a href="alltests.php" style="<?php echo ($filename == 'alltests.php') ? 'background-color: #ddd' : '' ?>">My Tests</a> -->
            <a href="mytestreport.php" style="<?php echo ($filename == 'mytestreport.php') ? 'background-color: #ddd' : '' ?>">Generate Test Report</a>
            <a href="mytestresultreport.php" style="<?php echo ($filename == 'mytestresultreport.php') ? 'background-color: #ddd' : '' ?>">Generate Test Result Report</a>
            <a href="admin_dashboard.php" style="<?php echo ($filename == 'admin_dashboard.php') ? 'background-color: #ddd' : '' ?>">Questions Report</a>
        </div>
    </div>
    <a href="logout.php" style="margin-left:auto;">Logout</a>
</div>