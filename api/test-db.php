<?php
require_once __DIR__. '/config/database.php';
echo json_encode(['status' => 'success', 'message' => 'Database connected!']);
?>