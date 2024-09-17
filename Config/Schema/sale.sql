CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    extension_id INT NOT NULL,
    order_id VARCHAR(255) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    date_added DATETIME NOT NULL,
    date_increase DATETIME DEFAULT NULL,
    buyer_email VARCHAR(255) DEFAULT NULL,
    buyer_username VARCHAR(255) DEFAULT NULL,
    system_version VARCHAR(255) DEFAULT NULL,
    marketplace VARCHAR(255) DEFAULT NULL,
    order_status VARCHAR(50) DEFAULT NULL,
    deleted TINYINT(1) DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    INDEX idx_extension_id (extension_id),
    INDEX idx_order_id (order_id),
    FOREIGN KEY (extension_id) REFERENCES extensions(id) ON DELETE CASCADE
);
