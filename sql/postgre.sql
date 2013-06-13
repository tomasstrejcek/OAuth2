--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: oauth_access_token; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE oauth_access_token (
    access_token character(64) NOT NULL,
    client_id uuid NOT NULL,
    user_id uuid NOT NULL,
    expires date NOT NULL
);


ALTER TABLE public.oauth_access_token OWNER TO postgres;

--
-- Name: oauth_access_token_scope; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE oauth_access_token_scope (
    id integer NOT NULL,
    access_token character(64) NOT NULL,
    scope_name character varying(80) NOT NULL
);


ALTER TABLE public.oauth_access_token_scope OWNER TO postgres;

--
-- Name: oauth_access_token_scope_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE oauth_access_token_scope_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.oauth_access_token_scope_id_seq OWNER TO postgres;

--
-- Name: oauth_access_token_scope_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE oauth_access_token_scope_id_seq OWNED BY oauth_access_token_scope.id;


--
-- Name: oauth_authorization_code; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE oauth_authorization_code (
    authorization_code character(64) NOT NULL,
    client_id uuid NOT NULL,
    user_id uuid NOT NULL,
    expires date NOT NULL
);


ALTER TABLE public.oauth_authorization_code OWNER TO postgres;

--
-- Name: oauth_authorization_code_scope; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE oauth_authorization_code_scope (
    id integer NOT NULL,
    authorization_code character(64) NOT NULL,
    scope_name character varying(80) NOT NULL
);


ALTER TABLE public.oauth_authorization_code_scope OWNER TO postgres;

--
-- Name: oauth_authorization_code_scope_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE oauth_authorization_code_scope_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.oauth_authorization_code_scope_id_seq OWNER TO postgres;

--
-- Name: oauth_authorization_code_scope_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE oauth_authorization_code_scope_id_seq OWNED BY oauth_authorization_code_scope.id;


--
-- Name: oauth_client; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE oauth_client (
    id uuid NOT NULL,
    secret character(64) NOT NULL,
    redirect_url character varying(255) NOT NULL
);


ALTER TABLE public.oauth_client OWNER TO postgres;

--
-- Name: oauth_refresh_token; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE oauth_refresh_token (
    refresh_token character(64) NOT NULL,
    client_id uuid NOT NULL,
    user_id uuid NOT NULL,
    expires date NOT NULL
);


ALTER TABLE public.oauth_refresh_token OWNER TO postgres;

--
-- Name: oauth_scope; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE oauth_scope (
    name character varying(80) NOT NULL,
    description character varying(255) NOT NULL
);


ALTER TABLE public.oauth_scope OWNER TO postgres;

--
-- Name: oauth_user; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE oauth_user (
    id uuid NOT NULL,
    username character varying(80) NOT NULL,
    password character(64) NOT NULL
);


ALTER TABLE public.oauth_user OWNER TO postgres;

--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oauth_access_token_scope ALTER COLUMN id SET DEFAULT nextval('oauth_access_token_scope_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oauth_authorization_code_scope ALTER COLUMN id SET DEFAULT nextval('oauth_authorization_code_scope_id_seq'::regclass);


--
-- Data for Name: oauth_access_token; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY oauth_access_token (access_token, client_id, user_id, expires) FROM stdin;
\.


--
-- Data for Name: oauth_access_token_scope; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY oauth_access_token_scope (id, access_token, scope_name) FROM stdin;
\.


--
-- Name: oauth_access_token_scope_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('oauth_access_token_scope_id_seq', 1, false);


--
-- Data for Name: oauth_authorization_code; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY oauth_authorization_code (authorization_code, client_id, user_id, expires) FROM stdin;
\.


--
-- Data for Name: oauth_authorization_code_scope; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY oauth_authorization_code_scope (id, authorization_code, scope_name) FROM stdin;
\.


--
-- Name: oauth_authorization_code_scope_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('oauth_authorization_code_scope_id_seq', 1, false);


--
-- Data for Name: oauth_client; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY oauth_client (id, secret, redirect_url) FROM stdin;
\.


--
-- Data for Name: oauth_refresh_token; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY oauth_refresh_token (refresh_token, client_id, user_id, expires) FROM stdin;
\.


--
-- Data for Name: oauth_scope; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY oauth_scope (name, description) FROM stdin;
\.


--
-- Data for Name: oauth_user; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY oauth_user (id, username, password) FROM stdin;
\.


--
-- Name: oauth_access_token_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY oauth_access_token
    ADD CONSTRAINT oauth_access_token_pkey PRIMARY KEY (access_token);


--
-- Name: oauth_authorization_code_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY oauth_authorization_code
    ADD CONSTRAINT oauth_authorization_code_pkey PRIMARY KEY (authorization_code);


--
-- Name: oauth_authorization_code_scope_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY oauth_authorization_code_scope
    ADD CONSTRAINT oauth_authorization_code_scope_pkey PRIMARY KEY (id);


--
-- Name: oauth_client_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY oauth_client
    ADD CONSTRAINT oauth_client_pkey PRIMARY KEY (id);


--
-- Name: oauth_refresh_token_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY oauth_refresh_token
    ADD CONSTRAINT oauth_refresh_token_pkey PRIMARY KEY (refresh_token);


--
-- Name: oauth_scope_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY oauth_scope
    ADD CONSTRAINT oauth_scope_pkey PRIMARY KEY (name);


--
-- Name: oauth_user_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY oauth_user
    ADD CONSTRAINT oauth_user_pkey PRIMARY KEY (id);


--
-- Name: oauth_access_token_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oauth_access_token
    ADD CONSTRAINT oauth_access_token_client_id_fkey FOREIGN KEY (client_id) REFERENCES oauth_client(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: oauth_access_token_scope_access_token_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oauth_access_token_scope
    ADD CONSTRAINT oauth_access_token_scope_access_token_fkey FOREIGN KEY (access_token) REFERENCES oauth_access_token(access_token) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: oauth_access_token_scope_scope_name_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oauth_access_token_scope
    ADD CONSTRAINT oauth_access_token_scope_scope_name_fkey FOREIGN KEY (scope_name) REFERENCES oauth_scope(name) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: oauth_access_token_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oauth_access_token
    ADD CONSTRAINT oauth_access_token_user_id_fkey FOREIGN KEY (user_id) REFERENCES oauth_user(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: oauth_authorization_code_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oauth_authorization_code
    ADD CONSTRAINT oauth_authorization_code_client_id_fkey FOREIGN KEY (client_id) REFERENCES oauth_client(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: oauth_authorization_code_scope_authorization_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oauth_authorization_code_scope
    ADD CONSTRAINT oauth_authorization_code_scope_authorization_code_fkey FOREIGN KEY (authorization_code) REFERENCES oauth_authorization_code(authorization_code) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: oauth_authorization_code_scope_scope_name_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oauth_authorization_code_scope
    ADD CONSTRAINT oauth_authorization_code_scope_scope_name_fkey FOREIGN KEY (scope_name) REFERENCES oauth_scope(name) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: oauth_authorization_code_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oauth_authorization_code
    ADD CONSTRAINT oauth_authorization_code_user_id_fkey FOREIGN KEY (user_id) REFERENCES oauth_user(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: oauth_refresh_token_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oauth_refresh_token
    ADD CONSTRAINT oauth_refresh_token_client_id_fkey FOREIGN KEY (client_id) REFERENCES oauth_client(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: oauth_refresh_token_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oauth_refresh_token
    ADD CONSTRAINT oauth_refresh_token_user_id_fkey FOREIGN KEY (user_id) REFERENCES oauth_user(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

