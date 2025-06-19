<?php

session_start();
session_destroy();

header('Location: adminlogin.php');
exit;

// unset($_SESSION['admin_name']);
// unset($_SESSION['admin_id']);

// header('Location: adminlogin.php');
// exit;