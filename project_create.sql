\connect postgres
drop database if exists dbproject ;
create database dbproject with encoding = 'utf8'  template = template0; 
\connect dbproject

CREATE SEQUENCE   USER_ID_SEQUENCE;

CREATE TABLE USERACCOUNT(
    USER_ID          INTEGER          PRIMARY KEY         DEFAULT NEXTVAL('USER_ID_SEQUENCE'),
    EMAIL            VARCHAR(50)      NOT NULL             UNIQUE,
    USERNAME         VARCHAR(20)      NOT NULL,
    PASSWORD         VARCHAR(15)      NOT NULL,
    FIRST_NAME       VARCHAR(15),
    LAST_NAME        VARCHAR(15),
    CITY             VARCHAR(15),
    STATE           VARCHAR(15)    
);

CREATE TABLE FRIENDSHIP(
    USER1_ID           INTEGER               REFERENCES USERACCOUNT(USER_ID),
    USER2_ID           INTEGER               REFERENCES USERACCOUNT(USER_ID),
    STATUS             VARCHAR(10)        CHECK (STATUS IN ('ACCEPTED', 'PENDING')),
    TIME               TIMESTAMP,
    primary key (user1_id, user2_id)
);


CREATE SEQUENCE PICTURE_ID_SEQUENCE;
CREATE TABLE PICTURE(
    PICTURE_ID              INTEGER         PRIMARY KEY         DEFAULT NEXTVAL('PICTURE_ID_SEQUENCE'),
    SOURCE_URL            TEXT,
    URL                   TEXT               NOT NULL,
    USER_ID                    INTEGER          REFERENCES USERACCOUNT(USER_ID),
    TIME                         TIMESTAMP
);

CREATE SEQUENCE TAG_ID_SEQUENCE;
CREATE TABLE TAG(
    TAG_ID              INTEGER                 PRIMARY KEY         DEFAULT NEXTVAL('TAG_ID_SEQUENCE'),
    TAG_NAME        VARCHAR(10)           NOT NULL,
    DESCRIPTION     TEXT
);

CREATE SEQUENCE PINBOARD_ID_SEQUENCE;
CREATE TABLE PINBOARD(
    PINBOARD_ID             INTEGER             PRIMARY KEY         DEFAULT NEXTVAL('PINBOARD_ID_SEQUENCE'),
    PINBOARD_NAME        VARCHAR(10)       NOT NULL,
    USER_ID                     INTEGER              REFERENCES USERACCOUNT(USER_ID),
    DESCRIPTION              TEXT,
    TIME                           TIMESTAMP,
    FRIEND_COMMENT_ONLY     BOOLEAN    
);

CREATE SEQUENCE PIN_ID_SEQUENCE;
CREATE TABLE PIN(
    PIN_ID                  INTEGER                 PRIMARY KEY         DEFAULT NEXTVAL('PIN_ID_SEQUENCE'),
    USER_ID               INTEGER                 REFERENCES USERACCOUNT(USER_ID),
    PINBOARD_ID       INTEGER                 REFERENCES PINBOARD(PINBOARD_ID),
    PICTURE_ID          INTEGER                 REFERENCES  PICTURE(PICTURE_ID),
    TIME                    TIMESTAMP
);

CREATE TABLE PIN_HAS_TAG(
    PIN_ID                 INTEGER                  REFERENCES  PIN(PIN_ID),
    TAG_ID                INTEGER                  REFERENCES  TAG(TAG_ID),
    TIME                   TIMESTAMP, 
    primary key (pin_id, tag_id)
);

CREATE TABLE BOARD_HAS_TAG(
    PINBOARD_ID         INTEGER                 REFERENCES      PINBOARD(PINBOARD_ID),
    TAG_ID                   INTEGER                 REFERENCES      TAG(TAG_ID),
    TIME                      TIMESTAMP,
    primary key (pinboard_id, tag_id)
);

CREATE TABLE LIKEPICTURE(
    USER_ID                 INTEGER                 REFERENCES     USERACCOUNT(USER_ID),
    PICTURE_ID            INTEGER                 REFERENCES      PICTURE(PICTURE_ID),
    TIME                      TIMESTAMP,
    primary key (user_id, picture_id)
);

CREATE SEQUENCE COMMENT_ID_SEQUENCE;

CREATE TABLE COMMENTS(
    COMMENT_ID          INTEGER              PRIMARY KEY        DEFAULT NEXTVAL('COMMENT_ID_SEQUENCE'), 
    PIN_ID                     INTEGER              REFERENCES        PIN(PIN_ID),
    USER_ID                  INTEGER              REFERENCES        USERACCOUNT(USER_ID),
    BODY                      TEXT,
    TIME                       TIMESTAMP
);

CREATE SEQUENCE STREAM_ID_SEQUENCE;
CREATE TABLE STREAM(
    STREAM_ID             INTEGER                PRIMARY KEY         DEFAULT NEXTVAL('STREAM_ID_SEQUENCE'),
    USER_ID                 INTEGER                REFERENCES          USERACCOUNT(USER_ID),
    TIME                      TIMESTAMP         
);


CREATE TABLE FOLLOWPINBOARD(
    PINBOARD_ID             INTEGER             REFERENCES         PINBOARD(PINBOARD_ID),
    STREAM_ID                 INTEGER             REFERENCES         STREAM(STREAM_ID),
    TIME                          TIMESTAMP,
    primary key(pinboard_id, stream_id)
);

CREATE TABLE FOLLOWTAG(
    TAG_ID                      INTEGER             REFERENCES          TAG(TAG_ID),
    STREAM_ID                INTEGER              REFERENCES          STREAM(STREAM_ID),
    TIME                          TIMESTAMP, 
    primary key (tag_id, stream_id)
);
