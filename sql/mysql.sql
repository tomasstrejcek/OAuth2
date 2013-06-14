SET NAMES utf8;

-- Clients table
CREATE TABLE oauth_client (
  id BINARY(16) NOT NULL,
  secret CHAR(64) NOT NULL,
  redirect_url VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
);

-- Create sample client
INSERT INTO oauth_client (id, secret, redirect_url) VALUES
('d3a213ad-d142-11',	'a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12',	'http://localhost/OAuth2/');

-- List of scope
CREATE TABLE oauth_scope (
  name VARCHAR(80) NOT NULL,
  description VARCHAR(255) NOT NULL,
  PRIMARY KEY (name)
);

-- Client roles
CREATE TABLE oauth_grant (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL,
  description VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
);

-- Base client roles
INSERT INTO oauth_grant (id, name, description) VALUES
(1,	'authorization_code',	'Allows to use authroization code grant type'),
(2,	'implicit',	'Allows to use implicit grant type'),
(3,	'password',	'Allows to use password grant type'),
(4,	'refresh_token',	'Allows to use refresh token grant type'),
(5,	'client_credentials',	'Allows to use client credentials grant type');

-- Client grant connect table
CREATE TABLE oauth_client_grant (
  id INT(11) NOT NULL AUTO_INCREMENT,
  client_id BINARY(16) NOT NULL,
  role_id INT(11) NOT NULL,
  PRIMARY KEY (id),
  KEY client_id (client_id),
  KEY role_id (role_id),
  FOREIGN KEY (client_id) REFERENCES oauth_client (id)
    ON DELETE CASCADE,
  FOREIGN KEY (role_id) REFERENCES oauth_role (id)
    ON DELETE CASCADE
);

-- Allow default client to use everything
INSERT INTO oauth_client_grant (id, client_id, role_id) VALUES
(1,	'd3a213ad-d142-11',	1),
(2,	'd3a213ad-d142-11',	2),
(3,	'd3a213ad-d142-11',	3),
(4,	'd3a213ad-d142-11',	4),
(5,	'd3a213ad-d142-11',	5);

-- Sample user table
CREATE TABLE oauth_user (
  id BINARY(16) NOT NULL COMMENT 'UUID',
  username VARCHAR(80) NOT NULL,
  password char(64) NOT NULL COMMENT 'HMAC sha256 hashed password ',
  PRIMARY KEY (id)
);

-- Access token storage table
CREATE TABLE oauth_access_token (
  access_token CHAR(64) NOT NULL,
  client_id BINARY(16) NOT NULL,
  user_id BINARY(16),
  expires DATETIME NOT NULL,
  PRIMARY KEY (access_token),
  KEY client_id (client_id),
  KEY user_id (user_id),
  FOREIGN KEY (client_id) REFERENCES oauth_client (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (user_id) REFERENCES oauth_user (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- Authorization code storage table
CREATE TABLE oauth_authorization_code (
  authorization_code CHAR(64) NOT NULL,
  client_id BINARY(16) NOT NULL,
  user_id BINARY(16) NOT NULL,
  expires DATETIME NOT NULL,
  PRIMARY KEY (authorization_code),
  KEY user_id (user_id),
  KEY client_id (client_id),
  FOREIGN KEY (client_id) REFERENCES oauth_client (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (user_id) REFERENCES oauth_user (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);


-- Refresh token table
CREATE TABLE oauth_refresh_token (
  refresh_token CHAR(64) NOT NULL,
  client_id BINARY(16) NOT NULL,
  user_id BINARY(16),
  expires DATETIME NOT NULL,
  PRIMARY KEY (refresh_token),
  KEY client_id (client_id),
  KEY user_id (user_id),
  FOREIGN KEY (user_id) REFERENCES oauth_user (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (client_id) REFERENCES oauth_client (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- Authorization code scope
CREATE TABLE oauth_authorization_code_scope (
  id INT(11) NOT NULL AUTO_INCREMENT,
  authorization_code CHAR(60) NOT NULL,
  scope_name VARCHAR(80) NOT NULL,
  PRIMARY KEY (id),
  KEY authorization_code (authorization_code),
KEY scope_name (scope_name),
  FOREIGN KEY (authorization_code) REFERENCES oauth_authorization_code (authorization_code)
    ON DELETE CASCADE,
  FOREIGN KEY (scope_name) REFERENCES oauth_scope (name)
    ON DELETE CASCADE
);

-- Access token scope table
CREATE TABLE oauth_access_token_scope (
  id INT(11) NOT NULL AUTO_INCREMENT,
  access_token CHAR(64) NOT NULL,
  scope_name VARCHAR(80) NOT NULL,
  PRIMARY KEY (id),
  KEY access_token (access_token),
  KEY scope_name (scope_name),
  FOREIGN KEY (access_token) REFERENCES oauth_access_token (access_token)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (scope_name) REFERENCES oauth_scope (name)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);