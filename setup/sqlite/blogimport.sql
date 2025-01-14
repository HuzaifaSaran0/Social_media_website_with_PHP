DELETE FROM blog;
DELETE FROM sqlite_sequence WHERE name='blog';

INSERT INTO blog(title,precis,article,created,updated)
VALUES
	('Whatever','I don’t know what I am talking about.',
		'That, of course wouldn’t necessarily get in the way of an intrepid blogger ...',
		'2011-02-22 00:45:45','2011-02-22 00:45:45'),
	('Three','The number three',
		'First shalt thou take out the Holy Pin, then shalt thou count to three, no more, no less. Three shalt be the number thou shalt count, and the number of the counting shalt be three. Four shalt thou not count, neither count thou two, excepting that thou then proceed to three. Five is right out.\r\nOnce the number three, being the third number, be reached, then lobbest thou thy Holy Hand Grenade of Antioch towards thou foe, who being naughty in My sight, shall snuff it.',
		'2011-02-22 02:15:27','2011-02-22 01:36:16'),
	('Four','For what it’s worth ...',
		'<p>For more than one cares to mention, four is foremost for exceeding three ...</p>',
		'2011-06-01 11:57:15','2011-02-22 01:38:27'),
	('Etc','And so it goes ...',
		'When you get to the end, you find inevitably that it is just the beginning of the reverse trip.\r\nAnd so it goes ...',
		'2011-02-22 01:39:48','2011-02-22 01:39:48'),
	('Australia, the Lucky Country',
		'In which the author explores what it means to be a lucky country, or at least to live in one.',
		'<p><em>I once knew a young lady who had decided to travel far and wide to explore the Lucky Country</em></p>\r\n<p>Alas, no more, or perhaps, a lass no more, but in either case, her explorations are far from over, due for the most part to the fact that they had never begun.</p>\r\n<p>Let me explain. When she had arrived at her momentous decision to wander about, seemingly aimlessly, she remembered that she had forgotten something, which is pretty clever when you think of it. Then she forgot what she had remembered, and so was no longer aware that she had fogotten anything.</p>\r\n<p>Unfortunately, in the process she also forgot to explore the Lucky Country, which is pretty unlucky.</p>',
		'2011-02-23 10:16:54','2011-02-23 09:44:42'),
	('The Land Down Under','A simple doggerel about Down Under',
		'<p>The country, they say is Down Under.<br />To some, it may look like a blunder.<br />Why should it be so?<br />Wherever you go,<br />The label may cause you to wonder.</p>\r\n<p>To those who prefer not to wander,<br />Who seldom go thither or yonder,<br />Not wishing to roam<br />Or stray from their home,<br />It’s simply a matter to ponder.</p>',
		'2011-02-23 10:29:06','2011-02-23 10:29:06'),
	('Some Australian Terms','Australians speak a dialect distantly related to English. Here you will see some terms which may help you to communicate.',
		'<table>\r\n\t<thead>\r\n\t\t<tr><th>Austalian</th><th>English</th></tr></thead>\r\n\t<tbody>\r\n\t\t<tr><td>G’Day</td><td>Hello</td></tr>\r\n\t\t<tr><td>Seeyalata</td><td>Goodbye</td></tr>\r\n\t\t<tr><td>Bloody Oath</td><td>Goodness yes</td></tr>\r\n\t\t<tr><td>Cupplapies</td><td>Lunch</td></tr>\r\n\t</tbody>\r\n</table>',
		'2011-03-13 01:45:59','2011-03-13 01:45:59');
