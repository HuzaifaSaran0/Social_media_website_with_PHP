/*	Create DATABASE australia
	================================================
	================================================ */

--  Remove Old Database
	DROP DATABASE IF EXISTS australia;

--  Create Database
	CREATE DATABASE australia;

--	Remove ozadmin User
	GRANT USAGE ON *.* TO 'ozadmin'@'localhost' IDENTIFIED BY '';
	DROP USER 'ozadmin'@'localhost';
	FLUSH PRIVILEGES;

--	Create User ozadmin
	USE australia;
	CREATE USER 'ozadmin'@'localhost' IDENTIFIED BY 'Test@321';
	GRANT SELECT,INSERT,UPDATE,DELETE,DROP ON australia.*  TO 'ozadmin'@'localhost';
