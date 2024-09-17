CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    license_id VARCHAR(255) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    payed_date DATETIME DEFAULT NULL,
    state VARCHAR(50) DEFAULT 'Pending',
    type VARCHAR(50) DEFAULT NULL,
    deleted TINYINT(1) DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    INDEX idx_license_id (license_id)
);
