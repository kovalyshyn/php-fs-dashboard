--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
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

--
-- Name: fn_cdrupdatebillings(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION fn_cdrupdatebillings() RETURNS trigger
    LANGUAGE plpgsql
    AS $$DECLAREBEGIN 	IF NEW.destination_id IS NOT NULL THEN		IF EXISTS (SELECT "id" FROM "Destinations" as "dest"			WHERE id = NEW.destination_id			AND			(			("progress_before_answer" > 0 AND NEW.billsec > 0 AND "progress_before_answer" > NEW.progress_mediasec)			OR			("progress_without_answer" > 0 AND NEW.billsec = 0 AND "progress_without_answer" > NEW.progress_mediasec)			OR			("progress_no_answer" IS TRUE AND NEW.billsec = 0 AND NEW.progress_mediasec > 0)			OR			("numA" = false AND "numB" = false)			) 			AND 			( 				(					("dest"."numA" = true) 					AND					( 						(select count("uuid") from cdr as "cdr" where 					cdr."start_stamp"::TIMESTAMP > (now() - "dest"."repeat_calls_minutes" * INTERVAL '1 minute')  					AND					cdr."caller_id_number" like NEW.caller_id_number						) >= "dest"."repeat_calls"					)				)				OR 				(					("dest"."numB" = true)					AND					( 						(select count("uuid") from cdr as "cdr" where 					cdr."start_stamp"::TIMESTAMP > (now() - "dest"."repeat_calls_minutes" * INTERVAL '1 minute') 					AND					cdr."destination_number" like NEW.destination_number						) >= "dest"."repeat_calls"					)				) 			)		) THEN			IF EXISTS (SELECT "id" FROM "Destinations" WHERE id = NEW.destination_id AND "numA" = true) THEN				INSERT INTO "NumberList" ( "caller_id_number", "added", "destinations","whitelist", "description") 				VALUES ( NEW.caller_id_number, NOW(), NEW.destination_id, false, 'ProgressMedia: ' || NEW.progress_mediasec || ' and Billsec: ' || NEW.billsec );			END IF;			IF EXISTS (SELECT "id" FROM "Destinations" WHERE id = NEW.destination_id AND "numB" = true) THEN				INSERT INTO "NumberList" ( "callee_id_number", "added", "destinations","whitelist", "description") 				VALUES ( NEW.destination_number, NOW(), NEW.destination_id, false, 'ProgressMedia: ' || NEW.progress_mediasec || ' and Billsec: ' || NEW.billsec );			END IF;		END IF;	END IF;	-- update values    IF NEW.billsec > 0 THEN		        IF NEW.context = 'public' THEN        --UPDATE "cdr" SET         --    "rate_user" = ("cdr"."billsec" * (SELECT "Destinations"."rate_user" FROM "Destinations" WHERE "Destinations"."id" = "cdr"."destination_id")/60),        --    "rate_agent" = ("cdr"."billsec" * (SELECT "Destinations"."rate_agent" FROM "Destinations" WHERE "Destinations"."id" = "cdr"."destination_id")/60),        --    "rate_admin" = ("cdr"."billsec" * (SELECT "Destinations"."rate_admin" FROM "Destinations" WHERE "Destinations"."id" = "cdr"."destination_id")/60)        --    WHERE "uuid" = NEW.uuid;                UPDATE "Getaways" SET "connected" = now(), "selected" = now(), "last_hangup_cause" = NEW.hangup_cause WHERE "Getaways"."id" = NEW.gw_id;                            ELSE                 --UPDATE "cdr" SET         --    "rate_user" = ("cdr"."billsec" * (SELECT "fas"."rate_user" FROM "fas" WHERE "fas"."id" = "cdr"."destination_id")/60),        --    "rate_agent" = ("cdr"."billsec" * (SELECT "fas"."rate_agent" FROM "fas" WHERE "fas"."id" = "cdr"."destination_id")/60),        --    "rate_admin" = ("cdr"."billsec" * (SELECT "fas"."rate_admin" FROM "fas" WHERE "fas"."id" = "cdr"."destination_id")/60)         --    WHERE "uuid" = NEW.uuid;        			IF (select delay_all from "Getaways" where id = NEW.gw_id) > 0 THEN				UPDATE "Getaways" SET "connected" = now(), "selected" = now(), "last_hangup_cause" = NEW.hangup_cause WHERE "Getaways"."id" = NEW.gw_id;			ELSE				UPDATE "Getaways" SET "selected" = now(), "last_hangup_cause" = NEW.hangup_cause WHERE "Getaways"."id" = NEW.gw_id;			END IF;                END IF;	END IF;    RETURN new;END;$$;


ALTER FUNCTION public.fn_cdrupdatebillings() OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: Getaways; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Getaways" (
    id integer NOT NULL,
    user_id integer NOT NULL,
    mask character varying(2044),
    ip inet NOT NULL,
    port integer NOT NULL,
    type_id integer NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    destinations integer NOT NULL,
    active smallint NOT NULL,
    selected timestamp with time zone DEFAULT now() NOT NULL,
    parent_id integer,
    "limit" integer,
    sip_profile character varying(2044) DEFAULT 'openvpn'::character varying,
    delay integer,
    minutes integer,
    imei character varying(20),
    delay_rnd smallint DEFAULT (0)::smallint,
    last_hangup_cause character varying(2044),
    delay_from integer,
    delay_to integer,
    concurrent integer DEFAULT 1 NOT NULL,
    connected timestamp with time zone,
    bridge_string character varying(2044),
    call_timeout integer,
    delay_all smallint DEFAULT (0)::smallint NOT NULL
);


