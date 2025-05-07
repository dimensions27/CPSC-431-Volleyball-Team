
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS volleyball_db;
CREATE DATABASE IF NOT EXISTS volleyball_db;

USE volleyball_db;

-- TEAM STUFF
CREATE TABLE Teams (
  team_id INT AUTO_INCREMENT PRIMARY KEY,
  team_name VARCHAR(100),
  division  VARCHAR(100),
  coach_name VARCHAR(100),
  team_rank INT DEFAULT NULL
);

INSERT INTO Teams (team_name, division, coach_name, team_rank) VALUES
('CSUF Titans', 'D1', 'Coach Carter', 1),
('UCSD Tritons', 'D1', 'Coach Han', 2),
('UCI Anteaters', 'D1', 'Coach Trent', 3);

-- ROLE STUFF
CREATE TABLE Roles (
  role_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(30),
  lastModified  DATETIME DEFAULT current_timestamp() ON UPDATE current_timestamp()
);

INSERT INTO Roles (name) VALUES
('observer'), ('player'), ('coach'), ('manager');

-- USER STUFF
CREATE TABLE Users (
  user_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100),
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password CHAR(60) NOT NULL,
  role_id TINYINT UNSIGNED NOT NULL,
  lastModified DATETIME DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  FOREIGN KEY (role_id) REFERENCES Roles(role_id)
);

INSERT INTO Users (first_name, last_name, email, password, role_id)
VALUES 
('John', 'Smith', 'johnsmith@gmail.com', '!john', 2),
('Coach', 'Carter', 'carter@csuf.edu', '!coachcarter', 3);

-- PLAYER STUFF
CREATE TABLE Players (
  player_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL UNIQUE,
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  team_id INT,
  position VARCHAR(100),
  height INT,
  weight INT,
  street VARCHAR(250),
  city VARCHAR(250),
  state VARCHAR(100),
  country VARCHAR(100),
  zipcode CHAR(10),
  is_active BOOLEAN DEFAULT TRUE,
  FOREIGN KEY (team_id) REFERENCES Teams(team_id),
  FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- Link John Smith to CSUF
INSERT INTO Players (
  user_id, first_name, last_name, team_id, position,
  height, weight, street, city, state, country, zipcode
)
VALUES (
  LAST_INSERT_ID(), 'John', 'Smith', 1, 'Outside Hitter',
  190, 180, 'Highlands', 'Fullerton', 'CA', 'USA', '91234'
);

-- GAME STUFF
CREATE TABLE Games (
  game_id INT AUTO_INCREMENT PRIMARY KEY,
  game_date DATE,
  opponent VARCHAR(100),
  location VARCHAR(100),
  team_id INT,
  result ENUM('Win', 'Loss'),
  FOREIGN KEY (team_id) REFERENCES Teams(team_id)
);

INSERT INTO Games (game_date, opponent, location, team_id, result) VALUES
('2025-04-01', 'UCLA Bruins', 'Titan Gym', 1, 'Win'),
('2025-04-08', 'UC Irvine', 'Irvine Arena', 1, 'Loss');

-- STAT STUFF
CREATE TABLE PlayerStats (
  stat_id INT AUTO_INCREMENT PRIMARY KEY,
  game_id INT,
  player_id INT UNSIGNED,
  kills INT UNSIGNED DEFAULT 0,
  blocks INT UNSIGNED DEFAULT 0,
  serving_aces INT UNSIGNED DEFAULT 0,
  assists INT UNSIGNED DEFAULT 0,
  digs INT UNSIGNED DEFAULT 0,
  FOREIGN KEY (game_id) REFERENCES Games(game_id),
  FOREIGN KEY (player_id) REFERENCES Players(player_id) ON DELETE CASCADE
);

INSERT INTO PlayerStats (game_id, player_id, kills, blocks, serving_aces, assists, digs)
VALUES
(1, 1, 7, 3, 2, 4, 3),
(2, 1, 8, 1, 1, 5, 7);

SET FOREIGN_KEY_CHECKS = 1;
