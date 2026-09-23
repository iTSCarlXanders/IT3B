CREATE TABLE user_sessions (
    -- Session ID to be used in dashboard and references
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- User ID reference
    user_id INT NOT NULL,

    -- Session attributes
    session_start DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    session_end DATETIME DEFAULT NULL,
    session_duration INT DEFAULT NULL,
    
    -- Constraints and Foreign Key Implementation
     CONSTRAINT fk_user_sessions_user_id 
        FOREIGN KEY (user_id) 
        REFERENCES users(user_id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
