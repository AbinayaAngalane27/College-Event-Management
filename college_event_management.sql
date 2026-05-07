
CREATE DATABASE IF NOT EXISTS college_event_management;
USE college_event_management;

CREATE TABLE IF NOT EXISTS event_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    img_path VARCHAR(255) NOT NULL,
    description TEXT,
    page_link VARCHAR(255)
);

INSERT INTO event_categories (title, img_path, description, page_link) VALUES
('Technical Events', 'img/technical.png', 'Participate in exciting technical events!', 'technical-events.php'),
('Cultural Events',  'img/cultural.png',  'Showcase your talents in cultural events!', 'cultural-events.php'),
('Sports Events',    'img/sports.png',     'Join thrilling sports events!', 'sports-events.php'),
('Gaming Events',    'img/gaming.png',     'Compete in the latest gaming events!', 'gaming-events.php'),
('Literary Events',  'img/literary.png',   'Express yourself in literary events!', 'literary-events.php');

-- -------------------------------------------------------
-- admins  (FIX: added visit_count column)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    visit_count INT DEFAULT 0
);

-- -------------------------------------------------------
-- events  (FIX: category_id defined ONCE in CREATE TABLE,
--          removed duplicate ALTER + duplicate FK)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category ENUM('Technical','Cultural','Sports','Gaming','Literary') NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    coordinator VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    seat_status VARCHAR(50) DEFAULT 'Available',
    category_id INT,
    FOREIGN KEY (category_id) REFERENCES event_categories(id)
);

-- Technical
INSERT INTO events (title, category, event_date, event_time, location, coordinator, price, category_id) VALUES
('Technical Quiz',    'Technical', '2024-11-25', '10:00:00', 'Main Auditorium', 'John Doe',      50.00, 1),
('Cryptohunt',        'Technical', '2024-11-26', '11:00:00', 'Lab 101',         'Jane Smith',    30.00, 1),
('Competitive Coding','Technical', '2024-11-27', '09:00:00', 'Computer Lab 3',  'Alice Johnson', 40.00, 1);

-- Cultural
INSERT INTO events (title, category, event_date, event_time, location, coordinator, price, category_id) VALUES
('Dance',        'Cultural', '2024-07-18', '18:00:00', 'Main Hall',   'Emily Clark',   12.00, 2),
('Drama',        'Cultural', '2024-07-23', '19:00:00', 'Stage 1',     'Mike Johnson',  10.00, 2),
('Fashion Show', 'Cultural', '2024-07-30', '20:00:00', 'Auditorium',  'Sophia Turner', 15.00, 2);

-- Sports
INSERT INTO events (title, category, event_date, event_time, location, coordinator, price, category_id) VALUES
('Football',   'Sports', '2024-07-20', '10:00:00', 'Stadium', 'Alex Morgan',    0.00, 3),
('Basketball', 'Sports', '2024-07-25', '12:00:00', 'Gym',     'Taylor Lee',     0.00, 3),
('Volleyball', 'Sports', '2024-07-28', '14:00:00', 'Court',   'Jordan Carter',  0.00, 3);

-- Gaming
INSERT INTO events (title, category, event_date, event_time, location, coordinator, price, category_id) VALUES
('PUBG',          'Gaming', '2024-07-22', '18:00:00', 'Game Zone 1', 'Nina Thompson', 10.00, 4),
('Counter-Strike','Gaming', '2024-07-27', '20:00:00', 'Game Zone 2', 'Jack Taylor',   12.00, 4),
('Candy Crush',   'Gaming', '2024-07-29', '17:00:00', 'Game Zone 3', 'Liam Brown',     5.00, 4);

-- Literary
INSERT INTO events (title, category, event_date, event_time, location, coordinator, price, category_id) VALUES
('Poetry Reading',       'Literary', '2024-07-17', '09:00:00', 'Library',          'Anna Scott',    5.00, 5),
('Book Club',            'Literary', '2024-07-24', '11:00:00', 'Reading Room',     'Ethan Roberts', 3.00, 5),
('Author Meet and Greet','Literary', '2024-07-31', '13:00:00', 'Community Center', 'Maya Wilson',   4.00, 5);

-- -------------------------------------------------------
-- committee
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS committee (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(255) NOT NULL,
    venue VARCHAR(255) NOT NULL,
    coordinator_name VARCHAR(255) NOT NULL,
    seat_status ENUM('Available','Full') NOT NULL,
    event_id INT,
    FOREIGN KEY (event_id) REFERENCES events(id)
);

-- -------------------------------------------------------
-- registrations  (FIX: event_id, category_id, payment_status
--                 all defined from the start)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    college_name VARCHAR(100) NOT NULL,
    degree VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    event_id INT,
    category_id INT,
    payment_status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id),
    FOREIGN KEY (category_id) REFERENCES event_categories(id)
);

-- -------------------------------------------------------
-- admin_sessions
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_sessions (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    admin_id INT(6) UNSIGNED,
    session_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    session_end TIMESTAMP NULL,
    session_status VARCHAR(10) NOT NULL,
    FOREIGN KEY (admin_id) REFERENCES admins(id)
);

-- -------------------------------------------------------
-- participants
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NOT NULL,
    event_id INT NOT NULL,
    category_id INT NOT NULL,
    FOREIGN KEY (registration_id) REFERENCES registrations(id),
    FOREIGN KEY (event_id) REFERENCES events(id),
    FOREIGN KEY (category_id) REFERENCES event_categories(id)
);

-- -------------------------------------------------------
-- payments  (FIX: amount_paid added, references registrations
--            not the non-existent registrations_new)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT,
    event_id INT,
    payment_method VARCHAR(50),
    amount_paid DECIMAL(10,2) DEFAULT 0.00,
    card_number VARCHAR(20) NULL,
    expiry_date VARCHAR(5) NULL,
    cvv VARCHAR(3) NULL,
    upi_id VARCHAR(50) NULL,
    payment_status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (registration_id) REFERENCES registrations(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);
