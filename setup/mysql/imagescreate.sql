/*	Images Table
	================================================
	Structure:

	| Column        | Type                                      |
	|---------------|-------------------------------------------|
	| `id`          | `INT UNSIGNED AUTO_INCREMENT PRIMARY KEY` |
	| `title`       | `VARCHAR(60) NOT NULL`                    |
	| `name`        | `VARCHAR(48) NOT NULL`                    |
	| `src`         | `VARCHAR(60) NOT NULL`                    |
	| `description` | `TEXT`                                    |
	| `gallery`     | `BOOLEAN`                                 |

	Sample:

	| id | title    | name         | src          | description              | gallery |
	|----|----------|--------------|--------------|--------------------------|---------|
	| 1  | Kangaroo | kangaroo.jpg | kangaroo.jpg | Notes on Kangaroos       | true    |
	| 3  | Wattle   | wattle.jpg   | wattle.jpg   | About Wattles            | true    |
	| 4  | Emu      | emu.jpg      | emu.jpg      | Something about Emus     | false   |
	| 6  | Koala    | koala.jpg    | koala.jpg    | Information about Koalas | true    |

	================================================ */

	USE australia;
	DROP TABLE IF EXISTS images;
	CREATE TABLE images (
		id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		title VARCHAR(60) NOT NULL,		--	descriptive name
		name VARCHAR(48) NOT NULL,		--	original file name
		src VARCHAR(60) NOT NULL,		--	current file name
		description TEXT,
		gallery BOOLEAN DEFAULT false	--	synonym for TINYINT DEFAULT 0
	) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=INNODB;
