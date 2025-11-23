CREATE DATABASE college_event_management;
USE college_event_management;

CREATE TABLE IF NOT EXISTS event_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    img_path VARCHAR(255) NOT NULL,
    description TEXT
);

INSERT INTO event_categories (title, img_path, description)
VALUES 
('Technical Events', 'img/technical.png', 'Participate in exciting technical events!'),
('Cultural Events', 'img/cultural.png', 'Showcase your talents in cultural events!'),
('Sports Events', 'img/sports.png', 'Join thrilling sports events!'),
('Gaming Events', 'img/gaming.png', 'Compete in the latest gaming events!'),
('Literary Events', 'img/literary.png', 'Express yourself in literary events!');

CREATE TABLE admins (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category ENUM('Technical', 'Cultural', 'Sports', 'Gaming', 'Literary') NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,  -- New column added for event time
    location VARCHAR(255) NOT NULL,
    coordinator VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT,  -- New column for foreign key relationship
    FOREIGN KEY (category_id) REFERENCES event_categories(id)  -- Foreign key constraint
);
--Technical Eventss--
INSERT INTO events (title, category, event_date, event_time, location, coordinator, price, category_id)
VALUES 
('Technical Quiz', 'Technical', '2024-11-25', '10:00:00', 'Main Auditorium', 'John Doe', 50.00, 1),
('Cryptohunt', 'Technical', '2024-11-26', '11:00:00', 'Lab 101', 'Jane Smith', 30.00, 1),
('Competitive Coding', 'Technical', '2024-11-27', '09:00:00', 'Computer Lab 3', 'Alice Johnson', 40.00, 1);
--Cultural Events--
INSERT INTO events (title, category, event_date, event_time, location, coordinator, price, category_id)
VALUES 
('Dance', 'Cultural', '2024-07-18', '18:00:00', 'Main Hall', 'Emily Clark', 12.00, 2),
('Drama', 'Cultural', '2024-07-23', '19:00:00', 'Stage 1', 'Mike Johnson', 10.00, 2),
('Fashion Show', 'Cultural', '2024-07-30', '20:00:00', 'Auditorium', 'Sophia Turner', 15.00, 2);
-- Sports Events
INSERT INTO events (title, category, event_date, event_time, location, coordinator, price, category_id) VALUES
('Football', 'Sports', '2024-07-20', '10:00:00', 'Stadium', 'Alex Morgan', 0.00, 3),
('Basketball', 'Sports', '2024-07-25', '12:00:00', 'Gym', 'Taylor Lee', 0.00, 3),
('Volleyball', 'Sports', '2024-07-28', '14:00:00', 'Court', 'Jordan Carter', 0.00, 3);

-- Gaming Events
INSERT INTO events (title, category, event_date, event_time, location, coordinator, price, category_id) VALUES
('PUBG', 'Gaming', '2024-07-22', '18:00:00', 'Game Zone 1', 'Nina Thompson', 10.00, 4),
('Counter-Strike', 'Gaming', '2024-07-27', '20:00:00', 'Game Zone 2', 'Jack Taylor', 12.00, 4),
('Candy Crush', 'Gaming', '2024-07-29', '17:00:00', 'Game Zone 3', 'Liam Brown', 5.00, 4);

-- Literary Events
INSERT INTO events (title, category, event_date, event_time, location, coordinator, price, category_id) VALUES
('Poetry Reading', 'Literary', '2024-07-17', '09:00:00', 'Library', 'Anna Scott', 5.00, 5),
('Book Club', 'Literary', '2024-07-24', '11:00:00', 'Reading Room', 'Ethan Roberts', 3.00, 5),
('Author Meet and Greet', 'Literary', '2024-07-31', '13:00:00', 'Community Center', 'Maya Wilson', 4.00, 5);

ALTER TABLE event_categories ADD COLUMN page_link VARCHAR(255);


UPDATE event_categories SET page_link = 'technical_events.php' WHERE title = 'Technical Events';
UPDATE event_categories SET page_link = 'cultural_events.php' WHERE title = 'Cultural Events';
UPDATE event_categories SET page_link = 'sports_events.php' WHERE title = 'Sports Events';
UPDATE event_categories SET page_link = 'gaming_events.php' WHERE title = 'Gaming Events';
UPDATE event_categories SET page_link = 'literary_events.php' WHERE title = 'Literary Events';

ALTER TABLE events 
ADD COLUMN category_id INT,
ADD CONSTRAINT fk_category_id FOREIGN KEY (category_id) REFERENCES event_categories(id);

UPDATE events e
JOIN event_categories ec ON e.category = ec.title
SET e.category_id = ec.id;

CREATE TABLE committee (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(255) NOT NULL,
    venue VARCHAR(255) NOT NULL,
    coordinator_name VARCHAR(255) NOT NULL,
    seat_status ENUM('Available', 'Full') NOT NULL
);
ALTER TABLE events ADD CONSTRAINT fk_category_id FOREIGN KEY (category_id) REFERENCES event_categories(id);
ALTER TABLE committee ADD COLUMN event_id INT;
ALTER TABLE committee ADD CONSTRAINT fk_event_id FOREIGN KEY (event_id) REFERENCES events(id);
UPDATE events e JOIN event_categories ec ON e.category = ec.title SET e.category_id = ec.id;
ALTER TABLE events ADD COLUMN seat_status VARCHAR(50) DEFAULT 'Available';

CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    college_name VARCHAR(100) NOT NULL,
    degree VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL
);
CREATE TABLE admin_sessions (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,  -- Auto-incrementing ID for each session record
    session_id VARCHAR(255) NOT NULL,                -- Unique session ID for each session
    admin_id INT(6) UNSIGNED,                        -- Link to admin user
    session_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- When session started
    session_end TIMESTAMP NULL,                      -- When session ended
    session_status VARCHAR(10) NOT NULL,             -- e.g., 'active', 'closed'
    FOREIGN KEY (admin_id) REFERENCES admins(id)
);


CREATE TABLE participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NOT NULL, -- Foreign key to registrations table
    event_id INT NOT NULL, -- Foreign key to events table
    category_id INT NOT NULL, -- Foreign key to event_categories table
    FOREIGN KEY (registration_id) REFERENCES registrations(id),
    FOREIGN KEY (event_id) REFERENCES events(id),
    FOREIGN KEY (category_id) REFERENCES event_categories(id)
);

ALTER TABLE registrations
ADD COLUMN event_id INT,
ADD COLUMN category_id INT;

UPDATE registrations r
JOIN events e ON r.event_id = e.id
JOIN event_categories ec ON e.category_id = ec.id
SET r.event_id = e.id, r.category_id = ec.id;

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT, -- ID from registrations_new table
    event_id INT, -- ID from events table
    payment_method VARCHAR(50),
    card_number VARCHAR(20) NULL,
    expiry_date VARCHAR(5) NULL,
    cvv VARCHAR(3) NULL,
    upi_id VARCHAR(50) NULL,
    payment_status VARCHAR(50) DEFAULT 'pending',
    FOREIGN KEY (registration_id) REFERENCES registrations_new(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);
