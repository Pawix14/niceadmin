<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

$id = intval($_GET['id']);
$conn->query("UPDATE notifications SET is_read=1 WHERE id=$id");

echo json_encode(['success' => true]);
$conn->close();
?>
