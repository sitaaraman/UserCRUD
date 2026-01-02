CREATE TABLE hobbies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hobby VARCHAR(100)
);

CREATE TABLE category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100)
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    contact_no VARCHAR(15),
    hobby_id TEXT,        -- CSV: 1,2
    category_id INT,
    profile_pic VARCHAR(255)
);
