DROP TABLE IF EXISTS images;
CREATE TABLE images (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	name VARCHAR(48) NOT NULL,		--	original file name
	src VARCHAR(60) NOT NULL,		--	current file name
	title VARCHAR(60) NOT NULL,		--	descriptive name
	description TEXT
);
