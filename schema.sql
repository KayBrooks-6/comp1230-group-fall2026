-- COMP1230 Group Project: Study Group Finder
-- Import this file once into YOUR OWN database (phpMyAdmin > your database > Import).
-- It removes and recreates the three tables, so importing it again resets your data.

SET NAMES utf8mb4;

DROP TABLE IF EXISTS memberships;
DROP TABLE IF EXISTS study_groups;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username      VARCHAR(20)  NOT NULL,
    display_name  VARCHAR(50)  NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE study_groups (
    id          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    owner_id    INT UNSIGNED     NOT NULL,
    course_code VARCHAR(10)      NOT NULL,
    title       VARCHAR(80)      NOT NULL,
    description TEXT             NOT NULL,
    meets_at    DATETIME         NOT NULL,
    location    VARCHAR(80)      NOT NULL,
    capacity    TINYINT UNSIGNED NOT NULL,
    created_at  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_study_groups_owner (owner_id),
    CONSTRAINT fk_study_groups_owner FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Used from Phase 2 (joining and leaving). Create it now so the schema never changes.
CREATE TABLE memberships (
    user_id   INT UNSIGNED NOT NULL,
    group_id  INT UNSIGNED NOT NULL,
    joined_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, group_id),
    KEY ix_memberships_group (group_id),
    CONSTRAINT fk_memberships_user  FOREIGN KEY (user_id)  REFERENCES users (id)        ON DELETE CASCADE,
    CONSTRAINT fk_memberships_group FOREIGN KEY (group_id) REFERENCES study_groups (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Test accounts. Both passwords are: Study-1230
-- Your login page must show these two accounts so the instructor can log in.
INSERT INTO users (id, username, display_name, password_hash) VALUES
(1, 'tester1', 'Tester One', '$2y$12$pE4R2U4LuEP1PcocDjcmjeW9JCEo0hxSh1BoJhO9btK6YXkdt5Q3G'),
(2, 'tester2', 'Tester Two', '$2y$12$M.erweNkpq/inLhM20Z5Ee1knVL8.U/hfhy9J5ZpSddt3q/UkErHW');

-- Two sample groups, one owned by each test account.
INSERT INTO study_groups (owner_id, course_code, title, description, meets_at, location, capacity) VALUES
(1, 'COMP1230', 'PDO practice session', 'We work through prepared statements together.', '2026-11-20 15:00:00', 'Library, room 2', 4),
(2, 'COMP1234', 'HTML and CSS review',  'Review forms and layout before the exam.',      '2026-11-24 13:00:00', 'Online', 6);
