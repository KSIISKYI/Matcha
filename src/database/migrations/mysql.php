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
    max_distance TINYINT DEFAULT 100,
    age_min TINYINT DEFAULT 18,
    age_max TINYINT DEFAULT 80,
    fame_rating_min TINYINT DEFAULT 0,
    fame_rating_max TINYINT DEFAULT 100,
    gender_id INT,
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
    age TINYINT DEFAULT 18,
    fame_rating INT DEFAULT 0,
    last_activity DATETIME,
    position_x FLOAT,
    position_y FLOAT,
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

$dbh->query('CREATE TABLE matcha.reviewed_profiles (
	id INT auto_increment PRIMARY KEY,
    viewer INT NOT NULL,
    reviewed INT NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    CONSTRAINT reviewed_profiles_viewer_fk FOREIGN KEY (viewer) REFERENCES matcha.profiles(id) ON DELETE CASCADE,
    CONSTRAINT reviewed_profiles_reviewed_fk FOREIGN KEY (reviewed) REFERENCES matcha.profiles(id) ON DELETE CASCADE
);');

$dbh->query('CREATE TABLE matcha.match_profiles (
	id INT auto_increment PRIMARY KEY,
    interested INT NOT NULL,
    interesting INT NOT NULL,
    CONSTRAINT match_profiles_interested_fk FOREIGN KEY (interested) REFERENCES matcha.profiles(id) ON DELETE CASCADE,
    CONSTRAINT match_profiles_interesting_fk FOREIGN KEY (interesting) REFERENCES matcha.profiles(id) ON DELETE CASCADE
);');

$dbh->query('CREATE TABLE matcha.activity_log (
	id INT auto_increment PRIMARY KEY,
    viewer INT NOT NULL,
    reviewed INT NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    CONSTRAINT activity_log_viewer_fk FOREIGN KEY (viewer) REFERENCES matcha.profiles(id) ON DELETE CASCADE,
    CONSTRAINT activity_log_reviewed_fk FOREIGN KEY (reviewed) REFERENCES matcha.profiles(id) ON DELETE CASCADE
);');

$dbh->query('CREATE TABLE matcha.blocked_profiles (
	id INT auto_increment PRIMARY KEY,
    blocker INT NOT NULL,
    blocked INT NOT NULL,
    CONSTRAINT blocked_profiles_blocker_fk FOREIGN KEY (blocker) REFERENCES matcha.profiles(id) ON DELETE CASCADE,
    CONSTRAINT blocked_profiles_blocked_fk FOREIGN KEY (blocked) REFERENCES matcha.profiles(id) ON DELETE CASCADE
);');

$dbh->query('CREATE TABLE matcha.fake_profile_reports (
	id INT auto_increment PRIMARY KEY,
    reporter INT NOT NULL,
    fake_profile INT NOT NULL,
    CONSTRAINT fake_profile_reports_reporter_fk FOREIGN KEY (reporter) REFERENCES matcha.profiles(id) ON DELETE CASCADE,
    CONSTRAINT fake_profile_reports_fake_profile_fk FOREIGN KEY (fake_profile) REFERENCES matcha.profiles(id) ON DELETE CASCADE
);');

$dbh->query('CREATE TABLE matcha.chats (
    id INT auto_increment PRIMARY KEY,
    created_at DATETIME,
    updated_at DATETIME
);');

$dbh->query('CREATE TABLE matcha.participants (
    id INT auto_increment PRIMARY KEY,
    chat_id INT NOT NULL,
    profile_id INT NOT NULL,
    CONSTRAINT participants_chat_fk FOREIGN KEY (chat_id) REFERENCES matcha.chats(id) ON DELETE CASCADE,
    CONSTRAINT participants_profile_fk FOREIGN KEY (profile_id) REFERENCES matcha.profiles(id) ON DELETE CASCADE
);');

$dbh->query('CREATE TABLE matcha.messages (
    id INT auto_increment PRIMARY KEY,
    chat_id INT NOT NULL,
    participant_id INT NOT NULL,
    message TEXT,
    reviewed BOOLEAN DEFAULT FALSE,
    created_at DATETIME,
    updated_at DATETIME,
    CONSTRAINT messages_chat_fk FOREIGN KEY (chat_id) REFERENCES matcha.chats(id) ON DELETE CASCADE,
    CONSTRAINT messages_participant_fk FOREIGN KEY (participant_id) REFERENCES matcha.participants(id) ON DELETE CASCADE
);');




// data
$dbh->query("INSERT INTO matcha.genders(gender)
    VALUES
        ('man'),
        ('women')
");

$dbh->exec("SET GLOBAL log_bin_trust_function_creators = 1;");

$dbh->exec("
    CREATE FUNCTION matcha.calcCrow( lat1 FLOAT, lon1 FLOAT, lat2 FLOAT, lon2 FLOAT ) RETURNS INTEGER
        BEGIN
        DECLARE R INT;
        DECLARE dLat FLOAT;
        DECLARE dLon FLOAT;
        DECLARE a FLOAT;
        DECLARE c FLOAT;
        DECLARE d FLOAT;


        SET R = 6371;
        SET dLat = RADIANS(lat2 - lat1);
        SET dLon = RADIANS(lon2 - lon1);
        SET lat1 = RADIANS(lat1);
        SET lat2 = RADIANS(lat2);

        SET a = SIN(dLat / 2) * SIN(dLat / 2) + SIN(dLon / 2) * SIN(dLon/2) * COS(lat1) * COS(lat2);
        SET c = 2 * ATAN2(SQRT(a), SQRT(1 - a)); 
        SET d = R * c;

        return d;
        END"
);

$dbh->exec("
    CREATE FUNCTION matcha.calcFameRating(rating INT) returns INT
        BEGIN
        DECLARE one_percent FLOAT;
        DECLARE min INT;
        DECLARE max INT;
        DECLARE result INT;
            
        SET min = (select min(fame_rating) from matcha.profiles);
        SET max = (select max(fame_rating) from matcha.profiles);

        IF rating = min THEN
            return 0;
        END IF;

        SET one_percent = (max - min) / 100;
        SET result = FLOOR((rating - min) / one_percent);

        return result;
        END"
);

$dbh->query("INSERT INTO matcha.interests(name)
    VALUES
        ('sport'),
        ('art'),
        ('music'),
        ('fishing'),
        ('cars')
");
