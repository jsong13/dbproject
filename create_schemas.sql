\c dbproject
drop schema if exists dbo cascade;
create schema dbo;
set search_path to 'dbo';

CREATE SEQUENCE   USER_ID_SEQUENCE INCREMENT 1 MINVALUE 0;
CREATE TABLE USERACCOUNT(
        USER_ID                 INTEGER              PRIMARY KEY              DEFAULT NEXTVAL('USER_ID_SEQUENCE'),
        EMAIL                     VARCHAR(50)          NOT NULL                 UNIQUE,
        USERNAME               VARCHAR(20)          NOT NULL,
        PASSWORD                    VARCHAR(15)          NOT NULL
);


CREATE TABLE FRIENDSHIP(
        USER1_ID               INTEGER                   REFERENCES USERACCOUNT(USER_ID) ON DELETE CASCADE,
        USER2_ID               INTEGER                   REFERENCES USERACCOUNT(USER_ID) ON DELETE CASCADE,
        STATUS                  VARCHAR(10)               CHECK (STATUS IN ('ACCEPTED','PENDING')),
        TIME                      TIMESTAMP                   DEFAULT CURRENT_TIMESTAMP,
        primary key (user1_id, user2_id)
);


CREATE SEQUENCE PICTURE_ID_SEQUENCE INCREMENT 1 MINVALUE 0;
CREATE TABLE PICTURE(
        PICTURE_ID                 INTEGER               PRIMARY KEY             DEFAULT NEXTVAL('PICTURE_ID_SEQUENCE') ,
        SOURCE_URL              TEXT,
        URL                   TEXT                  NOT NULL,
        USER_ID                     INTEGER               REFERENCES USERACCOUNT(USER_ID) ON DELETE CASCADE,
        TIME                 TIMESTAMP             DEFAULT CURRENT_TIMESTAMP
);


CREATE SEQUENCE TAG_ID_SEQUENCE INCREMENT 1 MINVALUE 0;
CREATE TABLE TAG(
        TAG_ID                      INTEGER                     PRIMARY KEY             DEFAULT NEXTVAL('TAG_ID_SEQUENCE'),
        TAG_NAME                VARCHAR(50)                 NOT NULL,
        DESCRIPTION             TEXT
);


CREATE SEQUENCE PINBOARD_ID_SEQUENCE INCREMENT 1 MINVALUE 0;
CREATE TABLE PINBOARD(
        PINBOARD_ID                 INTEGER                  PRIMARY KEY             DEFAULT NEXTVAL('PINBOARD_ID_SEQUENCE') ,
        PINBOARD_NAME               VARCHAR(50)              NOT NULL,
        USER_ID              INTEGER                  REFERENCES USERACCOUNT(USER_ID) ON DELETE CASCADE,
        DESCRIPTION          TEXT,
        TIME                 TIMESTAMP                DEFAULT CURRENT_TIMESTAMP,
        FRIEND_COMMENT_ONLY         BOOLEAN   DEFAULT  FALSE
);


CREATE SEQUENCE PIN_ID_SEQUENCE INCREMENT 1 MINVALUE 0;
CREATE TABLE PIN(
        PIN_ID                         INTEGER                     PRIMARY KEY             DEFAULT NEXTVAL('PIN_ID_SEQUENCE') ,
        USER_ID                      INTEGER                     REFERENCES USERACCOUNT(USER_ID) ON DELETE CASCADE,
        PINBOARD_ID                     INTEGER                     REFERENCES PINBOARD(PINBOARD_ID) ON DELETE CASCADE,
        PICTURE_ID                    INTEGER                     REFERENCES  PICTURE(PICTURE_ID) ON DELETE CASCADE,
        TIME                           TIMESTAMP                  DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE PIN_HAS_TAG(
        PIN_ID                     INTEGER                       REFERENCES  PIN(PIN_ID)  ON DELETE CASCADE,
        TAG_ID                     INTEGER                      REFERENCES  TAG(TAG_ID) ON DELETE CASCADE,
        TIME                       TIMESTAMP                    DEFAULT CURRENT_TIMESTAMP,
        primary key (pin_id, tag_id)
);


CREATE TABLE BOARD_HAS_TAG(
        PINBOARD_ID                  INTEGER                REFERENCES          PINBOARD(PINBOARD_ID) ON DELETE CASCADE,
        TAG_ID                       INTEGER                     REFERENCES          TAG(TAG_ID) ON DELETE CASCADE,
        TIME                         TIMESTAMP                   DEFAULT CURRENT_TIMESTAMP,
        primary key (pinboard_id, tag_id)
);


CREATE TABLE LIKEPICTURE(
        USER_ID                       INTEGER                     REFERENCES          USERACCOUNT(USER_ID) ON DELETE CASCADE,
        PICTURE_ID                          INTEGER                     REFERENCES          PICTURE(PICTURE_ID) ON DELETE CASCADE,
        TIME                            TIMESTAMP                  DEFAULT CURRENT_TIMESTAMP,
        primary key (user_id, picture_id)
);


CREATE SEQUENCE COMMENT_ID_SEQUENCE INCREMENT 1 MINVALUE 0;


CREATE TABLE COMMENTS(
        COMMENT_ID                     INTEGER                  PRIMARY KEY            DEFAULT NEXTVAL('COMMENT_ID_SEQUENCE'),
        PIN_ID                         INTEGER                  REFERENCES             PIN(PIN_ID) ON DELETE CASCADE,
        USER_ID                        INTEGER                  REFERENCES             USERACCOUNT(USER_ID) ON DELETE CASCADE,
        BODY                           TEXT,
        TIME                           TIMESTAMP                DEFAULT CURRENT_TIMESTAMP    
);


CREATE SEQUENCE STREAM_ID_SEQUENCE;
CREATE TABLE STREAM(
        STREAM_ID                    INTEGER                    PRIMARY KEY             DEFAULT NEXTVAL('STREAM_ID_SEQUENCE') ,
        USER_ID                       INTEGER                    REFERENCES              USERACCOUNT(USER_ID) ON DELETE CASCADE,
        NAME                           TEXT,
        TIME                            TIMESTAMP                  DEFAULT CURRENT_TIMESTAMP
);




CREATE TABLE FOLLOWPINBOARD(
        PINBOARD_ID                  INTEGER                 REFERENCES             PINBOARD(PINBOARD_ID) ON DELETE CASCADE,
        STREAM_ID                     INTEGER                 REFERENCES             STREAM(STREAM_ID) ON DELETE CASCADE,
        TIME                              TIMESTAMP               DEFAULT CURRENT_TIMESTAMP,
        primary key(pinboard_id, stream_id)
);


CREATE TABLE FOLLOWTAG(
        TAG_ID                          INTEGER                  REFERENCES              TAG(TAG_ID) ON DELETE CASCADE,
        STREAM_ID                    INTEGER                  REFERENCES              STREAM(STREAM_ID) ON DELETE CASCADE,
        TIME                              TIMESTAMP                DEFAULT CURRENT_TIMESTAMP,
        primary key (tag_id, stream_id)
);
