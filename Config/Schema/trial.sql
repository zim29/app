CREATE TABLE trials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    extension_id INT NOT NULL,
    domain VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    activated TINYINT(1) DEFAULT 0,
    form_recovered INT DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    INDEX idx_extension_id (extension_id),
    INDEX idx_domain (domain),
    FOREIGN KEY (extension_id) REFERENCES extensions(id) ON DELETE CASCADE
);
