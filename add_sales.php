<?php
// Session security + database connection
require_once 'core.php';

/** @var mysqli $conn */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: sales.php");
    exit();
}

$customer_name = trim($_POST['customer_name'] ?? '');
$sold_medicines = $_POST['sold_medicine'] ?? [];
$quantities = $_POST['quantity'] ?? [];
$unit_prices = $_POST['unit_price'] ?? [];
$sale_date = $_POST['sale_date'] ?? '';

// Generate one invoice ID for this complete sale transaction
$invoice_id = 'INV-' . date('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(3)));

if ($customer_name === '' || empty($sold_medicines) || empty($quantities) || $sale_date === '') {
    die("SYSTEM ERROR: Required sale information is missing.");
}

if (
    count($sold_medicines) !== count($quantities) ||
    count($sold_medicines) !== count($unit_prices)
) {
    die("SYSTEM ERROR: Medicine, quantity and selling price data do not match.");
}

$conn->begin_transaction();

try {

    // Update stock
    $update_stmt = $conn->prepare("
        UPDATE products
        SET medicine_quantity = medicine_quantity - ?
        WHERE id = ?
          AND medicine_quantity >= ?
    ");

    // Insert completed sale
    $insert_sale_stmt = $conn->prepare("
        INSERT INTO sales
        (invoice_id, customer_name, sold_medicine, quantity, unit_price, cost_price, sale_date)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    for ($i = 0; $i < count($sold_medicines); $i++) {

        $medicine_name = trim($sold_medicines[$i]);
        $quantity_needed = (int)$quantities[$i];

        if ($medicine_name === '') {
            throw new Exception("Medicine name is missing.");
        }

        if ($quantity_needed <= 0) {
            throw new Exception("Quantity must be greater than 0 for $medicine_name.");
        }

        /*
        * Get available batches in FEFO order.
        *
        * cost_price = YOUR buying cost from supplier
        * unit_price = SELLING PRICE entered on the Sales page
        */
        
        $batch_stmt = $conn->prepare("
            SELECT
                id,
                medicine_quantity,
                cost_price,
                exd
            FROM products
            WHERE medicine_name = ?
              AND medicine_quantity > 0
            ORDER BY exd ASC, id ASC
        ");

        $batch_stmt->bind_param("s", $medicine_name);
        $batch_stmt->execute();
        $batch_result = $batch_stmt->get_result();

        $batches = [];
        $total_available = 0;

        while ($batch = $batch_result->fetch_assoc()) {
            $batches[] = $batch;
            $total_available += (int)$batch['medicine_quantity'];
        }

        $batch_stmt->close();

        if ($total_available < $quantity_needed) {
            throw new Exception(
                "Not enough stock for $medicine_name. " .
                "Available: $total_available, Requested: $quantity_needed"
            );
        }

        $remaining = $quantity_needed;

        /*
         * Sell from the earliest-expiring batch first.
         */
        foreach ($batches as $batch) {

            if ($remaining <= 0) {
                break;
            }

            $batch_id = (int)$batch['id'];
            $available = (int)$batch['medicine_quantity'];

            // YOUR purchase cost per unit
            $cost_price = (float)$batch['cost_price'];

            // YOUR selling price per unit
            $unit_price = (float)($_POST['unit_price'][$i] ?? 0);

            if ($cost_price < 0 || $unit_price <= 0) {
                throw new Exception("Invalid price found for $medicine_name.");
            }

            $deduct = min($remaining, $available);

            // Reduce stock
            $update_stmt->bind_param(
                "iii",
                $deduct,
                $batch_id,
                $deduct
            );

            if (!$update_stmt->execute() || $update_stmt->affected_rows !== 1) {
                throw new Exception("Failed to update stock for $medicine_name.");
            }

            // Record sale using prices from PRODUCTS table
            $insert_sale_stmt->bind_param(
                "sssidds",
                $invoice_id,
                $customer_name,
                $medicine_name,
                $deduct,
                $unit_price,
                $cost_price,
                $sale_date
            );

            if (!$insert_sale_stmt->execute()) {
                throw new Exception("Failed to record sale for $medicine_name.");
            }

            $remaining -= $deduct;
        }
    }

    $update_stmt->close();
    $insert_sale_stmt->close();

    $conn->commit();

    echo "<script>
        alert('Sale added successfully.');
        window.location.href='sales.php';
    </script>";
    exit();

} catch (Throwable $e) {

    $conn->rollback();

    echo "<script>
        alert(" . json_encode("Sale failed: " . $e->getMessage()) . ");
        window.location.href='sales.php';
    </script>";
    exit();
}
?>