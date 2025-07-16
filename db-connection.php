<?php
$host = "localhost";
$user = "root";
$password = "08310324"; 
$database = "bankDashboard"; 

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";

