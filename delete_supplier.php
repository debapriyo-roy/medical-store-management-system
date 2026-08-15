<?php
require_once 'core.php';

/** @var mysqli $conn */

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: suppliers.php");
    exit();
}

$supplier_id = (int)$_GET['id'];

$stmt = $conn->prepare("
    DELETE FROM suppliers
    WHERE id = ?
");

$stmt->bind_param("i", $supplier_id);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {
        echo "<script>
            alert('Supplier deleted successfully.');
            window.location.href='suppliers.php';
        </script>";
    } else {
        echo "<script>
            alert('Supplier record not found.');
            window.location.href='suppliers.php';
        </script>";
    }

} else {

    $error = $stmt->error;

    echo "<script>
        alert(" . json_encode("Error deleting supplier: " . $error) . ");
        window.location.href='suppliers.php';
    </script>";
}

$stmt->close();
$conn->close();
?>