ALTER TABLE "Getaways" OWNER TO postgres;

--
-- Name: cdr; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE cdr (
    caller_id_name character varying(2044) NOT NULL,
    caller_id_number character varying(2044) NOT NULL,
    destination_number character varying(2044) NOT NULL,
    context character varying(2044) NOT NULL,
    start_stamp character varying(2044) NOT NULL,
    answer_stamp character varying(2044),
    end_stamp character varying(2044) NOT NULL,
    duration integer,
    billsec integer,
    hangup_cause character varying(2044) NOT NULL,
    uuid character varying(2044) NOT NULL,
    read_codec character varying(2044),
    write_codec character varying(2044),
    sip_hangup_disposition character varying(2044) NOT NULL,
    ani character varying(2044),
    gw_id integer,
    destination_id integer,
    rate_user numeric(5,4) DEFAULT (0)::numeric,
    rate_agent numeric(5,4) DEFAULT (0)::numeric,
    rate_admin numeric(5,4) DEFAULT (0)::numeric,
    user_id integer,
    progress_media_stamp character varying(2044),
    progress_mediasec integer,
    waitsec integer
);


ALTER TABLE cdr OWNER TO postgres;

--
-- Name: vw_gw; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW vw_gw AS
 SELECT "Getaways".id,
    "Getaways".ip,
    "Getaways".mask,
    "Getaways".port,
    "Getaways".sip_profile,
    "Getaways".type_id,
    "Getaways".user_id,
    "Getaways".delay_rnd,
    "Getaways".delay_from,
    "Getaways".delay_to,
    "Getaways".concurrent,
    "Getaways".destinations,
    "Getaways".selected,
    "Getaways".connected,
    "Getaways".bridge_string,
    "Getaways".call_timeout
   FROM "Getaways"
  WHERE (((("Getaways".active = 1) AND (( SELECT count(cdr.answer_stamp) AS count
           FROM cdr
          WHERE (((cdr.answer_stamp)::date = (now())::date) AND (cdr.gw_id = "Getaways".id))) <= "Getaways"."limit")) AND (( SELECT COALESCE(sum(cdr1.billsec), (0)::bigint) AS "coalesce"
           FROM cdr cdr1
          WHERE (((cdr1.answer_stamp)::date = (now())::date) AND (cdr1.gw_id = "Getaways".id))) <= ("Getaways".minutes * 60))) AND ((( SELECT ((((((date_part('day'::text, (now() - (("Getaways".connected)::timestamp without time zone)::timestamp with time zone)) * (24)::double precision) + date_part('hour'::text, (now() - (("Getaways".connected)::timestamp without time zone)::timestamp with time zone))) * (60)::double precision) + date_part('minute'::text, (now() - (("Getaways".connected)::timestamp without time zone)::timestamp with time zone))) * (60)::double precision) + date_part('second'::text, (now() - (("Getaways".connected)::timestamp without time zone)::timestamp with time zone)))) >= ("Getaways".delay)::double precision) OR (( SELECT count(cdr3.answer_stamp) AS count
           FROM cdr cdr3
          WHERE (cdr3.gw_id = "Getaways".id)) = 0)))
  ORDER BY "Getaways".selected;


