CREATE DATABASE enc_tracker
CHARACTER SET utf8
COLLATE utf8_general_ci;

USE enc_tracker;

CREATE TABLE users (
user_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
username VARCHAR(40) NOT NULL,
email VARCHAR(80) NOT NULL,
pass CHAR(40) NOT NULL,
user_level TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
active CHAR(32),
registration_date DATETIME NOT NULL,
PRIMARY KEY (user_id),
UNIQUE (username),
UNIQUE (email),
INDEX login (email, pass)
)ENGINE = INNODB;

CREATE TABLE encounters (
encounter_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
name VARCHAR(20) NOT NULL,
current_round INT UNSIGNED NOT NULL DEFAULT 0,
current_init INT UNSIGNED NOT NULL DEFAULT 0,
description TEXT NOT NULL,
user_id INT UNSIGNED,
PRIMARY KEY (encounter_id),
UNIQUE (name),
INDEX (current_round),
FOREIGN KEY (user_id) REFERENCES users (user_id)
ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE = INNODB;

CREATE TABLE creatures (
creature_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
name VARCHAR(20) NOT NULL,
description TEXT NOT NULL,
user_id INT UNSIGNED,
PRIMARY KEY (creature_id),
UNIQUE (name),
FOREIGN KEY (user_id) REFERENCES users (user_id)
ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE = INNODB;

CREATE TABLE effects (
effect_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
name VARCHAR(20) NOT NULL,
description TEXT NOT NULL,
user_id INT UNSIGNED,
PRIMARY KEY (effect_id),
UNIQUE (name),
FOREIGN KEY (user_id) REFERENCES users (user_id)
ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE = INNODB;

CREATE TABLE creature_effects (
creature_effect_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
creature_id INT UNSIGNED NOT NULL,
effect_id INT UNSIGNED NOT NULL,
PRIMARY KEY (creature_effect_id),
INDEX (creature_id),
INDEX (effect_id),
FOREIGN KEY (creature_id) REFERENCES creatures (creature_id)
ON DELETE CASCADE ON UPDATE NO ACTION,
FOREIGN KEY (effect_id) REFERENCES effects (effect_id)
ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = INNODB;

CREATE TABLE participants (
participant_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
name VARCHAR(20) NOT NULL,
creature_id INT UNSIGNED,
encounter_id INT UNSIGNED NOT NULL,
initiative INT UNSIGNED NOT NULL,
tie INT UNSIGNED NOT NULL DEFAULT 0,
PRIMARY KEY (participant_id),
INDEX (creature_id),
INDEX (encounter_id),
FOREIGN KEY (creature_id) REFERENCES creatures (creature_id)
ON DELETE SET NULL ON UPDATE NO ACTION,
FOREIGN KEY (encounter_id) REFERENCES encounters (encounter_id)
ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = INNODB;

CREATE TABLE corpses (
corpse_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
name VARCHAR(20) NOT NULL,
creature_id INT UNSIGNED,
encounter_id INT UNSIGNED NOT NULL,
PRIMARY KEY (corpse_id),
INDEX (creature_id),
INDEX (encounter_id),
UNIQUE (name),
FOREIGN KEY (creature_id) REFERENCES creatures (creature_id) 
ON DELETE SET NULL ON UPDATE NO ACTION,
FOREIGN KEY (encounter_id) REFERENCES encounters (encounter_id) 
ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = INNODB;

CREATE TABLE active_effects (
active_effect_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
effect_id INT UNSIGNED NOT NULL,
encounter_id INT UNSIGNED NOT NULL,
participant_id INT UNSIGNED,
PRIMARY KEY (active_effect_id),
INDEX (effect_id),
INDEX (encounter_id),
INDEX (participant_id),
FOREIGN KEY (effect_id) REFERENCES effects (effect_id)
ON DELETE CASCADE ON UPDATE NO ACTION,
FOREIGN KEY (encounter_id) REFERENCES encounters (encounter_id)
ON DELETE CASCADE ON UPDATE NO ACTION,
FOREIGN KEY (participant_id) REFERENCES participants (participant_id)
ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = INNODB;

CREATE TABLE targets (
target_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
active_effect_id INT UNSIGNED NOT NULL,
participant_id INT UNSIGNED NOT NULL,
PRIMARY KEY (target_id),
INDEX (active_effect_id),
INDEX (participant_id),
FOREIGN KEY (active_effect_id) REFERENCES active_effects (active_effect_id)
ON DELETE CASCADE ON UPDATE NO ACTION,
FOREIGN KEY (participant_id) REFERENCES participants (participant_id)
ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = INNODB;

INSERT INTO users (email, pass, username, active, user_level, registration_date) VALUES 
('admin@a.com', SHA1('EncAdmin'), 'Admin', NULL, 4, NOW() ),
('mod@m.com', SHA1('EncMod'), 'Moderator', NULL, 3, NOW() ),
('gm@g.com', SHA1('EncGm'), 'GameMaster', NULL, 2, NOW() ),
('player@p.com', SHA1('EncPlayer'), 'Player', NULL, 1, NOW() ),
('test@t.com', SHA1('EncTest'), 'Test', NULL, 0, NOW() ),
('spectator@s.com', SHA1('EncSpec'), 'Spectator', NULL, 0, NOW() );

INSERT INTO creatures (name, description, user_id) VALUES
('Fantome', 'Half-Elven Bard', 4),
('Thom', 'Human Cleric', 4),
('Elorin', 'Eladrin Swordmage', 3),
('Goblin', 'Pint sized menace', 3),
('HobGoblin', 'Full sized menace', 3),
('Jake', 'The Snake', 2);

INSERT INTO effects (name, description, user_id) VALUES
('Song of Healing', 'Allies within 4sqs of Fantome regain 2d8 HP at the start of their turn', 4),
('Bless', '+1 to damage and attacks', 4),
('Get Back!', 'When any creature moves into a space adjacent to Elorin he can use an immediate action to push them one square and immobilize them until the end of their turn', 3),
('Environmental Heat', 'Must beat endurance DC 20 or take 1d8 heat damage at the start of each turn', 3),
('Magic Turret Trap', 'When any creature moves within 3 sqs, Fires a bolt of magic fire: +20 vs REF 3d6+8 fire damage', 3);

INSERT INTO encounters (name, description, user_id) VALUES
('CaravanCrossingD1', 'High noon goblin ambush', 3),
('Blank', 'Blank', NULL);

INSERT INTO participants (encounter_id, creature_id, name, initiative) VALUES
(1, 1, 'Fantome', 20),
(1, 2, 'Thom', 40),
(1, 3, 'Elorin', 50),
(1, 4, 'Goblin A', 30),
(1, 4, 'Goblin B', 31),
(1, 4, 'Goblin C', 32),
(1, 4, 'Goblin D', 33),
(1, 5, 'HobGoblin A', 10),
(1, 5, 'HobGoblin A', 11);

INSERT INTO active_effects (encounter_id, participant_id, effect_id) VALUES
(1, 1, 1),
(1, 2, 2),
(1, 3, 3),
(1, NULL, 4),
(1, NULL, 5);

INSERT INTO targets (active_effect_id, participant_id) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(2, 2),
(2, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 7),
(3, 8),
(3, 9),
(4, 1),
(4, 2),
(4, 3),
(4, 4),
(4, 5),
(4, 6),
(4, 7),
(4, 8),
(4, 9),
(5, 1),
(5, 2),
(5, 3),
(5, 4),
(5, 5),
(5, 6),
(5, 7),
(5, 8),
(5, 9);

INSERT INTO creature_effects (creature_id, effect_id) VALUES
(1, 1),
(2, 2),
(3, 3);





