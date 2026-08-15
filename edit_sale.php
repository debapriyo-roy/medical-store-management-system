<?php
require_once 'core.php';

/** @var mysqli $conn */

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: sales.php");
    exit();
}

$sale_id = (int)$_GET['id'];

$stmt = $conn->prepare("
    SELECT
        id,
        customer_name,
        sold_medicine,
        quantity,
        unit_price,
        cost_price,
        sale_date
    FROM sales
    WHERE id = ?
");

$stmt->bind_param("i", $sale_id);
$stmt->execute();

$result = $stmt->get_result();
$sale = $result->fetch_assoc();

$stmt->close();

if (!$sale) {
    die("Sale not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Sale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-5">

<h2 class="mb-4">Edit Sale</h2>

<form action="update_sale.php" method="post">

    <input type="hidden" name="sale_id" value="<?= $sale['id']; ?>">

    <div class="mb-3">
        <label>Customer Name:</label>
        <input
            type="text"
            name="customer_name"
            class="form-control"
            value="<?= htmlspecialchars($sale['customer_name']); ?>"
            required>
    </div>

    <div class="mb-3">
        <label>Medicine:</label>
        <input
            type="text"
            class="form-control"
            value="<?= htmlspecialchars($sale['sold_medicine']); ?>"
            readonly>
    </div>

    <div class="mb-3">
        <label>Quantity:</label>
        <input
            type="text"
            class="form-control"
            value="<?= $sale['quantity']; ?>"
            readonly>
    </div>

    <div class="mb-3">
        <label>Selling Price:</label>
        <input
            type="number"
            name="unit_price"
            class="form-control"
            value="<?= $sale['unit_price']; ?>"
            step="0.01"
            min="0.01"
            required>
    </div>

    <div class="mb-3">
        <label>Buying Price:</label>
        <input
            type="text"
            class="form-control"
            value="₹ <?= number_format($sale['cost_price'], 2); ?>"
            readonly>
    </div>

    <div class="mb-3">
        <label>Sale Date:</label>
        <input
            type="date"
            name="sale_date"
            class="form-control"
            value="<?= $sale['sale_date']; ?>"
            required>
    </div>

    <button type="submit" class="btn btn-success">Update Sale</button>
    <a href="sales.php" class="btn btn-secondary">Cancel</a>

</form>

</body>
</html>