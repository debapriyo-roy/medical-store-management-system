<?php
// core.php handles both the session start AND the database connection!
require_once 'core.php';

/** @var mysqli $conn */ // <--- This tells VS Code that $conn exists!

// Fetch total counts
$totals = [
    'products' => 0,
    'suppliers' => 0,
    'sales' => 0,
    'employees' => 0,
    'profit' => 0
];

// SQL queries to get counts and profit dynamically
$sql_queries = [
    'products' => "SELECT COUNT(*) AS total FROM products",
    'suppliers' => "SELECT COUNT(*) AS total FROM suppliers",
    'sales' => "SELECT COUNT(*) AS total FROM sales",
    'employees' => "SELECT COUNT(*) AS total FROM employees",
    'profit' => "SELECT SUM(quantity * (unit_price - cost_price)) AS total_profit FROM sales"
];

// Fetch data
foreach ($sql_queries as $key => $query) {
    $result = $conn->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        if ($key == 'profit') {
            $totals[$key] = $row['total_profit']; // Store profit result
        } else {
            $totals[$key] = $row['total']; // Store other totals
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style_dashboard.css">
</head>
<body>

<div class="container">
    <!-- Sidebar Menu -->
    <div class="sidebar">
        <h3>Medicine Management</h3>
        <ul>
            <li><a href="products.php">Products</a></li>
            <li><a href="suppliers.php">Suppliers</a></li>
            
            <li><a href="sales.php">Sales</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2 class="dashboard-title">Dashboard</h2>
        <div class="stats" style="display:flex; justify-content: center;">
            
            <div class="stat-box" style="width: 300px;">Total Products: <span><?php echo $totals['products']; ?></span></div>
            <div class="stat-box" style="width: 300px;">Total Suppliers: <span><?php echo $totals['suppliers']; ?></span></div>
            
            <div class="stat-box" style="width: 300px;">Total Sales: <span><?php echo $totals['sales']; ?></span></div>
        </div>

    <!-- Dynamic Market Tracker -->
        <div class="financial-tracker">
            <h3>Net Profit / Loss</h3>
            <?php 
                // Determine if it's a profit or loss for dynamic styling
                $profit = $totals['profit'];
                $isProfit = $profit >= 0;
                $statusClass = $isProfit ? 'profit-positive' : 'profit-negative';
                $icon = $isProfit ? '▲' : '▼';
            ?>
            <div class="finance-display <?php echo $statusClass; ?>">
                <span class="trend-icon"><?php echo $icon; ?></span>
                <span class="amount">₹<?php echo number_format(abs($profit), 2); ?></span>
            </div>
            <p class="finance-subtitle">Live profit based on selling price minus buying cost</p>
        </div>

    </div>
</div>

</body>
</html>