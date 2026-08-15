<?php
include 'connection.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    $supplier_name = $conn->real_escape_string($_POST['supplier_name']);
    $contact_number = $conn->real_escape_string($_POST['contact_number']);
    $address = $conn->real_escape_string($_POST['address']);
    $supply_date = $conn->real_escape_string($_POST['supply_date']);
    $supply_medicine = $conn->real_escape_string($_POST['supply_medicine']);
    $total_price = $conn->real_escape_string($_POST['total_price']);

    // Insert data into database
    $sql = "INSERT INTO suppliers (supplier_name, contact_number, address, supply_date, supply_medicine, total_price) 
            VALUES ('$supplier_name', '$contact_number', '$address', '$supply_date', '$supply_medicine', '$total_price')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Supplier added successfully!'); window.location='suppliers.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>
