CREATE DATABASE IF NOT EXISTS oscar
CHARACTER SET utf8mb4
COLLATE utf8mb4_general ci;

USE oscar;

CREATE DATABASE IF NOT EXIST users {
    id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(250) NOT NULL,
    status ENUM('pending', 'approved')
     NOT NULL DEFAULT 'pending',
    role ENUM('user', 'admin')
    NOT NULL DEFAULT 'user',
    created_at TIMESTAMP NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id)
);


