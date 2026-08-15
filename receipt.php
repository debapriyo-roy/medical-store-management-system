<?php
require_once 'core.php';

/** @var mysqli $conn */

$invoice_id = trim($_GET['invoice_id'] ?? '');

if ($invoice_id === '') {
    die("Invalid invoice ID.");
}

$stmt = $conn->prepare("
    SELECT
        invoice_id,
        customer_name,
        sold_medicine,
        quantity,
        unit_price,
        sale_date
    FROM sales
    WHERE invoice_id = ?
    ORDER BY id ASC
");

$stmt->bind_param("s", $invoice_id);
$stmt->execute();

$result = $stmt->get_result();

$sales = [];

while ($row = $result->fetch_assoc()) {
    $sales[] = $row;
}

$stmt->close();

if (empty($sales)) {
    die("Invoice not found.");
}

$customer_name = $sales[0]['customer_name'];
$sale_date = $sales[0]['sale_date'];

$total_invoice = 0;
$total_quantity = 0;

foreach ($sales as $sale) {
    $line_total = (float)$sale['quantity'] * (float)$sale['unit_price'];

    $total_invoice += $line_total;
    $total_quantity += (int)$sale['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Invoice</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 30px;
        }

        .receipt {
            width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
        }

        .header p {
            margin: 5px 0;
        }

        .customer-info {
            margin-bottom: 20px;
        }

        .customer-info p {
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }

        .total-section {
            margin-top: 20px;
            text-align: right;
        }

        .total-section p {
            margin: 7px 0;
            font-size: 16px;
        }

        .grand-total {
            font-size: 20px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }

        .print-button {
            text-align: center;
            margin: 20px;
        }

        .print-button button {
            padding: 10px 20px;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            font-size: 15px;
            margin: 0 5px;
        }

        .print-btn {
            background: #198754;
        }

        .back-btn {
            background: #6c757d;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .receipt {
                width: 100%;
                box-shadow: none;
                border: none;
            }

            .print-button {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="receipt">

    <div class="header">
        <h1>Medicine Management</h1>
        <p>Sales Invoice</p>
    </div>

    <div class="customer-info">
        <p>
            <strong>Invoice ID:</strong>
            <?= htmlspecialchars($invoice_id); ?>
        </p>

        <p>
            <strong>Customer Name:</strong>
            <?= htmlspecialchars($customer_name); ?>
        </p>

        <p>
            <strong>Sale Date:</strong>
            <?= htmlspecialchars($sale_date); ?>
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Medicine</th>
                <th>Quantity</th>
                <th>Selling Price</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($sales as $sale): ?>

            <?php
            $line_total =
                (float)$sale['quantity'] *
                (float)$sale['unit_price'];
            ?>

            <tr>
                <td><?= htmlspecialchars($sale['sold_medicine']); ?></td>

                <td><?= (int)$sale['quantity']; ?></td>

                <td>
                    ₹ <?= number_format($sale['unit_price'], 2); ?>
                </td>

                <td>
                    ₹ <?= number_format($line_total, 2); ?>
                </td>
            </tr>

        <?php endforeach; ?>

        </tbody>
    </table>

    <div class="total-section">

        <p>
            <strong>Total Quantity:</strong>
            <?= number_format($total_quantity); ?>
        </p>

        <p class="grand-total">
            Total Invoice:
            ₹ <?= number_format($total_invoice, 2); ?>
        </p>

    </div>

    <div class="footer">
        <p>Thank you for your purchase!</p>
    </div>

</div>

<div class="print-button">

    <button
        class="print-btn"
        onclick="window.print()">
        Print Invoice
    </button>

    <button
        class="back-btn"
        onclick="window.location.href='sales.php'">
        Back to Manage Sales
    </button>

</div>

</body>
</html>