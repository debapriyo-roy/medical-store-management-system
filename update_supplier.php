<?php
require_once 'core.php';

/** @var mysqli $conn */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: suppliers.php");
    exit();
}

$supplier_id = (int)($_POST['supplier_id'] ?? 0);
$supplier_name = trim($_POST['supplier_name'] ?? '');
$contact_number = trim($_POST['contact_number'] ?? '');
$address = trim($_POST['address'] ?? '');
$supply_date = $_POST['supply_date'] ?? '';
$supply_medicine = trim($_POST['supply_medicine'] ?? '');
$total_price = (float)($_POST['total_price'] ?? 0);

if (
    $supplier_id <= 0 ||
    $supplier_name === '' ||
    $contact_number === '' ||
    $address === '' ||
    $supply_date === '' ||
    $supply_medicine === '' ||
    $total_price <= 0
) {
    die("SYSTEM ERROR: Invalid supplier information.");
}

$stmt = $conn->prepare("
    UPDATE suppliers
    SET
        supplier_name = ?,
        contact_number = ?,
        address = ?,
        supply_date = ?,
        supply_medicine = ?,
        total_price = ?
    WHERE id = ?
");

$stmt->bind_param(
    "sssssdi",
    $supplier_name,
    $contact_number,
    $address,
    $supply_date,
    $supply_medicine,
    $total_price,
    $supplier_id
);

if ($stmt->execute()) {

    $stmt->close();
    $conn->close();

    echo "<script>
        alert('Supplier updated successfully.');
        window.location.href='suppliers.php';
    </script>";
    exit();

} else {

    $error = $stmt->error;

    $stmt->close();
    $conn->close();

    echo "<script>
        alert(" . json_encode("Error updating supplier: " . $error) . ");
        window.location.href='suppliers.php';
    </script>";
    exit();
}
?>