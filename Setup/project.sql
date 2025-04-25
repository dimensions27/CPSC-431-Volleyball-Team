SET FOREIGN_KEY_CHECKS = 0;  -- Turn off foreign key checks while dropping tables allowing tables to be dropped in any order
CREATE OR REPLACE DATABASE  CPSC_431_HW3;
SET FOREIGN_KEY_CHECKS = 1;  -- Turn oit back on

USE CPSC_431_HW3;



CREATE TABLE People
( ID            INTEGER UNSIGNED  NOT NULL    AUTO_INCREMENT  PRIMARY KEY,
  Name_First    VARCHAR(100),
  Name_Last     VARCHAR(150)      NOT NULL,
  Street        VARCHAR(250),
  City          VARCHAR(100),
  State         VARCHAR(100),
  Country       VARCHAR(100),
  ZipCode       CHAR(10),
  LastModified  DATETIME          DEFAULT current_timestamp() ON UPDATE current_timestamp(),

  -- Zip code rules:
  --   5 digits, not all are zero and not all are nine,
  --   optionally followed by a hyphen and 4 digits, not all are zero and not all are nine.
  CHECK (ZipCode REGEXP '(?!0{5})(?!9{5})\\d{5}(-(?!0{4})(?!9{4})\\d{4})?'),

  INDEX  (Name_Last),
  UNIQUE (Name_Last, Name_First)
);

INSERT INTO People( ID, Name_First, Name_Last, Street, City, State, Country, ZipCode ) VALUES
( 100, 'Donald',               'Duck',    '1313 S. Harbor Blvd.',    'Anaheim',            'CA',            'USA',     '92808-3232'),
( 101, 'Daisy',                'Duck',    '1180 Seven Seas Dr.',     'Lake Buena Vista',   'FL',            'USA',     '32830'),
( 107, 'Mickey',               'Mouse',   '1313 S. Harbor Blvd.',    'Anaheim',            'CA',            'USA',     '92808-3232'),
( 111, 'Pluto',                'Dog',     '1313 S. Harbor Blvd.',    'Anaheim',            'CA',            'USA',     '92808-3232'),
( 118, 'Scrooge',              'McDuck',  '1180 Seven Seas Dr.',     'Lake Buena Vista',   'FL',            'USA',     '32830'),

( 119, 'Huebert (Huey)',       'Duck',    '1110 Seven Seas Dr.',     'Lake Buena Vista',   'FL',            'USA',     '32830'),
( 123, 'Deuteronomy (Dewey)',  'Duck',    '1110 Seven Seas Dr.',     'Lake Buena Vista',   'FL',            'USA',     '32830'),
( 128, 'Louie',                'Duck',    '1110 Seven Seas Dr.',     'Lake Buena Vista',   'FL',            'USA',     '32830'),
( 129, 'Phooey',               'Duck',    '1-1 Maihama Urayasu',     'Chiba Prefecture',   'Disney Tokyo',  'Japan',     NULL),
( 131, 'Della',                'Duck',    '77700 Boulevard du Parc', 'Coupvray',           'Disney Paris',  'France',    NULL),

( 140,  NULL,                  'Player',   NULL,                      NULL,                 NULL,            NULL,       NULL),
( 141,  NULL,                  'Coach',    NULL,                      NULL,                 NULL,            NULL,       NULL),
( 142,  NULL,                  'Manager',  NULL,                      NULL,                 NULL,            NULL,       NULL);





CREATE TABLE Statistics
(
  ID                INTEGER    UNSIGNED  NOT NULL  AUTO_INCREMENT PRIMARY KEY,
  Player            INTEGER    UNSIGNED  NOT NULL,
  PlayingTimeMin    TINYINT(2) UNSIGNED  DEFAULT 0 COMMENT 'Two 20-minute halves',
  PlayingTimeSec    TINYINT(2) UNSIGNED  DEFAULT 0,
  Points            TINYINT    UNSIGNED  DEFAULT 0,
  Assists           TINYINT    UNSIGNED  DEFAULT 0,
  Rebounds          TINYINT    UNSIGNED  DEFAULT 0,
  LastModified      DATETIME             DEFAULT current_timestamp() ON UPDATE current_timestamp(),

  FOREIGN KEY (Player) REFERENCES People(ID) ON DELETE CASCADE,

  CHECK((PlayingTimeMin =  0             AND PlayingTimeSec BETWEEN 1 AND 59) OR
        (PlayingTimeMin BETWEEN 1 AND 39 AND PlayingTimeSec BETWEEN 0 AND 59) OR
        (PlayingTimeMin = 40             AND PlayingTimeSec = 0             ))
);

INSERT INTO Statistics( ID, Player, PlayingTimeMin, PlayingTimeSec, Points, Assists, Rebounds ) VALUES
( 17, 100, 35, 12, 47, 11, 21 ),
( 18, 107, 13, 22, 13, 01, 03 ),
( 19, 111, 10, 00, 18, 02, 04 ),
( 20, 128, 02, 45, 09, 01, 02 ),
( 21, 107, 15, 39, 26, 03, 07 ),
( 22, 100, 29, 47, 27, 09, 08 );




-- Map Role Name to Database Account
CREATE TABLE Roles
(
  ID             TINYINT UNSIGNED   AUTO_INCREMENT   PRIMARY KEY,
  Name           VARCHAR(30)        NOT NULL         UNIQUE  COMMENT 'Display name',
  DBAccountName  VARCHAR(512)       NOT NULL         UNIQUE  COMMENT 'Account name',
  LastModified   DATETIME           DEFAULT current_timestamp() ON UPDATE current_timestamp()
);

