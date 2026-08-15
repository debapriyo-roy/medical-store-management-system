<?php
include 'connection.php';
$result = $conn->query("SELECT * FROM suppliers");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Suppliers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <h2 class="mb-4">Manage Suppliers</h2>

    <form action="add_suppliers.php" method="post" class="mb-4">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Supplier Name:</label>
                <input type="text" name="supplier_name" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>Contact Number:</label>
                <input type="text" name="contact_number" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>Address:</label>
                <input type="text" name="address" class="form-control" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Supply Date:</label>
                <input type="date" name="supply_date" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>Supply Medicine:</label>
                <input type="text" name="supply_medicine" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>Total Price:</label>
                <input type="number" name="total_price" class="form-control" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-primary mt-4">Add Supplier</button>
            </div>
            <div class="col-md-3 d-grid">
                <button
                    type="button"
                    class="btn btn-primary mt-4"
                    onclick="window.location.href='dashboard.php'">
                    Exit
                </button>
            </div>
        </div>
    </form>

    <h3>Supplier List</h3>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Supplier Name</th>
                <th>Contact Number</th>
                <th>Address</th>
                <th>Supply Date</th>
                <th>Supply Medicine</th>
                <th>Total Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['supplier_name']); ?></td>
                <td><?= htmlspecialchars($row['contact_number']); ?></td>
                <td><?= htmlspecialchars($row['address']); ?></td>
                <td><?= $row['supply_date']; ?></td>
                <td><?= htmlspecialchars($row['supply_medicine']); ?></td>
                <td>₹ <?= number_format($row['total_price'], 2); ?></td>

                <td>
                    <button
                        type="button"
                        class="btn btn-warning btn-sm"
                        onclick="editSupplier(<?= htmlspecialchars(json_encode($row)); ?>)">
                        Edit
                    </button>

                    <button
                        type="button"
                        class="btn btn-danger btn-sm"
                        onclick="deleteSupplier(<?= (int)$row['id']; ?>)">
                        Delete
                    </button>
                </td>

            </tr>
            <?php } ?>
        </tbody>
    </table>


<!-- Edit Supplier Modal -->
<div class="modal" id="editSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="update_supplier.php" method="post">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Supplier</h5>

                    <button
                        type="button"
                        class="btn-close"
                        onclick="closeSupplierModal()">
                    </button>
                </div>

                <div class="modal-body">

                    <input
                        type="hidden"
                        name="supplier_id"
                        id="edit_supplier_id">

                    <div class="mb-3">
                        <label>Supplier Name:</label>
                        <input
                            type="text"
                            name="supplier_name"
                            id="edit_supplier_name"
                            class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Contact Number:</label>
                        <input
                            type="text"
                            name="contact_number"
                            id="edit_contact_number"
                            class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Address:</label>
                        <input
                            type="text"
                            name="address"
                            id="edit_address"
                            class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Supply Date:</label>
                        <input
                            type="date"
                            name="supply_date"
                            id="edit_supply_date"
                            class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Supply Medicine:</label>
                        <input
                            type="text"
                            name="supply_medicine"
                            id="edit_supply_medicine"
                            class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Total Price:</label>
                        <input
                            type="number"
                            name="total_price"
                            id="edit_total_price"
                            class="form-control"
                            step="0.01"
                            min="0"
                            required>
                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="submit"
                        class="btn btn-success">
                        Update
                    </button>

                    <button
                        type="button"
                        class="btn btn-secondary"
                        onclick="closeSupplierModal()">
                        Cancel
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>


<script>

function editSupplier(supplier) {

    document.getElementById("edit_supplier_id").value =
        supplier.id;

    document.getElementById("edit_supplier_name").value =
        supplier.supplier_name;

    document.getElementById("edit_contact_number").value =
        supplier.contact_number;

    document.getElementById("edit_address").value =
        supplier.address;

    document.getElementById("edit_supply_date").value =
        supplier.supply_date;

    document.getElementById("edit_supply_medicine").value =
        supplier.supply_medicine;

    document.getElementById("edit_total_price").value =
        supplier.total_price;

    document.getElementById("editSupplierModal").style.display =
        "block";
}

function closeSupplierModal() {
    document.getElementById("editSupplierModal").style.display =
        "none";
}

function deleteSupplier(supplierId) {

    if (confirm("Are you sure you want to delete this supplier record?")) {
        window.location.href =
            "delete_supplier.php?id=" + supplierId;
    }

}

</script>


</body>
</html>
