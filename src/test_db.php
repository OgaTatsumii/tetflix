<?php
require_once 'config/database.php';

$database = new Database();
$database->testConnection();
?>