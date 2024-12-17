<?php
require_once 'db.php';

if (isset($_GET['set_id'])) {
    $set_id = $_GET['set_id'];

    $query = "SELECT word FROM words WHERE set_id = ? ORDER BY RAND()";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $set_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $words = [];
    while ($row = $result->fetch_assoc()) {
        $words[] = $row['word'];
    }

    echo json_encode($words);
}
?>