ALTER TABLE vw_gw OWNER TO postgres;

--
-- Name: select_and_update_gw(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION select_and_update_gw(parameter integer, integer) RETURNS SETOF vw_gw
    LANGUAGE sql
    AS $_$    UPDATE "Getaways"    SET "selected" = now()    FROM (       SELECT "id" 
       FROM vw_gw 
       WHERE destinations = $1       LIMIT $2        ) vw    where "Getaways".id = vw.id	    ;    SELECT  *
    FROM vw_gw        WHERE destinations = $1       LIMIT $2    ;$_$;


ALTER FUNCTION public.select_and_update_gw(parameter integer, integer) OWNER TO postgres;

--
-- Name: Destinations; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Destinations" (
    global_prefix character varying(2044) NOT NULL,
    local_prefix character varying(2044) NOT NULL,
    name character varying(2044) NOT NULL,
    id integer NOT NULL,
    number_length integer DEFAULT 10,
    active smallint DEFAULT (1)::smallint NOT NULL,
    updated_at timestamp without time zone DEFAULT now(),
    created_at timestamp without time zone DEFAULT now(),
    show_getaways integer DEFAULT 1 NOT NULL,
    ussd_balance character varying(2044),
    ussd_balance_pattern character varying(2044),
    rate_user numeric(5,4) DEFAULT (0)::numeric,
    rate_agent numeric(5,4) DEFAULT (0)::numeric,
    rate_admin numeric(5,4) DEFAULT (0)::numeric,
    agent_prefix character varying(2044),
    user_id integer,
    add_plus smallint DEFAULT (1)::smallint NOT NULL,
    del_prefix smallint DEFAULT (0)::smallint NOT NULL,
    progress_before_answer integer,
    progress_without_answer integer,
    progress_no_answer boolean DEFAULT false,
    "numA" boolean DEFAULT false NOT NULL,
    "numB" boolean DEFAULT false NOT NULL,
    repeat_calls_minutes integer DEFAULT 0,
    repeat_calls integer DEFAULT 0
);


ALTER TABLE "Destinations" OWNER TO postgres;

--
-- Name: Destinations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "Destinations_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE "Destinations_id_seq" OWNER TO postgres;

--
-- Name: Destinations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "Destinations_id_seq" OWNED BY "Destinations".id;


--
-- Name: Dialer; Type: TABLE; Schema: public; Owner: switch; Tablespace: 
--

CREATE TABLE "Dialer" (
    id integer NOT NULL,
    concurrent_calls integer NOT NULL,
    total_calls integer NOT NULL,
    durations integer NOT NULL,
    destination_srv character varying(2044) NOT NULL,
    source_num character varying(2044) NOT NULL,
    wait_answer boolean NOT NULL,
    done boolean NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    started_at timestamp with time zone,
    pause_between_rounds integer NOT NULL
);


ALTER TABLE "Dialer" OWNER TO switch;

--
-- Name: DialerB; Type: TABLE; Schema: public; Owner: switch; Tablespace: 
--

