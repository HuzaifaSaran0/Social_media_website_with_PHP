DROP VIEW IF EXISTS userdetails;

CREATE VIEW userdetails AS
SELECT id, email, familyname, givenname, passwd, admin,
	givenname||' '||familyname as name
FROM users;
