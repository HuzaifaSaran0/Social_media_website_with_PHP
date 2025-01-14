USE australia;
DROP TABLE IF EXISTS blog;
CREATE TABLE IF NOT EXISTS blog (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	imageid INT UNSIGNED REFERENCES image(id) ON DELETE SET NULL,
	title varchar(255) NOT NULL,
	precis text NOT NULL,
	article text NOT NULL,
	created timestamp NOT NULL default CURRENT_TIMESTAMP,
	updated timestamp NOT NULL default CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=INNODB;

/*	Auto Update, if desired
	================================================
	CREATE TRIGGER update_blog_updated BEFORE UPDATE ON blog
	FOR EACH ROW
	SET new.updated=now();
	================================================ */
