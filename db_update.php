<?php
require_once 'includes/db_connect.php';

try {
    echo "<h2>ParkEase Database Update</h2>";

    // 0. Base Tables
    $pdo->exec("CREATE TABLE IF NOT EXISTS parking_slots (
        slot_id INT AUTO_INCREMENT PRIMARY KEY,
        slot_number VARCHAR(10) NOT NULL UNIQUE,
        slot_type ENUM('Two-Wheeler', 'Four-Wheeler') DEFAULT 'Four-Wheeler',
        is_available BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        two_wheeler_rate DECIMAL(10, 2) DEFAULT 20.00,
        four_wheeler_rate DECIMAL(10, 2) DEFAULT 50.00
    )");

    // Initial Slots if empty
    $checkSlots = $pdo->query("SELECT COUNT(*) FROM parking_slots")->fetchColumn();
    if ($checkSlots == 0) {
        $pdo->exec("INSERT INTO parking_slots (slot_number, slot_type) VALUES ('A1', 'Four-Wheeler'), ('A2', 'Four-Wheeler'), ('A3', 'Four-Wheeler'), ('B1', 'Two-Wheeler'), ('B2', 'Two-Wheeler')");
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        fullname VARCHAR(100),
        phone VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Vehicles table
    $pdo->exec("CREATE TABLE IF NOT EXISTS vehicles (
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
    )");

    echo "✅ [SUCCESS] Core tables (slots, settings, users, vehicles) checked/created.<br>";
    echo "✅ [SUCCESS] Users table checked/created.<br>";

    // 2. Create staff table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS staff (
        staff_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        fullname VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Add default staff if none exist
    $checkStaff = $pdo->query("SELECT COUNT(*) FROM staff")->fetchColumn();
    if ($checkStaff == 0) {
        $pass = password_hash('staff123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO staff (username, password, fullname) VALUES ('staff', '$pass', 'General Staff')");
    }
    echo "✅ [SUCCESS] Staff table checked/created (Default: staff/staff123).<br>";

    // 3. Create admin table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        fullname VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Add default admin if none exist
    $checkAdmin = $pdo->query("SELECT COUNT(*) FROM admin")->fetchColumn();
    if ($checkAdmin == 0) {
        $passAdmin = password_hash('password', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO admin (username, password, fullname) VALUES ('admin', '$passAdmin', 'Admin User')");
        echo "✅ [SUCCESS] Admin user created (admin / password).<br>";
    }

    // 4. Add user_id column...
    $columnCheck = $pdo->query("SHOW COLUMNS FROM vehicles LIKE 'user_id'")->fetch();
    if (!$columnCheck) {
        $pdo->exec("ALTER TABLE vehicles ADD user_id INT NULL AFTER slot_id");
        $pdo->exec("ALTER TABLE vehicles ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users(user_id)");
        echo "✅ [SUCCESS] user_id column added to vehicles table.<br>";
    } else {
        echo "ℹ️ [INFO] user_id column already exists in vehicles.<br>";
    }

    echo "<br><div style='padding:10px; background:#d4edda; color:#155724; border-radius:5px;'>
            Update Complete! You can now use the User Module.
          </div>";
    echo "<br><a href='index.php'>Go to Home</a>";

} catch (PDOException $e) {
    echo "❌ [ERROR] " . $e->getMessage();
}
?>
