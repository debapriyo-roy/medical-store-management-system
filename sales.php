<?php
include 'connection.php';

// Get total quantity per medicine
$medicines = $conn->query("
    SELECT
        p.medicine_name,
        p.cost_price,
        totals.total_qty
    FROM products p
    INNER JOIN (
        SELECT
            p1.medicine_name,
            p1.id AS fefo_id,
            SUM(p2.medicine_quantity) AS total_qty
        FROM products p1
        INNER JOIN products p2
            ON p2.medicine_name = p1.medicine_name
           AND p2.medicine_quantity > 0
        WHERE p1.medicine_quantity > 0
          AND NOT EXISTS (
              SELECT 1
              FROM products p3
              WHERE p3.medicine_name = p1.medicine_name
                AND p3.medicine_quantity > 0
                AND (
                    p3.exd < p1.exd
                    OR (p3.exd = p1.exd AND p3.id < p1.id)
                )
          )
        GROUP BY p1.medicine_name, p1.id
    ) totals
        ON p.id = totals.fefo_id
    ORDER BY p.medicine_name ASC
");

// Get sales history
$sales_search = trim($_GET['sales_search'] ?? '');

$search_pattern = '%' . $sales_search . '%';

$sales_stmt = $conn->prepare("
    SELECT
        id,
        invoice_id,
        customer_name,
        sold_medicine,
        quantity,
        unit_price,
        cost_price,
        sale_date
    FROM sales
    WHERE customer_name LIKE ?
       OR sold_medicine LIKE ?
    ORDER BY sale_date DESC, id DESC
");

$sales_stmt->bind_param("ss", $search_pattern, $search_pattern);
$sales_stmt->execute();

$sales_history = $sales_stmt->get_result();



$total_stmt = $conn->prepare("
    SELECT
        COALESCE(SUM(quantity), 0) AS total_quantity,
        COALESCE(SUM(quantity * unit_price), 0) AS total_revenue,
        COALESCE(SUM(quantity * cost_price), 0) AS total_cost,
        COALESCE(SUM(quantity * (unit_price - cost_price)), 0) AS total_profit
    FROM sales
    WHERE customer_name LIKE ?
       OR sold_medicine LIKE ?
");

$total_stmt->bind_param("ss", $search_pattern, $search_pattern);
$total_stmt->execute();

$total_result = $total_stmt->get_result();
$totals = $total_result->fetch_assoc();

$total_quantity = (int)$totals['total_quantity'];
$total_revenue = (float)$totals['total_revenue'];
$total_cost = (float)$totals['total_cost'];
$total_profit = (float)$totals['total_profit'];

$total_stmt->close();



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Sales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body class="container mt-5">

<h2 class="mb-4">Manage Sales</h2>

<form action="add_sales.php" method="post" class="mb-4">
    <div class="mb-3">
        <label>Customer Name:</label>
        <input type="text" name="customer_name" class="form-control" required>
    </div>

    <div id="medicines-container">
        <div class="row medicine-row">
            <div class="col-md-4 mb-3">
                <label>Medicine:</label>
                    

        <select name="sold_medicine[]"
            class="form-select medicine-select"
                required>

                <option value="">-- Search & Select Medicine --</option>

                    <?php while ($row = $medicines->fetch_assoc()): ?>
                <option
                    value="<?= htmlspecialchars($row['medicine_name']) ?>"
                    data-cost="<?= $row['cost_price']; ?>">
                    <?= htmlspecialchars($row['medicine_name']) ?>
                    (Available: <?= $row['total_qty'] ?>)
                </option>
            <?php endwhile; ?>

        </select>
            </div>

            <div class="col-md-2 mb-3">
    <label>Quantity:</label>
    <input type="number" name="quantity[]" class="form-control" required>
</div>

<div class="col-md-2 mb-3">
    <label>Actual Cost:</label>
    <input type="text"
        class="form-control actual-cost"
        readonly
        placeholder="₹ 0.00">
</div>

<div class="col-md-2 mb-3">
    <label>Selling Price:</label>
    <input type="number"
        step="0.01"
        name="unit_price[]"
        class="form-control unit-price"
        min="0.01"
        required>
</div>
            <div class="col-md-2 d-grid">
                <button type="button" class="btn btn-danger mt-4" onclick="removeMedicineRow(this)">Remove</button>
            </div>
        </div>
    </div>

    <button type="button" class="btn btn-secondary mb-3" onclick="addMedicineRow()">+ Add Medicine</button>

    <div class="mb-3">
        <label>Sale Date:</label>
        <input type="date" name="sale_date" class="form-control" required>
    </div>



    <div class="row">
        <div class="col-md-3 d-grid">
            <button type="submit" class="btn btn-primary mt-2">Add Sale</button>
        </div>
        <div class="col-md-3 d-grid">
            <button
                type="button"
                class="btn btn-primary mt-2"
                onclick="window.location.href='dashboard.php'">
                Exit
            </button>
        </div>
    </div>
</form>


<form method="GET" class="mb-3">
    <div class="input-group">
        <input
            type="text"
            name="sales_search"
            class="form-control"
            placeholder="Search by Customer Name or Medicine"
            value="<?= htmlspecialchars($_GET['sales_search'] ?? ''); ?>">

        <button type="submit" class="btn btn-outline-primary">
            Search
        </button>
    </div>
</form>



<div class="row mb-4">

    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6>Total Quantity</h6>
                <h4><?= number_format($total_quantity); ?></h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6>Total Revenue</h6>
                <h4>₹ <?= number_format($total_revenue, 2); ?></h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6>Total Cost</h6>
                <h4>₹ <?= number_format($total_cost, 2); ?></h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6>Total Profit</h6>
                <h4>₹ <?= number_format($total_profit, 2); ?></h4>
            </div>
        </div>
    </div>

</div>



<h3 class="mt-4">Sales History</h3>

<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Customer Name</th>
            <th>Medicine</th>
            <th>Quantity</th>
            <th>Selling Price</th>
            <th>Buying Price</th>
            <th>Profit</th>
            <th>Sale Date</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>



        <?php while ($sale = $sales_history->fetch_assoc()): ?>
    <?php
        $revenue = $sale['unit_price'] * $sale['quantity'];
        $cost = $sale['cost_price'] * $sale['quantity'];
        $profit = $revenue - $cost;
    ?>

            

            <tr>
                <td><?= $sale['id']; ?></td>
                <td><?= htmlspecialchars($sale['customer_name']); ?></td>
                <td><?= htmlspecialchars($sale['sold_medicine']); ?></td>
                <td><?= $sale['quantity']; ?></td>
                <td>₹ <?= number_format($sale['unit_price'], 2); ?></td>
                <td>₹ <?= number_format($sale['cost_price'], 2); ?></td>
                <td>₹ <?= number_format($profit, 2); ?></td>
                <td><?= $sale['sale_date']; ?></td>
               <td>
                    <button
                        type="button"
                        class="btn btn-warning btn-sm"
                        onclick="editSale(<?= $sale['id']; ?>)">
                        Edit
                    </button>

                    <button
                        type="button"
                        class="btn btn-danger btn-sm"
                        onclick="deleteSale(<?= $sale['id']; ?>)">
                        Delete
                    </button>

            <a
                href="./receipt.php?invoice_id=<?= urlencode($sale['invoice_id']); ?>"
                class="btn btn-success btn-sm">
                Receipt
            </a>               
                    
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>



<?php
$sales_stmt->close();
?>



<!-- Select2 & jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>


function addMedicineRow() {
    const container = document.getElementById('medicines-container');
    const row = document.querySelector('.medicine-row');

    // Destroy Select2 before cloning
    $(row).find('.medicine-select').select2('destroy');

    // Clone clean row
    const clone = row.cloneNode(true);

    // Clear all inputs
    clone.querySelectorAll('input').forEach(input => {
        input.value = '';
    });

    // Reset medicine selection
    clone.querySelector('.medicine-select').value = '';

    // Add cloned row
    container.appendChild(clone);

    // Reinitialize Select2 on original row
    $(row).find('.medicine-select').select2({
        width: '100%'
    });

    // Initialize Select2 on new row
    $(clone).find('.medicine-select').select2({
        width: '100%'
    });
}


function removeMedicineRow(button) {
    const row = button.closest('.medicine-row');
    const container = document.getElementById('medicines-container');

    if (container.querySelectorAll('.medicine-row').length > 1) {

        // Destroy Select2 before removing the row
        $(row).find('.medicine-select').select2('destroy');

        // Remove the row
        row.remove();

    } else {
        alert("At least one medicine is required.");
    }
}


function deleteSale(saleId) {
    if (confirm("Are you sure you want to delete this sale?")) {
        window.location.href = "delete_sale.php?id=" + saleId;
    }
}

function editSale(saleId) {
    window.location.href = "edit_sale.php?id=" + saleId;
}


// Initialize Select2
$(document).ready(function() {

    $('.medicine-select').select2({ width: '100%' });

    $(document).on('change', '.medicine-select', function() {

        const selectedOption = $(this).find(':selected');
        const actualCost = selectedOption.data('cost');

        const row = $(this).closest('.medicine-row');

        if (actualCost !== undefined && actualCost !== '') {
            row.find('.actual-cost').val('₹ ' + parseFloat(actualCost).toFixed(2));
        } else {
            row.find('.actual-cost').val('');
        }
    });

});

</script>

</body>
</html>
