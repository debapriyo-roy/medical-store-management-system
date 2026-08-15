<?php
require_once 'core.php';

/** @var mysqli $conn */

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: sales.php");
    exit();
}

$sale_id = (int)$_GET['id'];

$stmt = $conn->prepare("DELETE FROM sales WHERE id = ?");
$stmt->bind_param("i", $sale_id);

if ($stmt->execute()) {
    echo "<script>
        alert('Sale deleted successfully.');
        window.location.href='sales.php';
    </script>";
} else {
    echo "<script>
        alert('Error deleting sale.');
        window.location.href='sales.php';
    </script>";
}

$stmt->close();
$conn->close();
?>