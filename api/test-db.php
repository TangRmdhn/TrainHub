<?php
require_once 'api/config/database.php';
echo json_encode(['status' => 'success', 'message' => 'Database connected!']);
?>