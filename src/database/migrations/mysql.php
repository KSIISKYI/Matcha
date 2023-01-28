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

$dbh->query('CREATE TABLE matcha.discovery_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    max_distance TINYINT DEFAULT 5,
    age_min TINYINT DEFAULT 18,
    age_max TINYINT DEFAULT 40,
    fame_rating_min TINYINT DEFAULT 0,
    fame_rating_max TINYINT DEFAULT 100,
    gender_id INT,
    position_x FLOAT,
    position_y FLOAT,
    CONSTRAINT discovery_settings_genders_fk FOREIGN KEY (gender_id) REFERENCES matcha.genders(id) ON DELETE SET NULL
);');

$dbh->query('CREATE TABLE matcha.profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(64),
    last_name  VARCHAR(64),
    about_me VARCHAR(250),
    gender_id INT,
    user_id INT NOT NULL UNIQUE,
    discovery_settings_id INT UNIQUE,
    age TINYINT,
    fame_rating TINYINT,
    last_activity DATETIME,
    instagram_link VARCHAR(64),
    twitter_link VARCHAR(64),
    facebook_link VARCHAR(64),
    CONSTRAINT profiles_genders_fk FOREIGN KEY (gender_id) REFERENCES matcha.genders(id) ON DELETE SET NULL,
    CONSTRAINT profile_users_fk FOREIGN KEY (user_id) REFERENCES matcha.profiles(id) ON DELETE CASCADE,
    CONSTRAINT profile_discovery_settings_fk FOREIGN KEY (discovery_settings_id) REFERENCES matcha.discovery_settings(id)
);');

$dbh->query('CREATE TABLE matcha.profile_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    path CHAR(100) NOT NULL,
    profile_id INT NOT NULL,
    CONSTRAINT profile_photos_users_fk FOREIGN KEY (profile_id) REFERENCES matcha.profiles(id) ON DELETE CASCADE
);');

$dbh->query('CREATE TABLE matcha.interests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(40) NOT NULL
);');

$dbh->query('CREATE TABLE matcha.interest_profile (
    id INT AUTO_INCREMENT PRIMARY KEY,
    interest_id INT NOT NULL,
    profile_id INT NOT NULL,
    CONSTRAINT interest_id_profile_id_unique UNIQUE(interest_id, profile_id),
    CONSTRAINT interest_interests_profiles_fk FOREIGN KEY (interest_id) REFERENCES matcha.interests(id) ON DELETE CASCADE,
    CONSTRAINT profile_interests_profiles_fk FOREIGN KEY (profile_id) REFERENCES matcha.profiles(id) ON DELETE CASCADE
);');

$dbh->query('CREATE TABLE matcha.discovery_setting_interest (
    id INT AUTO_INCREMENT PRIMARY KEY,
    discovery_setting_id INT NOT NULL,
    interest_id INT NOT NULL,
    CONSTRAINT discovery_setting_id_interest_id_unique UNIQUE(discovery_setting_id, interest_id),
    CONSTRAINT discovery_settings_discovery_setting_intersts_fk FOREIGN KEY (discovery_setting_id) REFERENCES matcha.discovery_settings(id) ON DELETE CASCADE,
    CONSTRAINT interests_discovery_setting_interst_fk FOREIGN KEY (interest_id) REFERENCES matcha.interests(id) ON DELETE CASCADE
);');



// data
$dbh->query("INSERT INTO matcha.genders(gender)
    VALUES
        ('man'),
        ('women')
");

$dbh->query("INSERT INTO matcha.interests(name)
    VALUES
        ('sport'),
        ('art'),
        ('music'),
        ('fishing'),
        ('cars')
");