CREATE TABLE "DialerB" (
    id integer NOT NULL,
    dialer_id integer NOT NULL,
    number character varying(2044) NOT NULL
);


ALTER TABLE "DialerB" OWNER TO switch;

--
-- Name: DialerB_id_seq; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE "DialerB_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE "DialerB_id_seq" OWNER TO switch;

--
-- Name: DialerB_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: switch
--

ALTER SEQUENCE "DialerB_id_seq" OWNED BY "DialerB".id;


--
-- Name: Dialer_id_seq; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE "Dialer_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE "Dialer_id_seq" OWNER TO switch;

--
-- Name: Dialer_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: switch
--

ALTER SEQUENCE "Dialer_id_seq" OWNED BY "Dialer".id;


--
-- Name: Gateways_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "Gateways_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE "Gateways_id_seq" OWNER TO postgres;

--
-- Name: Gateways_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "Gateways_id_seq" OWNED BY "Getaways".id;


--
-- Name: GetawayType; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "GetawayType" (
    id integer NOT NULL,
    name character varying(2044) NOT NULL
);


ALTER TABLE "GetawayType" OWNER TO postgres;

--
-- Name: GetawayType_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "GetawayType_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE "GetawayType_id_seq" OWNER TO postgres;

--
-- Name: GetawayType_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "GetawayType_id_seq" OWNED BY "GetawayType".id;


--
-- Name: GsmBalance; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "GsmBalance" (
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    balance numeric NOT NULL,
    imei character varying(2044) NOT NULL
);


ALTER TABLE "GsmBalance" OWNER TO postgres;

--
-- Name: NumberList; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "NumberList" (
    id integer NOT NULL,
    caller_id_number character varying(2044),
    whitelist boolean DEFAULT false NOT NULL,
    description character varying(2044),
    destinations integer NOT NULL,
    added timestamp without time zone NOT NULL,
    callee_id_number character varying(2044)
);


ALTER TABLE "NumberList" OWNER TO postgres;

--
-- Name: NumberList_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "NumberList_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE "NumberList_id_seq" OWNER TO postgres;

--
-- Name: NumberList_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "NumberList_id_seq" OWNED BY "NumberList".id;


--
-- Name: SwitchUsers; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "SwitchUsers" (
    id integer NOT NULL,
    email character varying(2044) NOT NULL,
    password character varying(2044) NOT NULL,
    name character varying(2044) NOT NULL,
    type integer DEFAULT 2 NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    parent_id integer DEFAULT 0,
    remember_token character varying(2044)
);


ALTER TABLE "SwitchUsers" OWNER TO postgres;

--
-- Name: COLUMN "SwitchUsers".type; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN "SwitchUsers".type IS '0 - root
1 - agent
2 - user';


--
-- Name: SwitchUsers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "SwitchUsers_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE "SwitchUsers_id_seq" OWNER TO postgres;

--
-- Name: SwitchUsers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "SwitchUsers_id_seq" OWNED BY "SwitchUsers".id;


--
-- Name: fas; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE fas (
    id integer NOT NULL,
    global_prefix character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    number_length integer DEFAULT 10,
    active smallint NOT NULL,
    before_ansfer integer DEFAULT 10,
    after_ansfer integer DEFAULT 10,
    tone_stream character varying(255) NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    rate_user numeric(5,4) DEFAULT (0)::numeric,
    rate_agent numeric(5,4) DEFAULT (0)::numeric,
    rate_admin numeric(5,4) DEFAULT (0)::numeric,
    before_ansfer_from integer,
    before_ansfer_to integer,
    tone_stream_duration integer,
    recording_file character varying(2044),
    random_pdd boolean DEFAULT false
);


ALTER TABLE fas OWNER TO postgres;

--
-- Name: fas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE fas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE fas_id_seq OWNER TO postgres;

--
-- Name: fas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE fas_id_seq OWNED BY fas.id;


--
-- Name: news; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE news (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    the_news text NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL
);


