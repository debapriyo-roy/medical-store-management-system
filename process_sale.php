<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $medicine_name = $conn->real_escape_string($_POST['product_sold']);
    $quantity_sold = intval($_POST['quantity']);

    // Check current quantity
    $check = $conn->query("SELECT medicine_quantity FROM products WHERE medicine_name = '$medicine_name'");

    if ($check && $check->num_rows > 0) {
        $row = $check->fetch_assoc();
        $current_quantity = $row['medicine_quantity'];

        if ($quantity_sold <= $current_quantity) {
            $new_quantity = $current_quantity - $quantity_sold;

            $update = $conn->query("UPDATE products SET medicine_quantity = $new_quantity WHERE medicine_name = '$medicine_name'");

            if ($update) {
                echo "<script>alert('Sale processed. Quantity updated.'); window.location='sales.php';</script>";
            } else {
                echo "<script>alert('Error updating quantity.');</script>";
            }
        } else {
            echo "<script>alert('Not enough stock available!'); history.back();</script>";
        }
    } else {
        echo "<script>alert('Medicine not found in product list!'); history.back();</script>";
    }
}
?>
