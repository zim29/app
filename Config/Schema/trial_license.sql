CREATE TABLE trial_licenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    license_id VARCHAR(255) NOT NULL,
    extension_id INT NOT NULL,
    extension_name VARCHAR(255) NOT NULL,
    domain VARCHAR(255) NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    activated TINYINT(1) DEFAULT 0,
    form_recovered INT DEFAULT 0,
    oc_version VARCHAR(255) DEFAULT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    INDEX idx_license_id (license_id),
    INDEX idx_extension_id (extension_id),
    INDEX idx_domain (domain),
    FOREIGN KEY (extension_id) REFERENCES extensions(id) ON DELETE CASCADE
);
