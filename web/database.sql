CREATE TABLE patients (
                          token_id INT AUTO_INCREMENT PRIMARY KEY,
                          patient_name VARCHAR(100),
                          role VARCHAR(20),
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