ALTER TABLE news OWNER TO postgres;

--
-- Name: news_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE news_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE news_id_seq OWNER TO postgres;

--
-- Name: news_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE news_id_seq OWNED BY news.id;


--
-- Name: registrations; Type: TABLE; Schema: public; Owner: postgres; Tablespace:
--

CREATE TABLE registrations (
    reg_user character varying(256),
    realm character varying(256),
    token character varying(256),
    url text,
    expires integer,
    network_ip character varying(256),
    network_port character varying(256),
    network_proto character varying(256),
    hostname character varying(256),
    metadata character varying(256)
);


ALTER TABLE registrations OWNER TO postgres;

--
-- Name: sip_profiles; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE sip_profiles (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    ip character varying(255) NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL
);


ALTER TABLE sip_profiles OWNER TO postgres;

--
-- Name: sip_profiles_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE sip_profiles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sip_profiles_id_seq OWNER TO postgres;

--
-- Name: sip_profiles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE sip_profiles_id_seq OWNED BY sip_profiles.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Destinations" ALTER COLUMN id SET DEFAULT nextval('"Destinations_id_seq"'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: switch
--

ALTER TABLE ONLY "Dialer" ALTER COLUMN id SET DEFAULT nextval('"Dialer_id_seq"'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: switch
--

ALTER TABLE ONLY "DialerB" ALTER COLUMN id SET DEFAULT nextval('"DialerB_id_seq"'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "GetawayType" ALTER COLUMN id SET DEFAULT nextval('"GetawayType_id_seq"'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Getaways" ALTER COLUMN id SET DEFAULT nextval('"Gateways_id_seq"'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "NumberList" ALTER COLUMN id SET DEFAULT nextval('"NumberList_id_seq"'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "SwitchUsers" ALTER COLUMN id SET DEFAULT nextval('"SwitchUsers_id_seq"'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY fas ALTER COLUMN id SET DEFAULT nextval('fas_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY news ALTER COLUMN id SET DEFAULT nextval('news_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY sip_profiles ALTER COLUMN id SET DEFAULT nextval('sip_profiles_id_seq'::regclass);


--
-- Data for Name: GetawayType; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY "GetawayType" (id, name) FROM stdin;
1	GoIP
2	SipGsm
3	SIP Gateway
\.


--
-- Name: GetawayType_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('"GetawayType_id_seq"', 3, true);

--
-- Data for Name: SwitchUsers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY "SwitchUsers" (id, email, password, name, type, created_at, updated_at, parent_id, remember_token) FROM stdin;
1	support@hd-worldwide.com	$2y$10$Nhutv4gG23Rqa/0Z6U5SqOpnT9PpPfFZeiUUT6hqih.CUhBhSq8..	support@hd-worldwide.com	0	2014-05-28 04:35:43+00	2014-06-19 06:43:38+00	0	\N
\.


--
-- Name: SwitchUsers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('"SwitchUsers_id_seq"', 2, true);


--
-- Data for Name: sip_profiles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY sip_profiles (id, name, ip, created_at, updated_at) FROM stdin;
1	openvpn	10.8.0.1	2015-04-18 04:47:56.36028	2015-04-18 04:47:56.36028
\.


--
-- Name: sip_profiles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('sip_profiles_id_seq', 2, false);


--
-- Name: Destinations_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Destinations"
    ADD CONSTRAINT "Destinations_id_key" UNIQUE (id);


--
-- Name: DialerB_pkey; Type: CONSTRAINT; Schema: public; Owner: switch; Tablespace: 
--

ALTER TABLE ONLY "DialerB"
    ADD CONSTRAINT "DialerB_pkey" PRIMARY KEY (id);


--
-- Name: Dialer_pkey; Type: CONSTRAINT; Schema: public; Owner: switch; Tablespace: 
--

ALTER TABLE ONLY "Dialer"
    ADD CONSTRAINT "Dialer_pkey" PRIMARY KEY (id);


--
-- Name: Gateways_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Getaways"
    ADD CONSTRAINT "Gateways_id_key" UNIQUE (id);


--
-- Name: GetawayType_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "GetawayType"
    ADD CONSTRAINT "GetawayType_pkey" PRIMARY KEY (id);


--
-- Name: Getaways_imei_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Getaways"
    ADD CONSTRAINT "Getaways_imei_key" UNIQUE (imei);


--
-- Name: NumberList_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "NumberList"
    ADD CONSTRAINT "NumberList_pkey" PRIMARY KEY (id);


--
-- Name: fas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY fas
    ADD CONSTRAINT fas_pkey PRIMARY KEY (id);


--
-- Name: news_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY news
    ADD CONSTRAINT news_pkey PRIMARY KEY (id);


--
-- Name: sip_profiles_name_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY sip_profiles
    ADD CONSTRAINT sip_profiles_name_key UNIQUE (name);


--
-- Name: sip_profiles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY sip_profiles
    ADD CONSTRAINT sip_profiles_pkey PRIMARY KEY (id);


--
-- Name: unique_Id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "SwitchUsers"
    ADD CONSTRAINT "unique_Id" PRIMARY KEY (id);


--
-- Name: unique_email; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "SwitchUsers"
    ADD CONSTRAINT unique_email UNIQUE (email);


--
-- Name: unique_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Destinations"
    ADD CONSTRAINT unique_id PRIMARY KEY (id);


--
-- Name: unique_imei; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Getaways"
    ADD CONSTRAINT unique_imei UNIQUE (imei);


--
-- Name: DestinationsIdx_number_length; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX "DestinationsIdx_number_length" ON "Destinations" USING btree (number_length);


--
-- Name: Destinations_agent_prefix_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX "Destinations_agent_prefix_idx" ON "Destinations" USING btree (agent_prefix);


--
-- Name: NumberListIdx_destinations; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX "NumberListIdx_destinations" ON "NumberList" USING btree (destinations);


--
-- Name: NumberList_caller_id_number_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX "NumberList_caller_id_number_idx" ON "NumberList" USING btree (caller_id_number);


--
-- Name: cdr_caller_id_number_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX cdr_caller_id_number_idx ON cdr USING btree (caller_id_number);


--
-- Name: index_Id; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX "index_Id" ON "SwitchUsers" USING btree (id);


--
-- Name: index_callee_id_number; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX index_callee_id_number ON "NumberList" USING btree (callee_id_number);


--
-- Name: index_destination_id; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX index_destination_id ON cdr USING btree (destination_id);


--
-- Name: index_destination_number; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX index_destination_number ON cdr USING btree (destination_number);


--
-- Name: index_email; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX index_email ON "SwitchUsers" USING btree (email);


--
-- Name: index_global_prefix; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX index_global_prefix ON "Destinations" USING btree (global_prefix);


--
-- Name: index_gw_id; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX index_gw_id ON cdr USING btree (gw_id);


--
-- Name: index_id; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX index_id ON "Destinations" USING btree (id);


--
-- Name: index_id1; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX index_id1 ON "Getaways" USING btree (id);


--
-- Name: index_id2; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX index_id2 ON "GetawayType" USING btree (id);


--
-- Name: index_imei; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX index_imei ON "GsmBalance" USING btree (imei);


--
-- Name: index_imei1; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX index_imei1 ON "Getaways" USING btree (imei);


--
-- Name: index_start_stamp; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX index_start_stamp ON cdr USING btree (start_stamp);


--
-- Name: tr_AI_UpdateBilling; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER "tr_AI_UpdateBilling" AFTER INSERT ON cdr FOR EACH ROW EXECUTE PROCEDURE fn_cdrupdatebillings();


--
-- Name: destinations; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Getaways"
    ADD CONSTRAINT destinations FOREIGN KEY (destinations) REFERENCES "Destinations"(id);


--
-- Name: type_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Getaways"
    ADD CONSTRAINT type_id FOREIGN KEY (type_id) REFERENCES "GetawayType"(id);


--
-- Name: user_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Getaways"
    ADD CONSTRAINT user_id FOREIGN KEY (user_id) REFERENCES "SwitchUsers"(id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: Getaways; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE "Getaways" FROM PUBLIC;
REVOKE ALL ON TABLE "Getaways" FROM postgres;
GRANT ALL ON TABLE "Getaways" TO postgres;


--
-- Name: cdr; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE cdr FROM PUBLIC;
REVOKE ALL ON TABLE cdr FROM postgres;
GRANT ALL ON TABLE cdr TO postgres;


--
-- Name: Destinations; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE "Destinations" FROM PUBLIC;
REVOKE ALL ON TABLE "Destinations" FROM postgres;
GRANT ALL ON TABLE "Destinations" TO postgres;


--
-- Name: Destinations_id_seq; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON SEQUENCE "Destinations_id_seq" FROM PUBLIC;
REVOKE ALL ON SEQUENCE "Destinations_id_seq" FROM postgres;
GRANT ALL ON SEQUENCE "Destinations_id_seq" TO postgres;


--
-- Name: Gateways_id_seq; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON SEQUENCE "Gateways_id_seq" FROM PUBLIC;
REVOKE ALL ON SEQUENCE "Gateways_id_seq" FROM postgres;
GRANT ALL ON SEQUENCE "Gateways_id_seq" TO postgres;


--
-- Name: GetawayType; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE "GetawayType" FROM PUBLIC;
REVOKE ALL ON TABLE "GetawayType" FROM postgres;
GRANT ALL ON TABLE "GetawayType" TO postgres;


--
-- Name: GetawayType_id_seq; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON SEQUENCE "GetawayType_id_seq" FROM PUBLIC;
REVOKE ALL ON SEQUENCE "GetawayType_id_seq" FROM postgres;
GRANT ALL ON SEQUENCE "GetawayType_id_seq" TO postgres;


--
-- Name: GsmBalance; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE "GsmBalance" FROM PUBLIC;
REVOKE ALL ON TABLE "GsmBalance" FROM postgres;
GRANT ALL ON TABLE "GsmBalance" TO postgres;
GRANT ALL ON TABLE "GsmBalance" TO PUBLIC;


--
-- Name: NumberList; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE "NumberList" FROM PUBLIC;
REVOKE ALL ON TABLE "NumberList" FROM postgres;
GRANT ALL ON TABLE "NumberList" TO postgres;


--
-- Name: NumberList_id_seq; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON SEQUENCE "NumberList_id_seq" FROM PUBLIC;
REVOKE ALL ON SEQUENCE "NumberList_id_seq" FROM postgres;
GRANT ALL ON SEQUENCE "NumberList_id_seq" TO postgres;


--
-- Name: SwitchUsers; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE "SwitchUsers" FROM PUBLIC;
REVOKE ALL ON TABLE "SwitchUsers" FROM postgres;
GRANT ALL ON TABLE "SwitchUsers" TO postgres;


--
-- Name: SwitchUsers_id_seq; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON SEQUENCE "SwitchUsers_id_seq" FROM PUBLIC;
REVOKE ALL ON SEQUENCE "SwitchUsers_id_seq" FROM postgres;
GRANT ALL ON SEQUENCE "SwitchUsers_id_seq" TO postgres;


--
-- Name: fas; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE fas FROM PUBLIC;
REVOKE ALL ON TABLE fas FROM postgres;
GRANT ALL ON TABLE fas TO postgres;


--
-- Name: news; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE news FROM PUBLIC;
REVOKE ALL ON TABLE news FROM postgres;
GRANT ALL ON TABLE news TO postgres;


--
-- Name: news_id_seq; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON SEQUENCE news_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE news_id_seq FROM postgres;
GRANT ALL ON SEQUENCE news_id_seq TO postgres;


--
-- PostgreSQL database dump complete
--

