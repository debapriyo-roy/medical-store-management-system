<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $medicine_name = $_POST['medicine_name'];
    $mfd = $_POST['mfd'];
    $exd = $_POST['exd'];
    $medi_type = $_POST['medi_type'];
    $sale_price = $_POST['sale_price'];

    $stmt = $conn->prepare("INSERT INTO products (medicine_name, mfd, exd, medi_type, sale_price) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssd", $medicine_name, $mfd, $exd, $medi_type, $sale_price);

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
