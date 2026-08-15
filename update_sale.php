<?php
require_once 'core.php';

/** @var mysqli $conn */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: sales.php");
    exit();
}

$sale_id = (int)($_POST['sale_id'] ?? 0);
$customer_name = trim($_POST['customer_name'] ?? '');
$unit_price = (float)($_POST['unit_price'] ?? 0);
$sale_date = $_POST['sale_date'] ?? '';

if (
    $sale_id <= 0 ||
    $customer_name === '' ||
    $unit_price <= 0 ||
    $sale_date === ''
) {
    die("SYSTEM ERROR: Invalid sale information.");
}

$stmt = $conn->prepare("
    UPDATE sales
    SET
        customer_name = ?,
        unit_price = ?,
        sale_date = ?
    WHERE id = ?
");

$stmt->bind_param(
    "sdsi",
    $customer_name,
    $unit_price,
    $sale_date,
    $sale_id
);

if ($stmt->execute()) {

    $stmt->close();
    $conn->close();

    echo "<script>
        alert('Sale updated successfully.');
        window.location.href='sales.php';
    </script>";
    exit();

} else {

    $error = $stmt->error;

    $stmt->close();
    $conn->close();

    echo "<script>
        alert(" . json_encode("Error updating sale: " . $error) . ");
        window.location.href='sales.php';
    </script>";
    exit();
}
?>