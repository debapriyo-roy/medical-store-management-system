# Medical Store Management System

A web-based Medical Store Management System built with PHP and MySQL for managing medicines, inventory, suppliers, sales, and sales receipts.

## Overview

This project provides a simple web-based interface for managing day-to-day operations of a medical store. It includes product and stock management, supplier records, sales processing, invoice generation, and user registration/login.

## Features

- User registration and login
- Product management
  - Add medicines
  - Edit medicine details
  - Delete product batches
  - Search medicines
  - Track quantity, MFD, EXD, medicine type, and buying price
- FEFO-based medicine selection
  - Displays available stock according to earliest expiry
- Supplier management
  - Add supplier records
  - Edit supplier information
  - Delete supplier records
- Sales management
  - Sell one or multiple medicines in a single sale
  - Automatic stock deduction
  - Display actual/buying cost
  - Enter selling price
  - Calculate revenue, cost, and profit
  - Search sales history
  - Edit and delete sales
- Receipt / invoice generation
  - Combined receipt for multiple medicines sold in one transaction
  - Invoice ID generation
  - Total quantity and total invoice amount
  - Print-friendly invoice
- Dashboard and basic information pages

## Technologies Used

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- Bootstrap 5
- jQuery
- Select2
- XAMPP

## Project Structure

```text
medical-store-management-system/
│
├── aboutus.php
├── add_product.php
├── add_products.php
├── add_sales.php
├── add_suppliers.php
├── contact.php
├── core.php
├── dashboard.php
├── database_iv.sql
├── delete_product.php
├── delete_sale.php
├── delete_supplier.php
├── edit_sale.php
├── index.php
├── login.php
├── logout.php
├── process_sale.php
├── products.php
├── receipt.php
├── register.php
├── sales.php
├── suppliers.php
├── update_product.php
├── update_sale.php
├── update_supplier.php
│
├── connection.example.php
├── style.css
├── style_dashboard.css
├── style_login.css
│
└── images/
    ├── background.jpg
    └── login_img.jpg
```

## Requirements

- XAMPP
- Apache
- MySQL
- PHP
- A modern web browser

## Local Setup

1. Install and start **Apache** and **MySQL** from XAMPP.
2. Clone or download this repository into the XAMPP `htdocs` directory.
3. Create a MySQL database named:

```text
iv
```

4. Import the provided `database_iv.sql` file into the database.
5. Create your local `connection.php` using `connection.example.php` as a template.
6. Update the local database connection settings in `connection.php`.
7. Open the project in your browser:

```text
http://localhost/main/
```

> `connection.php` is intentionally excluded from version control because it contains local database credentials. Use `connection.example.php` as the configuration template.

## Important Notes

- This project is intended for local development and academic/project use.
- Do not commit real database passwords, API keys, or other secrets.
- Review and secure the application before using it in a production environment.

## Current Status

**Working project / active development**

The project currently includes product, supplier, sales, stock, authentication, and receipt functionality. Further UI improvements, validation, testing, and security hardening can be added as development continues.

## Author

**Debapriyo Roy**

GitHub: [@debapriyo-roy](https://github.com/debapriyo-roy)
