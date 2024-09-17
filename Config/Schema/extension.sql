CREATE TABLE extensions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    system VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) DEFAULT NULL,
    seo_url VARCHAR(255) DEFAULT NULL,
    features TEXT DEFAULT NULL,
    deleted TINYINT(1) DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    INDEX idx_name (name),
    INDEX idx_system (system)
);
