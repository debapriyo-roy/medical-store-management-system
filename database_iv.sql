create database iv;
use iv;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    phone VARCHAR(15) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

select * from users;

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

select * from messages;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_name VARCHAR(255) NOT NULL,
    medicine_quantity INT NOT NULL,
    mfd DATE NOT NULL,
    exd DATE NOT NULL,
    medicine_type VARCHAR(100) NOT NULL,
    sale_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

select * from products;

CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_name VARCHAR(255) NOT NULL,
    contact_number VARCHAR(15) NOT NULL,
    address TEXT NOT NULL,
    supply_date DATE NOT NULL,
    supply_medicine VARCHAR(255) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL
);


select * from suppliers;

CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    sold_medicine VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    sale_date DATE NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL
);

truncate table sales;

CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    contact VARCHAR(50)
);

select * from employees;

ALTER TABLE products ADD COLUMN cost_price DECIMAL(10, 2) NOT NULL;
ALTER TABLE products MODIFY cost_price DECIMAL(10,2) NOT NULL DEFAULT 0.00;