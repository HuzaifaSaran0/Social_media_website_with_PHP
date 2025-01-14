DROP TABLE IF EXISTS blog;
CREATE TABLE IF NOT EXISTS blog (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	title varchar(255) NOT NULL,
	precis text NOT NULL,
	article text NOT NULL,
	created integer NOT NULL default CURRENT_TIMESTAMP,
	updated integer NOT NULL default CURRENT_TIMESTAMP
);
CREATE TRIGGER update_blog_updated UPDATE ON blog
BEGIN
	UPDATE blog SET updated=CURRENT_TIMESTAMP WHERE id=old.id;
END;
