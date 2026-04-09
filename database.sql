-- Database creation script for Harvester Payment Tracker

CREATE DATABASE IF NOT EXISTS harvester;
USE harvester;

CREATE TABLE IF NOT EXISTS ht_users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(150) NOT NULL,
    username   VARCHAR(80)  NOT NULL UNIQUE,
    phone      VARCHAR(20),
    vehicle    VARCHAR(50),
    password   VARCHAR(255) NOT NULL,
    token      VARCHAR(64),
    token_exp  DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS harvester_entries (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL DEFAULT 1,
    name       VARCHAR(150) NOT NULL,
    phone      VARCHAR(20),
    address    VARCHAR(255),
    date       DATE,
    acres      DECIMAL(8,2)  DEFAULT 0,
    crop       VARCHAR(100),
    rate       DECIMAL(10,2) DEFAULT 0,
    amount     DECIMAL(10,2) DEFAULT 0,
    collected  DECIMAL(10,2) DEFAULT 0,
    balance    DECIMAL(10,2) DEFAULT 0,
    vehicle    VARCHAR(50),
    read_start DECIMAL(10,2) DEFAULT 0,
    read_end   DECIMAL(10,2) DEFAULT 0,
    fuel_l     DECIMAL(8,2)  DEFAULT 0,
    fuel_rate  DECIMAL(8,2)  DEFAULT 0,
    fuel_cost  DECIMAL(10,2) DEFAULT 0,
    notes      TEXT,
    photo      VARCHAR(255),
    paid       TINYINT(1)    DEFAULT 0,
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    INDEX (user_id),
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES ht_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
