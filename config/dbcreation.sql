CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            full_name VARCHAR(255) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('user','manager','admin') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;

        CREATE TABLE IF NOT EXISTS cars (
            id INT AUTO_INCREMENT PRIMARY KEY,
            make VARCHAR(100) NOT NULL,
            model VARCHAR(100) NOT NULL,
            transmission ENUM('механика','автомат') NOT NULL,
            fuel ENUM('бензин','дизель','электро','гибрид') NOT NULL,
            seats TINYINT NOT NULL,
            daily_price DECIMAL(10,2) NOT NULL,
            image_url VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (make, model, transmission, fuel, seats, daily_price)
        ) ENGINE=InnoDB;