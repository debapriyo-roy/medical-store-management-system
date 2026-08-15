<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $product_id = intval($_POST['product_id']);
    $medicine_name = $conn->real_escape_string($_POST['medicine_name']);
    $medicine_quantity = $conn->real_escape_string($_POST['medicine_quantity']);
    $mfd = $conn->real_escape_string($_POST['mfd']);
    $exd = $conn->real_escape_string($_POST['exd']);
    $medicine_type = $conn->real_escape_string($_POST['medicine_type']);
    $sale_price = $conn->real_escape_string($_POST['sale_price']);

    $sql = "UPDATE products SET 
            medicine_name='$medicine_name', 
            medicine_quantity='$medicine_quantity', 
            mfd='$mfd', 
            exd='$exd', 
            medicine_type='$medicine_type', 
            cost_price='$cost_price'
            WHERE id=$product_id";

    if ($conn->query($sql)) {
        echo "<script>alert('Product updated successfully!'); window.location.href='products.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "'); window.location.href='products.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request!'); window.location.href='products.php';</script>";
}
?>