-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    fullname VARCHAR(100) DEFAULT NULL,
    avatar_url VARCHAR(255) DEFAULT NULL,
    birthdate DATE DEFAULT NULL,
    gender ENUM('male', 'female', 'other') DEFAULT NULL,
    is_premium BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role VARCHAR(20) DEFAULT 'user'
);


-- Create movies table
CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    release_year INT,
    genre VARCHAR(100),
    duration INT,
    poster_path VARCHAR(255),
    video_path VARCHAR(255),
    trailer_url VARCHAR(255),
    embed_url TEXT,
    direct_url TEXT,
    hls_url TEXT,
    is_premium BOOLEAN DEFAULT FALSE,
    is_series BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create episodes table for TV series
CREATE TABLE episodes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT,
    episode_number INT,
    title VARCHAR(255),
    description TEXT,
    duration INT,
    video_path VARCHAR(255),
    embed_url TEXT,
    direct_url TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

-- Create favorites table
CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    movie_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (movie_id) REFERENCES movies(id)
);

-- Create comments table
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    movie_id INT,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (movie_id) REFERENCES movies(id)
);

-- Create ratings table
CREATE TABLE ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    movie_id INT,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (movie_id) REFERENCES movies(id)
);

-- Insert sample data
INSERT INTO users (username, password, email, is_premium, role) VALUES
('admin', '3b0e0ab909a7abf76d2db168411b577e', 'admin@example.com', TRUE, 'admin'),
('user1', 'e10adc3949ba59abbe56e057f20f883e', 'user1@example.com', FALSE, 'user');

-- Insert sample movies
INSERT INTO movies (title, description, release_year, genre, duration, is_premium, is_series) VALUES
('Sample Movie 1', 'This is a sample movie description', 2023, 'Action', 120, TRUE, FALSE),
('Sample Movie 2', 'Another sample movie description', 2023, 'Comedy', 90, FALSE, FALSE),
('Sample Series 1', 'This is a sample TV series', 2023, 'Drama', 45, TRUE, TRUE); 