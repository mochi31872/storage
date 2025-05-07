CREATE DATABASE ticketdb;
USE ticketdb;

CREATE TABLE ticket_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100),
    ticket_type VARCHAR(50),
    age_group VARCHAR(10),
    quantity INT,
    price INT,
    order_time DATETIME DEFAULT CURRENT_TIMESTAMP
);
