<?php

require_once '../Connection.php';

$dbh = (new Connection('127.0.0.1', 'root', 'root'))->getConnection();

$dbh->query('DROP DATABASE IF EXISTS matcha');
$dbh->query('CREATE DATABASE matcha');

$dbh->query('CREATE TABLE IF NOT EXISTS matcha.users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(64) NOT NULL UNIQUE,
    email VARCHAR(64) NOT NULL UNIQUE,
    is_active BOOLEAN DEFAULT FALSE,
    is_google_auth BOOLEAN DEFAULT FALSE,
    password VARCHAR(64)
);');

$dbh->query('CREATE TABLE matcha.pending_users (
    token CHAR(40) NOT NULL,
    user_id INT NOT NULL UNIQUE,
    PRIMARY KEY(token),
    CONSTRAINT pending_users_users_fk FOREIGN KEY (user_id) REFERENCES matcha.users(id) ON DELETE CASCADE
);');

$dbh->query('CREATE TABLE matcha.new_user_emails (
    token CHAR(40) NOT NULL,
    email VARCHAR(64) NOT NULL,
    user_id INT NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    PRIMARY KEY(token),
    CONSTRAINT new_email_users_users_fk FOREIGN KEY (user_id) REFERENCES matcha.users(id) ON DELETE CASCADE
);');

$dbh->query('CREATE TABLE matcha.genders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gender VARCHAR(40)
);');

$dbh->query('CREATE TABLE matcha.profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(64),
    last_name  VARCHAR(64),
    about_me VARCHAR(250),
    gender_id INT,
    user_id INT NOT NULL UNIQUE,
    CONSTRAINT prifiles_genders_fk FOREIGN KEY (gender_id) REFERENCES matcha.genders(id) ON DELETE SET NULL,
    CONSTRAINT profile_users_users_fk FOREIGN KEY (user_id) REFERENCES matcha.profiles(id) ON DELETE CASCADE
);');

$dbh->query('CREATE TABLE matcha.profile_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    path CHAR(100) NOT NULL,
    profile_id INT NOT NULL,
    CONSTRAINT profile_photos_users_fk FOREIGN KEY (profile_id) REFERENCES matcha.profiles(id) ON DELETE CASCADE
);');

// $dbh->query('CREATE TABLE matcha.profile_photos_profiles (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     profile_id INT NOT NULL,
//     profile_photo_id INT NOT NULL,
//     CONSTRAINT profile_photos_profiles_profile_id FOREIGN KEY (profile_id) REFERENCES matcha.profiles(id) ON DELETE CASCADE,
//     CONSTRAINT profile_photos_profiles_profile_photo_id FOREIGN KEY (profile_photo_id) REFERENCES matcha.profile_photos(id) ON DELETE CASCADE
// );');