<?php
include 'connection.php';

// Handle Adding a New Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $medicine_name = $conn->real_escape_string($_POST['medicine_name']);
    $medicine_quantity = intval($_POST['medicine_quantity']);
    $mfd = $conn->real_escape_string($_POST['mfd']);
    $exd = $conn->real_escape_string($_POST['exd']);
    $medicine_type = $conn->real_escape_string($_POST['medicine_type']);
    $cost_price = floatval($_POST['cost_price']);

    $sql = "INSERT INTO products (medicine_name, medicine_quantity, mfd, exd, medicine_type, cost_price) 
        VALUES ('$medicine_name', '$medicine_quantity', '$mfd', '$exd', '$medicine_type', '$cost_price')";

    if ($conn->query($sql)) {
        header("Location: products.php?success=1");
        exit();
    } else {
        echo "<script>alert(" . json_encode("Error: " . $conn->error) . ");</script>";
    }

}

// Handle Updating a Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {

    $product_id = intval($_POST['product_id']);
    $medicine_name = $conn->real_escape_string($_POST['medicine_name']);
    $mfd = $conn->real_escape_string($_POST['mfd']);
    $exd = $conn->real_escape_string($_POST['exd']);
    $medicine_type = $conn->real_escape_string($_POST['medicine_type']);
    $cost_price = floatval($_POST['cost_price']);

    $sql = "UPDATE products SET
            medicine_name='$medicine_name',
            mfd='$mfd',
            exd='$exd',
            medicine_type='$medicine_type',
            cost_price='$cost_price'
            WHERE id=$product_id";


    if ($conn->query($sql)) {
        header("Location: products.php?updated=1");
        exit();
    } else {
        echo "<script>alert(" . json_encode("Error: " . $conn->error) . ");</script>";
    }

}

// Handle Searching
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$result = $conn->query("SELECT * FROM products WHERE medicine_name LIKE '%$search_query%' ORDER BY exd ASC");
?>


<?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
<script>
    alert('Product added successfully!');
</script>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="mb-4">Manage Products</h2>

    <!-- Add Product Form -->
    <form action="" method="post" class="mb-4">
        <input type="hidden" name="add_product" value="1">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Medicine Name:</label>
                <input type="text" name="medicine_name" class="form-control" required>
            </div>
            <div class="col-md-3 mb-3">
                <label>Medicine Quantity:</label>
                <input type="number" name="medicine_quantity" class="form-control" required>
            </div>
            <div class="col-md-3 mb-3">

                <label>Buying Price:</label>
                    <input type="number"
                        step="0.01"
                        min="0.01"
                        name="cost_price"
                        id="cost_price"
                        class="form-control"
                    required>

            </div>
            <div class="col-md-3 mb-3">
                <label>MFD:</label>
                <input type="date" name="mfd" class="form-control" required>
            </div>
            <div class="col-md-3 mb-3">
                <label>EXD:</label>
                <input type="date" name="exd" class="form-control" required>
            </div>
            <div class="col-md-3 mb-3">
                <label>Medicine Type:</label>
                <input type="text" name="medicine_type" class="form-control" required>
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-primary mt-4">Add Product</button>
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

    <!-- Search Form -->
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by Medicine Name" value="<?= htmlspecialchars($search_query); ?>">
            <button type="submit" class="btn btn-outline-primary">Search</button>
        </div>
    </form>

    <!-- Product List -->
    <h3>Product List (Soonest Expiry First)</h3>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Medicine Name</th>
                <th>Quantity</th>
                <th>MFD</th>
                <th>EXD</th>
                <th>Type</th>
                <th>Buying Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['medicine_name']); ?></td>
                    <td><?= $row['medicine_quantity']; ?></td>
                    <td><?= $row['mfd']; ?></td>
                    <td><?= $row['exd']; ?></td>
                    <td><?= htmlspecialchars($row['medicine_type']); ?></td>
                    <td>₹ <?= number_format($row['cost_price'], 2); ?></td>
                    <td>
                        <button
                            type="button"
                            onclick="editProduct(<?= htmlspecialchars(json_encode($row)); ?>)"
                            class="btn btn-warning btn-sm">
                            Edit
                        </button>

                        <button
                            type="button"
                            onclick="deleteProduct(<?= (int)$row['id']; ?>)"
                            class="btn btn-danger btn-sm">
                            Delete
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Edit Product Modal -->
    <div class="modal" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Product</h5>
                        <button type="button" class="btn-close" onclick="closeModal()"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="update_product" value="1">
                        <input type="hidden" name="product_id" id="edit_id">
                        <label>Medicine Name:</label>
                        <input type="text" name="medicine_name" id="edit_name" class="form-control" required>
                        <label>Quantity:</label>
                        <input type="number" id="edit_quantity" class="form-control" readonly>
                        <label>MFD:</label>
                        <input type="date" name="mfd" id="edit_mfd" class="form-control" required>
                        <label>EXD:</label>
                        <input type="date" name="exd" id="edit_exd" class="form-control" required>
                        <label>Medicine Type:</label>
                        <input type="text" name="medicine_type" id="edit_type" class="form-control" required>
                        
                        <label>Buying Price:</label>
                            <input type="number"
                                step="0.01"
                                min="0.01"
                                name="cost_price"
                                id="edit_cost_price"
                                class="form-control"
                            required>
                    
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>

        function deleteProduct(productId) {
            if (confirm("Are you sure you want to delete this product batch?")) {
                window.location.href = "delete_product.php?id=" + productId;
            }
        }

    function editProduct(product) {
        document.getElementById("edit_id").value = product.id;
        document.getElementById("edit_name").value = product.medicine_name;
        document.getElementById("edit_quantity").value = product.medicine_quantity;
        document.getElementById("edit_mfd").value = product.mfd;
        document.getElementById("edit_exd").value = product.exd;
        document.getElementById("edit_type").value = product.medicine_type;
        document.getElementById("edit_cost_price").value = product.cost_price;
        document.getElementById("editModal").style.display = "block";
    }

    function closeModal() {
        document.getElementById("editModal").style.display = "none";
    }
</script>

</body>
</html>
