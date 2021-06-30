<?php

require_once 'Database.php';

$dbhost = 'localhost'; // 127.0.0.1
$dbuser = 'user';
$dbpass = 'root';
$dbname = 'media_library_api';

$database = new Database($dbhost, $dbuser, $dbpass, $dbname);

$database->query('DROP TABLE IF EXISTS albums;');
$database->query('CREATE TABLE albums (
  id INTEGER AUTO_INCREMENT NOT NULL,
  name VARCHAR(100) NOT NULL,
  user_id INTEGER NULL DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);');

$database->query('DROP TABLE IF EXISTS photos;');
$database->query('CREATE TABLE photos (
  id INTEGER AUTO_INCREMENT NOT NULL,
  album_id INTEGER NULL DEFAULT NULL,
  description VARCHAR(100) NULL DEFAULT NULL,
  location VARCHAR(100) NULL DEFAULT NULL,
  file_path VARCHAR(100) NULL DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);');

$database->query('DROP TABLE IF EXISTS users;');
$database->query('CREATE TABLE users (
  id INTEGER AUTO_INCREMENT NOT NULL,
  username VARCHAR(100) NULL UNIQUE DEFAULT NULL,
  token VARCHAR(100) NULL DEFAULT NULL,
  email VARCHAR(100) NULL UNIQUE DEFAULT NULL,
  is_admin BOOLEAN DEFAULT FALSE ,
  password VARCHAR(100) NULL DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP NULL DEFAULT NULL,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id)
);');

$database->query('INSERT INTO users (email, username, password, is_admin) VALUES (?,?,?,?)',
    'admin@admin.com',
    'admin',
    md5('admin123'),
    1 // basic user role
);

//$database->query('DROP TABLE IF EXISTS roles;');
//$database->query('CREATE TABLE roles (
//  id INTEGER AUTO_INCREMENT NOT NULL,
//  name VARCHAR(100) NULL DEFAULT NULL,
//  PRIMARY KEY (id)
//);
//');