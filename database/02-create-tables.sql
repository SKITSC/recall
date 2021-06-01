
-- date: 26-05-2021
-- author: Iyad Al-Kassab @ SKITSC
-- description: create's tables on deployement

use plivo_backer;

DROP TABLE IF EXISTS `backer_users`;
DROP TABLE IF EXISTS `backer_recordings`;

CREATE TABLE backer_users (
  id INT NOT NULL AUTO_INCREMENT,
  username VARCHAR(64) DEFAULT '' NOT NULL,
  password VARCHAR(255) DEFAULT '' NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (id)
);

INSERT INTO backer_users (username, password) VALUES
    ('skitsc', '$2y$10$ZZvdSoF4hk92M9iGqxpMCOIYVk2wioQhB3/mecVebbl0fx7HEbWS6'); -- password: skitsc

CREATE TABLE backer_recordings (
  id INT NOT NULL AUTO_INCREMENT,
  call_uuid VARCHAR(36),
  add_time DATETIME,
  recording_url VARCHAR(255),
  recording_duration INT,
  from_number VARCHAR(32),
  to_number VARCHAR(32),
  downloaded BOOLEAN NOT NULL DEFAULT 0,
  PRIMARY KEY  (id)
);

commit;