INSERT INTO Roles( ID, Name, DBAccountName ) VALUES
-- NOTE:  DBAccountName MUST exactly match the Database Users created below
 (1, 'Undefined', 'No_Role'     ),  -- Default Role                 -- Order must match Adaptation.php's Accounts array
 (2, 'Player',    'Player_Role' ),
 (3, 'Coach',     'Coach_Role'  ),
 (4, 'Manager',   'Manager_Role');




CREATE TABLE Accounts
( ID            INTEGER UNSIGNED  NOT NULL    AUTO_INCREMENT  PRIMARY KEY,
  UserName      VARCHAR(30)       NOT NULL                    UNIQUE,
  Password      CHAR(60)          NOT NULL,                                  -- encrypted
  Role          TINYINT UNSIGNED  NOT NULL DEFAULT 1,
  Person        INTEGER UNSIGNED  NOT NULL,
  LastModified  DATETIME          DEFAULT current_timestamp() ON UPDATE current_timestamp(),

  INDEX (Username),

  FOREIGN KEY (Person) REFERENCES People(ID) ON DELETE CASCADE,
  FOREIGN KEY (Role)   REFERENCES Roles (ID) ON DELETE CASCADE
);

-- NOTE:  Run the password_generation.php script after you execute this sql script
--        so the encrypted passwords get populated.  Something like
--          clear && php password_generation.php
--   The password is the user name prefixed with "!", for example the password for
--   donald.duck is "!donald.duck"  (but check password_generation.php)
INSERT INTO Accounts( ID, UserName, Password, Role, Person ) VALUES
  ( 1,  'donald.duck',    '0x0000', 2, 100),
  ( 2,  'daisy.duck',     '0x0000', 2, 101),
  ( 3,  'mickey.mouse',   '0x0000', 3, 107),
  ( 4,  'pluto.dog',      '0x0000', 2, 111),
  ( 5,  'scrooge.mcduck', '0x0000', 4, 118),
  ( 6,  'huey.duck',      '0x0000', 2, 119),
  ( 7,  'dewey.duck',     '0x0000', 2, 123),
  ( 8,  'louie.duck',     '0x0000', 2, 128),
  ( 9,  'phooey.duck',    '0x0000', 2, 129),
  (10,  'della.duck',     '0x0000', 2, 131),

  (11,  'player@gmail.com',  '0x0000', 2, 140),
  (12,  'coach@gmail.com',   '0x0000', 3, 141),
  (13,  'manager@gmail.com', '0x0000', 4, 142);


-- NOTE:
--  If no host is specified then default is all hosts (i.e., '%').  However, 'localhost' is considered a socket connection and not a network
--  connection.  If the specified host is '%' then connecting from 'localhost' will fail IF you haven't removed any anonymous users (e.g. ''@'%' or
--  ''@'localhost').  By default, yes there are anonymous users and you must explicitly remove them.  (Best practice anyway for security reasons)

--  If the CREATE USER (or GRANT that created a user) statement that you ran was executed, but you are denied access when trying
--  to log in, this usually means you have not deleted the anonymous users as part of the installation process. Log back in as
--  root and consult Appendix A for instructions on how to delete the anonymous accounts. You should then be able to log in as the web user.
--
--  Appendix A:
--    # mysql -u root –p
--    Enter password:
--    mysql> use mysql
--    mysql> delete from user where User='';
--    mysql> quit
--
--  You then need to type
--    # mysqladmin -u root -p reload
--  for these changes to take effect.




-- External users (clients) log into the system using their established credentials.  Once authenticated, they are assigned a role.
-- Transactions are performed in the context of the role, not the user.  However, for historical and security reasons, the last user to
-- make changes is saved.

-- DB pseudo Roles (MariaDB allows us to create Roles and then assign Roles to Users, but let's not use that yet)

-- Player Role
CREATE OR REPLACE USER Player_Role@localhost  identified by '!player' PASSWORD EXPIRE NEVER;      -- Must match Adaptation.php
GRANT SELECT,                 UPDATE ON People      to Player_Role@localhost;
GRANT SELECT, INSERT, DELETE, UPDATE ON Statistics  to Player_Role@localhost;
GRANT SELECT                         ON Roles       TO Player_Role@localhost;
GRANT SELECT,                 UPDATE ON Accounts    TO Player_Role@localhost;

-- Coach Role
CREATE OR REPLACE USER Coach_Role@localhost  identified by '!coach' PASSWORD EXPIRE NEVER;        -- Must match Adaptation.php
GRANT SELECT, INSERT, DELETE, UPDATE ON People      to Coach_Role@localhost;
GRANT SELECT,                 UPDATE ON Statistics  to Coach_Role@localhost;
GRANT SELECT                         ON Roles       TO Coach_Role@localhost;
GRANT SELECT,                 UPDATE ON Accounts    TO Coach_Role@localhost;

-- MANAGERS
CREATE OR REPLACE USER Manager_Role@localhost  identified by '!manager' PASSWORD EXPIRE NEVER;    -- Must match Adaptation.php
GRANT SELECT, INSERT, DELETE, UPDATE, EXECUTE ON *  to Manager_Role@localhost;

-- Unknown/Observer Role
CREATE OR REPLACE USER No_Role@localhost  identified by '' PASSWORD EXPIRE NEVER;                 -- Must match Adaptation.php
GRANT SELECT                         ON Roles       TO No_Role@localhost;
GRANT SELECT                         ON Accounts    TO No_Role@localhost;




-- Pipe output to column -t to see aligned columns.  Something like
--  clear && mysql -u root < "HW3 DDL.sql" | column -t
select P.Name_First, P.Name_Last, A.UserName, R.Name as Role
from People   as P,
     Accounts as A,
     Roles    as R
where P.id = A.Person and
      A.Role = R.id;
