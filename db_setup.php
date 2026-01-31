<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'vehicle_parking';

try {
    // 1. Connect without database first
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>ParkEase System Setup</h2>";

    // 2. Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` COLLATE utf8mb4_general_ci");
    echo "✅ [SUCCESS] Database `$dbname` created or already exists.<br>";

    // 3. Connect to the database
    $pdo->exec("USE `$dbname` ");
    
    // Re-instantiate PDO with the database for future steps
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 4. Create Core Tables
    echo "Creating tables...<br>";

    $pdo->exec("CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        fullname VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS staff (
        staff_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        fullname VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        fullname VARCHAR(100),
        phone VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS parking_slots (
        slot_id INT AUTO_INCREMENT PRIMARY KEY,
        slot_number VARCHAR(10) NOT NULL UNIQUE,
        slot_type ENUM('Two-Wheeler', 'Four-Wheeler') DEFAULT 'Four-Wheeler',
        is_available BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

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

    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        two_wheeler_rate DECIMAL(10, 2) DEFAULT 20.00,
        four_wheeler_rate DECIMAL(10, 2) DEFAULT 50.00
    )");

    echo "✅ [SUCCESS] All tables created.<br>";

    // 5. Insert Initial Data
    $checkAdmin = $pdo->query("SELECT COUNT(*) FROM admin")->fetchColumn();
    if ($checkAdmin == 0) {
        $hash = password_hash('password', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO admin (username, password, fullname) VALUES (?, ?, ?)")->execute(['admin', $hash, 'Admin User']);
        echo "✅ [INFO] Default admin created (admin/password).<br>";
    }

    $checkStaff = $pdo->query("SELECT COUNT(*) FROM staff")->fetchColumn();
    if ($checkStaff == 0) {
        $hash = password_hash('staff123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO staff (username, password, fullname) VALUES (?, ?, ?)")->execute(['staff', $hash, 'General Staff']);
        echo "✅ [INFO] Default staff created (staff/staff123).<br>";
    }

    $checkSlots = $pdo->query("SELECT COUNT(*) FROM parking_slots")->fetchColumn();
    if ($checkSlots == 0) {
        $pdo->exec("INSERT INTO parking_slots (slot_number, slot_type) VALUES ('A1', 'Four-Wheeler'), ('A2', 'Four-Wheeler'), ('A3', 'Four-Wheeler'), ('B1', 'Two-Wheeler'), ('B2', 'Two-Wheeler')");
        echo "✅ [INFO] Initial parking slots added.<br>";
    }

    $checkSettings = $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();
    if ($checkSettings == 0) {
        $pdo->exec("INSERT INTO settings (two_wheeler_rate, four_wheeler_rate) VALUES (20.00, 50.00)");
        echo "✅ [INFO] Default rates configured.<br>";
    }

    echo "<br><div style='padding:20px; background:#e3fcef; color:#006644; border-radius:10px; border:1px solid #006644;'>
            <h3>Everything is Ready!</h3>
            <p>The database and all tables have been successfully initialized.</p>
            <a href='index.php' style='display:inline-block; padding:10px 20px; background:#006644; color:white; text-decoration:none; border-radius:5px;'>Launch Application</a>
          </div>";

} catch (PDOException $e) {
    echo "❌ [ERROR] " . $e->getMessage();
    if ($e->getCode() == 1049) {
        echo "<br><i>Note: Database doesn't exist. This script should have created it. Ensure your MySQL user has CREATE permissions.</i>";
    }
}
?>
