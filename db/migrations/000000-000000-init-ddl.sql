CREATE EXTENSION btree_gist;
CREATE EXTENSION unaccent;

CREATE SCHEMA conia;
CREATE SCHEMA audit;


CREATE FUNCTION conia.update_changed_column() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
   NEW.changed = now();
   RETURN NEW;
END;
$$;


CREATE TABLE conia.userroles (
    userrole text NOT NULL,
    CONSTRAINT pk_userroles PRIMARY KEY (userrole)
);


CREATE TABLE conia.users (
    usr integer GENERATED ALWAYS AS IDENTITY,
    uid text NOT NULL CHECK (char_length(uid) = 13),
    username text CHECK (username NOT SIMILAR TO '%@%' AND char_length(username) > 0),
    email text CHECK (email SIMILAR TO '%@%' AND char_length(email) > 5),
    display text,
    pwhash text NOT NULL,
    userrole text NOT NULL,
    active boolean NOT NULL,
    creator integer NOT NULL,
    editor integer NOT NULL,
    created timestamp with time zone NOT NULL DEFAULT now(),
    changed timestamp with time zone NOT NULL DEFAULT now(),
    deleted timestamp with time zone,
    CONSTRAINT pk_users PRIMARY KEY (usr),
    CONSTRAINT uc_users_uid UNIQUE (uid),
    CONSTRAINT fk_users_users_creator FOREIGN KEY (creator)
        REFERENCES conia.users (usr),
    CONSTRAINT fk_users_userroles FOREIGN KEY (userrole)
        REFERENCES conia.userroles (userrole) ON UPDATE CASCADE,
    CONSTRAINT fk_users_users_editor FOREIGN KEY (editor)
        REFERENCES conia.users (usr)
);
CREATE UNIQUE INDEX uix_users_username ON conia.users
    USING btree (lower(username)) WHERE (deleted IS NULL AND username IS NOT NULL);
CREATE UNIQUE INDEX uix_users_email ON conia.users
    USING btree (lower(email)) WHERE (deleted IS NULL AND email IS NOT NULL);
CREATE OR REPLACE FUNCTION conia.process_users_audit()
    RETURNS TRIGGER AS $$
BEGIN
    IF (TG_OP = 'UPDATE') THEN
        INSERT INTO audit.users (
            usr, username, email, display, pwhash,
            userrole, active, editor, changed, deleted
        ) VALUES (
            OLD.usr, OLD.username, OLD.email, OLD.display, OLD.pwhash,
            OLD.userrole, OLD.active, OLD.editor, OLD.changed, OLD.deleted
        );
        RETURN OLD;
    END IF;

    RETURN NULL;
EXCEPTION WHEN unique_violation THEN
    RAISE WARNING 'duplicate users audit row skipped. user: %, changed: %', OLD.usr, OLD.changed;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER update_users_01_changed_trigger BEFORE UPDATE ON conia.users
    FOR EACH ROW EXECUTE FUNCTION conia.update_changed_column();
CREATE TRIGGER update_users_02_audit_trigger AFTER UPDATE
    ON conia.users FOR EACH ROW EXECUTE PROCEDURE
    conia.process_users_audit();


CREATE TABLE conia.loginsessions (
    hash text NOT NULL,
    uid text NOT NULL CHECK (char_length(uid) = 13),
    expires timestamp with time zone NOT NULL,
    CONSTRAINT pk_loginsessions PRIMARY KEY (hash),
    CONSTRAINT uc_loginsessions_uid UNIQUE (uid),
    CONSTRAINT fk_loginsessions_users FOREIGN KEY (uid) REFERENCES conia.users(uid),
    CONSTRAINT ck_loginsessions_hash CHECK (char_length(hash) <= 254)
);


CREATE TABLE conia.pagetypes (
    pagetype integer GENERATED ALWAYS AS IDENTITY,
    classname text NOT NULL CHECK (char_length(classname) <= 256),
    CONSTRAINT pk_pagetypes PRIMARY KEY (pagetype),
    CONSTRAINT uc_pagestypes_classname UNIQUE (classname)
);


