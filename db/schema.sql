CREATE TABLE IF NOT EXISTS activity_logs(
    activity_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50),
    user_email VARCHAR(50),
    activity_log_action VARCHAR(50) NOT NULL,
    activity_log_status ENUM('success', 'failed') DEFAULT 'success',

    --client Parameters
    activity_log_ip_address VARCHAR(45),
    activity_log_user_agent VARCHAR(255),
    
    --Timestamps
    activity_log_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);