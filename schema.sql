-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql-r401auth.alwaysdata.net
-- Generation Time: Mar 12, 2026 at 12:20 PM
-- Server version: 11.4.9-MariaDB
-- PHP Version: 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

--
-- Database: r401auth_users
--

-- --------------------------------------------------------

--
-- Table structure for table users
--
-- Creation: Mar 12, 2026 at 12:19 PM
--

CREATE TABLE IF NOT EXISTS users (
  id int(11) NOT NULL AUTO_INCREMENT,
  user varchar(20) NOT NULL,
  password varchar(40) NOT NULL,
  role enum('coach','joueur') NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table users
--

INSERT INTO users (id, user, password, role) VALUES
(1, 'coach', 'sport', 'coach');
COMMIT;
