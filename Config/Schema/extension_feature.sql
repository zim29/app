CREATE TABLE extensions_features (
    id INT AUTO_INCREMENT PRIMARY KEY,
    extension_id INT NOT NULL,
    feature_name VARCHAR(255) NOT NULL,
    feature_value VARCHAR(255) DEFAULT NULL,
    sort_order INT DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    INDEX idx_extension_id (extension_id),
    FOREIGN KEY (extension_id) REFERENCES extensions(id) ON DELETE CASCADE
);
