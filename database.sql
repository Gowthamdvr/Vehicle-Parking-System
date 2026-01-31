CREATE DATABASE IF NOT EXISTS vehicle_parking;
USE vehicle_parking;

CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS staff (
    staff_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS parking_slots (
    slot_id INT AUTO_INCREMENT PRIMARY KEY,
    slot_number VARCHAR(10) NOT NULL UNIQUE,
    slot_type ENUM('Two-Wheeler', 'Four-Wheeler') DEFAULT 'Four-Wheeler',
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS vehicles (
    vehicle_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_number VARCHAR(20) NOT NULL,
    owner_name VARCHAR(100),
    vehicle_type ENUM('Two-Wheeler', 'Four-Wheeler') DEFAULT 'Four-Wheeler',
    entry_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    exit_time DATETIME NULL,
    slot_id INT,
    user_id INT NULL,
    amount DECIMAL(10, 2) DEFAULT 0.00,
    status ENUM('Parked', 'Exited') DEFAULT 'Parked',
    FOREIGN KEY (slot_id) REFERENCES parking_slots(slot_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    two_wheeler_rate DECIMAL(10, 2) DEFAULT 20.00,
    four_wheeler_rate DECIMAL(10, 2) DEFAULT 50.00
);

-- Initial Data
INSERT INTO admin (username, password, fullname) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User'); -- password is 'password'

INSERT INTO settings (two_wheeler_rate, four_wheeler_rate) VALUES (20.00, 50.00);

-- Initial Slots
INSERT INTO parking_slots (slot_number, slot_type) VALUES ('A1', 'Four-Wheeler'), ('A2', 'Four-Wheeler'), ('A3', 'Four-Wheeler'), ('B1', 'Two-Wheeler'), ('B2', 'Two-Wheeler');
