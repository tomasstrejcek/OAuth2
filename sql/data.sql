SET NAMES utf8;

-- Create sample client
INSERT INTO oauth_client (id, secret, redirect_url) VALUES
('d3a213ad-d142-11',	'a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12',	'http://localhost/OAuth2/');

-- Base client roles
INSERT INTO oauth_grant (id, name, description) VALUES
(1,	'authorization_code',	'Allows to use authroization code grant type'),
(2,	'implicit',	'Allows to use implicit grant type'),
(3,	'password',	'Allows to use password grant type'),
(4,	'refresh_token',	'Allows to use refresh token grant type'),
(5,	'client_credentials',	'Allows to use client credentials grant type');

-- Allow default client to use everything
INSERT INTO oauth_client_grant (id, client_id, grant_id) VALUES
(1,	'd3a213ad-d142-11',	1),
(2,	'd3a213ad-d142-11',	2),
(3,	'd3a213ad-d142-11',	3),
(4,	'd3a213ad-d142-11',	4),
(5,	'd3a213ad-d142-11',	5);