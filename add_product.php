<?php
// core.php handles BOTH the session security and the database connection
require_once 'core.php';

/** @var mysqli $conn */ // <--- This fixes the red lines in VS Code!

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $medicine_name = $_POST['medicine_name'];
    $medicine_quantity = (int)$_POST['medicine_quantity'];
    $mfd = $_POST['mfd'];
    $exd = $_POST['exd'];
    $medicine_type = $_POST['medicine_type'];
    $sale_price = $_POST['sale_price'];

    // 1. AUTO-CALCULATION: Find the latest supplier total price
    $cost_price = 0.00;

    $supplier_stmt = $conn->prepare("SELECT total_price FROM suppliers WHERE supply_medicine = ? ORDER BY id DESC LIMIT 1");
    $supplier_stmt->bind_param("s", $medicine_name);
    $supplier_stmt->execute();
    $supplier_result = $supplier_stmt->get_result();

    if ($row = $supplier_result->fetch_assoc()) {
        // CLEAN THE DATA: Remove any commas, ₹ symbols, or letters from the database value
        $clean_price = preg_replace('/[^0-9.]/', '', $row['total_price']);
        $total_supplier_price = (float)$clean_price;
        
        if ($medicine_quantity > 0) {
            $cost_price = $total_supplier_price / $medicine_quantity; 
        } else {
            die("SYSTEM ERROR: Medicine quantity is 0. Cannot divide by zero.");
        }
    } else {
        die("SYSTEM ERROR: Could not find '" . $medicine_name . "' in your suppliers table. The spelling must be 100% identical!");
    }
    $supplier_stmt->close();

    // FAILSAFE: Prevent a 0.00 from ever entering the database again
    if ($cost_price <= 0) {
        die("SYSTEM ERROR: The math failed. (Supplier Total: $total_supplier_price / Quantity: $medicine_quantity).");
    }

    // 2. INSERT PRODUCT
    $stmt = $conn->prepare("INSERT INTO products (medicine_name, medicine_quantity, mfd, exd, medicine_type, sale_price, cost_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssdd", $medicine_name, $medicine_quantity, $mfd, $exd, $medicine_type, $sale_price, $cost_price);

    if ($stmt->execute()) {
        header("Location: products.php?success=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>