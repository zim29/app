CREATE TABLE changelogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_extension INT NOT NULL,
    description TEXT NOT NULL,
    deleted TINYINT(1) DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    INDEX idx_id_extension (id_extension),
    FOREIGN KEY (id_extension) REFERENCES extensions(id) ON DELETE CASCADE
);
