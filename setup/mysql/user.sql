USE australia;

--	The following dummy user will have
CREATE VIEW userdetails AS
SELECT id, email, familyname, givenname, passwd, admin,
	concat(givenname,' ',familyname) as name
FROM users;