CREATE TABLE conia.pages (
    page integer GENERATED ALWAYS AS IDENTITY,
    uid text NOT NULL CHECK (char_length(uid) = 13),
    published boolean DEFAULT false NOT NULL,
    hidden boolean DEFAULT false NOT NULL,
    locked boolean DEFAULT false NOT NULL,
    pagetype integer NOT NULL,
    creator integer NOT NULL,
    editor integer NOT NULL,
    created timestamp with time zone NOT NULL DEFAULT now(),
    changed timestamp with time zone NOT NULL DEFAULT now(),
    deleted timestamp with time zone,
    content jsonb NOT NULL,
    tsv tsvector NOT NULL GENERATED ALWAYS AS (jsonb_to_tsvector('simple', content, '["string"]')) STORED,
    CONSTRAINT pk_pages PRIMARY KEY (page),
    CONSTRAINT uc_pages_uid UNIQUE (uid),
    CONSTRAINT fk_pages_users_creator FOREIGN KEY (creator)
        REFERENCES conia.users (usr),
    CONSTRAINT fk_pages_users_editor FOREIGN KEY (editor)
        REFERENCES conia.users (usr),
    CONSTRAINT fk_pages_pagetypes FOREIGN KEY (pagetype)
        REFERENCES conia.pagetypes (pagetype) ON UPDATE CASCADE ON DELETE NO ACTION
);
CREATE INDEX ix_pages_tsv ON conia.pages USING GIN(tsv);
CREATE OR REPLACE FUNCTION conia.process_pages_audit()
    RETURNS TRIGGER AS $$
BEGIN
    IF (TG_OP = 'UPDATE') THEN
        INSERT INTO audit.pages (
            page, changed, published, hidden, locked,
            pagetype, editor, deleted, content
        ) VALUES (
            OLD.page, OLD.changed, OLD.published, OLD.hidden, OLD.locked,
            OLD.pagetype, OLD.editor, OLD.deleted, OLD.content
        );
        RETURN OLD;
    END IF;

    RETURN NULL;
EXCEPTION WHEN unique_violation THEN
    RAISE WARNING 'duplicate pages audit row skipped. page: %, changed: %', OLD.page, OLD.changed;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER update_pages_01_changed_trigger BEFORE UPDATE ON conia.pages
    FOR EACH ROW EXECUTE FUNCTION conia.update_changed_column();
CREATE TRIGGER update_pages_02_audit_trigger AFTER UPDATE
    ON conia.pages FOR EACH ROW EXECUTE PROCEDURE
    conia.process_pages_audit();


CREATE TABLE conia.urls (
    page integer NOT NULL,
    lang text NOT NULL CHECK (char_length(lang) <= 32),
    url text NOT NULL CHECK (char_length(url) <= 512),
    inactive timestamp with time zone,
    CONSTRAINT pk_urls PRIMARY KEY (page, lang, url),
    CONSTRAINT fk_urls_pages FOREIGN KEY (page)
        REFERENCES conia.pages (page)
);
CREATE UNIQUE INDEX uix_urls_url ON conia.urls
    USING btree (lang, lower(url)) WHERE (inactive IS NULL);



CREATE TABLE conia.drafts (
    page integer NOT NULL,
    changed timestamp with time zone NOT NULL,
    editor integer NOT NULL,
    content jsonb NOT NULL,
    CONSTRAINT pk_drafts PRIMARY KEY (page),
    CONSTRAINT fk_drafts_pages FOREIGN KEY (page) REFERENCES conia.pages (page)
);
CREATE OR REPLACE FUNCTION conia.process_drafts_audit()
    RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO audit.drafts (
        page, changed, editor, content
    ) VALUES (
        OLD.page, OLD.changed, OLD.editor, OLD.content
    );
    RETURN OLD;
    -- END IF;
EXCEPTION WHEN unique_violation THEN
    RAISE WARNING 'duplicate drafts audit row skipped. draft: %, changed: %', OLD.page, OLD.changed;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER update_drafts_01_audit_trigger AFTER UPDATE
    ON conia.drafts FOR EACH ROW EXECUTE PROCEDURE
    conia.process_drafts_audit();



CREATE TABLE conia.itemtypes (
    type text NOT NULL CHECK (char_length(type) <= 32),
    CONSTRAINT pk_itemtypes PRIMARY KEY (type)
);


CREATE TABLE conia.menues (
    menu text NOT NULL CHECK (char_length(menu) <= 32),
    CONSTRAINT pk_menues PRIMARY KEY (menu)
);


