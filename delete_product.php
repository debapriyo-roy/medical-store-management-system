<?php
require_once 'core.php';

/** @var mysqli $conn */

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = (int)$_GET['id'];

/*
 * Only allow deletion when stock is zero.
 */
$stmt = $conn->prepare("
    DELETE FROM products
    WHERE id = ?
      AND medicine_quantity = 0
");

$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {
        echo "<script>
            alert('Product deleted successfully.');
            window.location.href='products.php';
        </script>";
    } else {
        echo "<script>
            alert('Product cannot be deleted because stock is still available.');
            window.location.href='products.php';
        </script>";
    }

} else {

    echo "<script>
        alert('Error deleting product.');
        window.location.href='products.php';
    </script>";
}

$stmt->close();
$conn->close();
?>