<?php

require_once  __DIR__ . '/../Connection.php';

$dbh = (new Connection)->getConnection();

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

$dbh->query('CREATE TABLE matcha.notification_events (
    id INT auto_increment PRIMARY KEY,
    type VARCHAR(20),
    context VARCHAR(128)
);');

$dbh->query('CREATE TABLE matcha.notifications (
    id INT auto_increment PRIMARY KEY,
    notifier_id INT NOT NULL,
    notified_id INT NOT NULL,
    event_id INT NOT NULL,
    reviewed BOOLEAN DEFAULT FALSE,
    created_at DATETIME,
    updated_at DATETIME,
    CONSTRAINT notifications_from_fk FOREIGN KEY (notifier_id) REFERENCES matcha.profiles(id) ON DELETE CASCADE,
    CONSTRAINT notifications_to_fk FOREIGN KEY (notified_id) REFERENCES matcha.profiles(id) ON DELETE CASCADE,
    CONSTRAINT notifications_event_fk FOREIGN KEY (event_id) REFERENCES matcha.notification_events(id) ON DELETE CASCADE
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
        ('cars'),
        ('yoga'),
        ('vegan'),
        ('geek'),
        ('piercing'),
        ('photo'),
        ('architecture'),
        ('rock'),
        ('pop')

");

$dbh->query("INSERT INTO matcha.notification_events(type, context)
    VALUES
        ('like', 'liked you'),
        ('match', 'You have new match')
");

$dbh->query("INSERT INTO matcha.users(username, email, is_active, password)
    VALUES
        ('username1', 'username1@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username2', 'username2@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username3', 'username3@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username4', 'username4@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username5', 'username5@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username6', 'username6@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username7', 'username7@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username8', 'username8@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username9', 'username9@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username10', 'username10@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username11', 'username11@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username12', 'username12@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username13', 'username13@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username14', 'username14@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username15', 'username15@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username16', 'username16@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username17', 'username17@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username18', 'username18@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username19', 'username19@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('username20', 'username20@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe'),
        ('my_user', 'my_user@gmail.com', 1, '\$2y\$10\$8Gq5gOQ75zxuzRlmNBJGkuPzAYBjHZ5wF.0lSYohjt8gd/6VqQxAe')
");

$dbh->query("INSERT INTO matcha.discovery_settings()
    VALUES
        (), (), (), (), (), (), (), (), (), (), (), (), (), (), (), (), (), (), (), (), ()
");

$dbh->query("INSERT INTO matcha.profiles(first_name, last_name, gender_id, user_id, discovery_settings_id, age, fame_rating, last_activity, position_x, position_y)
    VALUES
        ('Yaakov', 'Bennett', 1, 1, 1, 18, 220, '2023-02-21 08:53:40', 50.450001, 31.523333),
        ('Jonathan', 'Taylor', null, 2, 2, 19, 90, '2023-02-22 08:53:40', 51.450001, 30.523333),
        ('Zeus', 'Green', 1, 3, 3, 23, 340, '2023-02-22 08:53:40', 50.950001, 30.923333),
        ('Isiah', 'Watson', 1, 4, 4, 32, 52, '2023-02-21 08:53:40', 50.850001, 31.523333),
        ('Waylon', 'Torres', null, 5, 5, 23, 130, '2023-02-23 08:53:40', 51.450001, 30.923333),
        ('Joshua', 'Brooks', 1, 6, 6, 28, 180, '2023-02-25 08:53:40', 51.450001, 30.223333),
        ('Kane', 'Sanchez', null, 7, 7, 25, 45, '2023-02-21 08:53:40', 50.150001, 30.923333),
        ('Myles', 'Martin', 1, 8, 8, 34, 234, '2023-02-23 08:53:40', 50.250001, 30.923333),
        ('Chase', 'Baker', 1, 9, 9, 23, 321, '2023-02-25 08:53:40', 50.250001, 30.623333),
        ('Kolton', 'Morgan', 1, 10, 10, 23, 190, '2023-02-25 08:53:40', 50.150001, 31.523333),
        ('Damaris', 'Bell', 2, 11, 11, 18, 320, '2023-02-23 08:53:40', 51.450001, 30.123333),
        ('Quinna', 'Long', 2, 12, 12, 23, 300, '2023-01-25 08:53:40', 50.250001, 30.823333),
        ('Dior', 'Williams', 2, 13, 13, 20, 200, '2023-02-25 08:53:40', 51.450001, 30.223333),
        ('Unita', 'Jenkins', null, 14, 14, 28, 80, '2023-02-25 08:53:40', 50.850001, 30.823333),
        ('Unita', 'Jones', 2, 15, 15, 23, 300, '2023-01-25 08:53:40', 50.150001, 30.123333),
        ('Luna', 'Martin', 2, 16, 16, 18, 70, '2023-02-25 08:53:40', 50.250001, 30.223333),
        ('Xena', 'Hughes', 2, 17, 17, 21, 160, '2023-02-23 08:53:40', 50.350001, 30.823333),
        ('Yolanda', 'Bennett', null, 18, 18, 19, 230, '2023-01-25 08:53:40', 50.150001, 31.523333),
        ('Nellie', 'Anderson', 2, 19, 19, 25, 90, '2023-02-25 08:53:40', 50.450001, 30.923333),
        ('Fatimah', 'Scott', 2, 20, 20, 42, 80, '2023-02-25 08:53:40', 50.450001, 30.923333),
        ('first name', 'last name', null, 21, 21, 22, 100, '2023-02-25 08:53:40', 50.450001, 30.523333)
");

$dbh->query("INSERT INTO matcha.profile_photos(path, profile_id)
    VALUES
        ('profile_images/1/167732427553.jpeg', 1),
        ('profile_images/2/1677324275637.jpeg', 2),
        ('profile_images/3/1677324484743.jpeg', 3),
        ('profile_images/4/1677324485392.jpeg', 4),
        ('profile_images/5/1677324784915.jpeg', 5),
        ('profile_images/6/1677324485631.jpeg', 6),
        ('profile_images/10/167732462472.jpeg', 10),
        ('profile_images/11/167732466332.jpeg', 11),
        ('profile_images/12/1677324698906.jpeg', 12),
        ('profile_images/13/1677324739279.jpeg', 13),
        ('profile_images/14/1677324739363.jpeg', 14)
");

$dbh->query("INSERT INTO matcha.interest_profile(interest_id, profile_id)
    VALUES
        (6, 1), (3, 1), (1, 1), (4, 1),
        (1, 2), (3, 2),
        (3, 3), (4, 3),
        (5, 4), (6, 4),
        (7, 5), (8, 5),
        (9, 6), (10, 6),
        (11, 7), (12, 7),
        (1, 8), (2, 8),
        (3, 9), (4, 9),
        (5, 10), (6, 10),
        (7, 11), (8, 11),
        (9, 12), (10, 12),
        (11, 13), (12, 13),
        (1, 14), (2, 14),
        (3, 15), (4, 15),
        (5, 16), (6, 16),
        (7, 17), (8, 17),
        (9, 18), (10, 18),
        (11, 19), (12, 19),
        (1, 20), (2, 20)
");

$dbh->query("INSERT INTO matcha.match_profiles(interested, interesting)
    VALUES
        (1, 21),
        (2, 21),
        (7, 21),
        (11, 21),
        (14, 21),
        (19, 21),
        (13, 21)
");
