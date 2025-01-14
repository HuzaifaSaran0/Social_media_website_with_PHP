USE australia;
DROP TABLE IF EXISTS users;
CREATE TABLE users (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	email VARCHAR(60) NOT NULL UNIQUE,
	familyname VARCHAR(40) NOT NULL,
	givenname VARCHAR(40) NOT NULL,
	hash VARCHAR(255) NOT NULL,	--	use with PHP password functions
	admin BOOLEAN NOT NULL DEFAULT FALSE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=INNODB;

/*	Add a user
	================================================
	The last value is an integer:
	1	True
	0	False
	================================================
	USE australia;
	INSERT INTO users(email,familyname,givenname,hash,admin)
	VALUES ('…','…','…','…',…)
	================================================ */
