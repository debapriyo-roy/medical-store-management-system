<?php
// about.php - Professional About Page for Inventory Management System

session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Medicine Management System</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        header {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
        }
        main {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        section {
            margin-bottom: 20px;
        }
        h2 {
            color: #0056b3;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 5px;
        }
        ul {
            list-style-type: square;
            padding-left: 20px;
        }
        footer {
            text-align: center;
            background: #007bff;
            color: white;
            padding: 10px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>
<body>
    <header>Medicine Management System</header>
    <main>
        <section>
            <h2>Overview</h2>
            <p>The Medicine Management System is a powerful and user-friendly solution designed to help businesses efficiently manage and track medicine, optimize operations, and improve profitability.</p>
        </section>
        <section>
            <h2>Key Features</h2>
            <ul>
                <li>Real-time stock monitoring and alerts</li>
                <li>Comprehensive supplier and customer management</li>
                <li>Detailed sales and purchase tracking</li>
                <li>Automated reports and analytics</li>
                <li>Secure role-based user access</li>
                <li>Cloud backup and seamless data integration</li>
            </ul>
        </section>
        <section>
            <h2>Meet the Team</h2>
            <p>Developed by a skilled team of professionals—Sayan, Sourav, Debapriyo, Subhendu, Nibedita, and Taniya—our mission is to deliver innovative and reliable medicine management solutions to businesses.</p>
        </section>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Medicine Management System. All rights reserved.</p>
    </footer>
</body>
</html>