CREATE TABLE conia.menuitems (
    item integer GENERATED ALWAYS AS IDENTITY,
    uid text NOT NULL CHECK (char_length(uid) = 13),
    type text NOT NULL,
    menu text NOT NULL,
    displayorder smallint NOT NULL,
    title jsonb NOT NULL,
    settings jsonb NOT NULL,
    CONSTRAINT pk_menuitems PRIMARY KEY (item, type),
    CONSTRAINT uc_menuitems_uid UNIQUE (uid),
    CONSTRAINT fk_menuitems_itemtypes FOREIGN KEY (type)
        REFERENCES conia.itemtypes (type) ON UPDATE CASCADE,
    CONSTRAINT fk_menuitems_menues FOREIGN KEY (menu)
        REFERENCES conia.menues (menu) ON UPDATE CASCADE
);


CREATE TABLE conia.linkedpages (
    item integer NOT NULL,
    page integer NOT NULL,
    type text NOT NULL CHECK (type = 'page'),
    CONSTRAINT pk_menupages PRIMARY KEY (item),
    CONSTRAINT fk_linkedpages_menuitems FOREIGN KEY (item, type)
        REFERENCES conia.menuitems (item, type) ON UPDATE CASCADE,
    CONSTRAINT fk_linkedpages_pages FOREIGN KEY (page)
        REFERENCES conia.pages (page)
);


CREATE TABLE conia.topics (
    topic integer GENERATED ALWAYS AS IDENTITY,
    uid text NOT NULL CHECK (char_length(uid) = 13),
    name jsonb NOT NULL,
    color text NOT NULL CHECK (char_length(color) <= 128),
    CONSTRAINT pk_topics PRIMARY KEY (topic),
    CONSTRAINT uc_topics_uid UNIQUE (uid)
);


CREATE TABLE conia.tags (
    tag integer GENERATED ALWAYS AS IDENTITY,
    uid text NOT NULL CHECK (char_length(uid) = 13),
    name jsonb NOT NULL,
    topic integer NOT NULL,
    CONSTRAINT pk_tags PRIMARY KEY (tag),
    CONSTRAINT uc_tags_uid UNIQUE (uid),
    CONSTRAINT fk_tags_topics FOREIGN KEY (topic)
        REFERENCES conia.topics (topic)
);


CREATE TABLE conia.pagetags (
    page integer NOT NULL,
    tag integer NOT NULL,
    sort smallint NOT NULL DEFAULT 0,
    CONSTRAINT pk_pagetags PRIMARY KEY (page, tag),
    CONSTRAINT fk_pagetags_pages FOREIGN KEY (page)
        REFERENCES conia.pages (page),
    CONSTRAINT fk_pagetags_tags FOREIGN KEY (tag)
        REFERENCES conia.tags (tag)
);


CREATE TABLE audit.pages (
    page integer NOT NULL,
    changed timestamp with time zone NOT NULL,
    published boolean NOT NULL,
    hidden boolean NOT NULL,
    locked boolean NOT NULL,
    pagetype text NOT NULL,
    editor integer NOT NULL,
    deleted timestamp with time zone,
    content jsonb NOT NULL,
    CONSTRAINT pk_pages PRIMARY KEY (page, changed),
    CONSTRAINT fk_audit_pages FOREIGN KEY (page)
        REFERENCES conia.pages (page)
);


CREATE TABLE audit.drafts (
    page integer NOT NULL,
    changed timestamp with time zone NOT NULL,
    editor integer NOT NULL,
    content jsonb NOT NULL,
    CONSTRAINT pk_drafts PRIMARY KEY (page, changed),
    CONSTRAINT fk_audit_drafts FOREIGN KEY (page)
        REFERENCES conia.drafts (page)
);


CREATE TABLE audit.users (
    usr integer NOT NULL,
    username text,
    email text,
    display text,
    pwhash text NOT NULL,
    userrole text NOT NULL,
    editor integer NOT NULL,
    changed timestamp with time zone NOT NULL DEFAULT now(),
    deleted timestamp with time zone,
    CONSTRAINT pk_users PRIMARY KEY (usr, changed),
    CONSTRAINT fk_audit_users FOREIGN KEY (usr)
        REFERENCES conia.users (usr)
);
