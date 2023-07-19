CREATE EXTENSION btree_gist;
CREATE EXTENSION btree_gin;
CREATE EXTENSION unaccent;

CREATE SCHEMA conia;
CREATE SCHEMA audit;

CREATE TYPE conia.contenttype AS ENUM ('page', 'block', 'document');


CREATE FUNCTION conia.update_changed_column()
    RETURNS TRIGGER AS $$
BEGIN
   NEW.changed = now();
   RETURN NEW;
END;
$$ LANGUAGE plpgsql;


CREATE TABLE conia.userroles (
    userrole text NOT NULL,
    CONSTRAINT pk_userroles PRIMARY KEY (userrole)
);


CREATE TABLE conia.users (
    usr integer GENERATED ALWAYS AS IDENTITY,
    uid text NOT NULL CHECK (char_length(uid) <= 64),
    username text CHECK (char_length(username) > 0),
    email text CHECK (email SIMILAR TO '%@%' AND char_length(email) > 5),
    pwhash text NOT NULL,
    userrole text NOT NULL,
    active boolean NOT NULL,
    data jsonb NOT NULL,
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
CREATE FUNCTION conia.process_users_audit()
    RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO audit.users (
        usr, username, email, pwhash, userrole, active,
        data, editor, changed, deleted
    ) VALUES (
        OLD.usr, OLD.username, OLD.email, OLD.pwhash, OLD.userrole, OLD.active,
        OLD.data, OLD.editor, OLD.changed, OLD.deleted
    );

    RETURN OLD;
EXCEPTION WHEN unique_violation THEN
    RAISE WARNING 'Duplicate users audit row skipped. user: %, changed: %', OLD.usr, OLD.changed;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;
CREATE FUNCTION conia.validate_user_credentials()
    RETURNS TRIGGER AS $$
BEGIN
    IF NEW.username IS NULL AND NEW.email IS NULL THEN
        RAISE EXCEPTION 'Either username or email must be provided.';
        RETURN NULL;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER users_trigger_01_change BEFORE UPDATE ON conia.users
    FOR EACH ROW EXECUTE FUNCTION conia.update_changed_column();
CREATE TRIGGER users_trigger_02_verification BEFORE INSERT OR UPDATE ON conia.users
    FOR EACH ROW EXECUTE FUNCTION conia.validate_user_credentials();
CREATE TRIGGER users_trigger_03_audit AFTER UPDATE
    ON conia.users FOR EACH ROW EXECUTE PROCEDURE
    conia.process_users_audit();


CREATE TABLE conia.activationcodes (
    usr integer NOT NULL,
    uid text NOT NULL CHECK (char_length(uid) <= 128),
    CONSTRAINT pk_activationcodes PRIMARY KEY (usr),
    CONSTRAINT fk_activationcodes_users FOREIGN KEY (usr) REFERENCES conia.users(usr)
);

CREATE TABLE conia.loginsessions (
    hash text NOT NULL,
    usr integer NOT NULL,
    expires timestamp with time zone NOT NULL,
    CONSTRAINT pk_loginsessions PRIMARY KEY (hash),
    CONSTRAINT uc_loginsessions_usr UNIQUE (usr),
    CONSTRAINT fk_loginsessions_users FOREIGN KEY (usr) REFERENCES conia.users(usr),
    CONSTRAINT ck_loginsessions_hash CHECK (char_length(hash) <= 254)
);


CREATE TABLE conia.types (
    type integer GENERATED ALWAYS AS IDENTITY,
    slug text NOT NULL CHECK (char_length(slug) <= 64),
    kind conia.contenttype NOT NULL,
    CONSTRAINT pk_types PRIMARY KEY (type),
    CONSTRAINT uc_types_name UNIQUE (slug)
);


CREATE TABLE conia.nodes (
    node integer GENERATED ALWAYS AS IDENTITY,
    uid text NOT NULL CHECK (char_length(uid) <= 64),
    parent integer,
    published boolean DEFAULT false NOT NULL,
    hidden boolean DEFAULT false NOT NULL,
    locked boolean DEFAULT false NOT NULL,
    type integer NOT NULL,
    creator integer NOT NULL,
    editor integer NOT NULL,
    created timestamp with time zone NOT NULL DEFAULT now(),
    changed timestamp with time zone NOT NULL DEFAULT now(),
    deleted timestamp with time zone,
    content jsonb NOT NULL,
    CONSTRAINT pk_nodes PRIMARY KEY (node),
    CONSTRAINT uc_nodes_uid UNIQUE (uid),
    CONSTRAINT fk_nodes_users_creator FOREIGN KEY (creator)
        REFERENCES conia.users (usr),
    CONSTRAINT fk_nodes_nodes FOREIGN KEY (parent)
        REFERENCES conia.nodes (node),
    CONSTRAINT fk_nodes_users_editor FOREIGN KEY (editor)
        REFERENCES conia.users (usr),
    CONSTRAINT fk_nodes_types FOREIGN KEY (type)
        REFERENCES conia.types (type) ON UPDATE CASCADE ON DELETE NO ACTION
);
CREATE INDEX ix_nodes_content ON conia.nodes USING GIN (type, content);
CREATE FUNCTION conia.process_nodes_audit()
    RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO audit.nodes (
        node, parent, changed, published, hidden, locked,
        type, editor, deleted, content
    ) VALUES (
        OLD.node, OLD.parent, OLD.changed, OLD.published, OLD.hidden, OLD.locked,
        OLD.type, OLD.editor, OLD.deleted, OLD.content
    );

    RETURN OLD;
EXCEPTION WHEN unique_violation THEN
    RAISE WARNING 'Duplicate nodes audit row skipped. node: %, changed: %', OLD.node, OLD.changed;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;
CREATE FUNCTION conia.check_if_deletable()
    RETURNS TRIGGER AS $$
BEGIN
    IF (
        NEW.deleted IS NOT NULL
        AND (
            SELECT count(*)
            FROM conia.menuitems mi
            WHERE
                mi.data->>'type' = 'node'
                AND mi.data->>'node' = OLD.node::text
        ) > 0
    )
    THEN
        RAISE EXCEPTION 'node is still referenced in a menu';
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER nodes_trigger_01_delete BEFORE UPDATE ON conia.nodes
    FOR EACH ROW EXECUTE PROCEDURE conia.check_if_deletable();
CREATE TRIGGER nodes_trigger_02_change BEFORE UPDATE ON conia.nodes
    FOR EACH ROW EXECUTE FUNCTION conia.update_changed_column();
CREATE TRIGGER nodes_trigger_03_audit AFTER UPDATE
    ON conia.nodes FOR EACH ROW EXECUTE PROCEDURE
    conia.process_nodes_audit();


CREATE TABLE conia.fulltext (
    node integer NOT NULL,
    locale text NOT NULL CHECK (char_length(locale) = 32),
    document tsvector NOT NULL,
    CONSTRAINT pk_fulltext PRIMARY KEY (node, locale),
    CONSTRAINT fk_fulltext_nodes FOREIGN KEY (node)
        REFERENCES conia.nodes (node)
);
CREATE INDEX ix_nodes_tsv ON conia.fulltext USING GIN(document);


CREATE TABLE conia.urlpaths (
    node integer NOT NULL,
    path text NOT NULL CHECK (char_length(path) <= 512),
    locale text NOT NULL CHECK (char_length(locale) <= 32),
    inactive timestamp with time zone,
    CONSTRAINT pk_urlpaths PRIMARY KEY (node, locale, path),
    CONSTRAINT fk_urlpaths_nodes FOREIGN KEY (node)
        REFERENCES conia.nodes (node)
);
CREATE UNIQUE INDEX uix_urlpaths_path ON conia.urlpaths
    USING btree (path);
CREATE UNIQUE INDEX uix_urlpaths_locale ON conia.urlpaths
    USING btree (node, locale) WHERE (inactive IS NULL);


CREATE TABLE conia.drafts (
    node integer NOT NULL,
    changed timestamp with time zone NOT NULL,
    editor integer NOT NULL,
    content jsonb NOT NULL,
    CONSTRAINT pk_drafts PRIMARY KEY (node),
    CONSTRAINT fk_drafts_nodes FOREIGN KEY (node) REFERENCES conia.nodes (node)
);
CREATE FUNCTION conia.process_drafts_audit()
    RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO audit.drafts (
        node, changed, editor, content
    ) VALUES (
        OLD.node, OLD.changed, OLD.editor, OLD.content
    );

    RETURN OLD;
EXCEPTION WHEN unique_violation THEN
    RAISE WARNING 'Duplicate drafts audit row skipped. draft: %, changed: %', OLD.node, OLD.changed;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER drafts_trigger_01_audit AFTER UPDATE
    ON conia.drafts FOR EACH ROW EXECUTE PROCEDURE
    conia.process_drafts_audit();


CREATE TABLE conia.menus (
    menu text NOT NULL CHECK (char_length(menu) <= 32),
    description text NOT NULL CHECK (char_length(description) <= 128),
    CONSTRAINT pk_menus PRIMARY KEY (menu)
);


CREATE TABLE conia.menuitems (
    item text NOT NULL CHECK (char_length(item) <= 64),
    parent text CHECK (char_length(parent) <= 64),
    menu text NOT NULL,
    displayorder smallint NOT NULL,
    data jsonb NOT NULL,
    CONSTRAINT pk_menuitems PRIMARY KEY (item),
    CONSTRAINT fk_menuitems_menus FOREIGN KEY (menu)
        REFERENCES conia.menus (menu) ON UPDATE CASCADE,
    CONSTRAINT fk_menuitems_menuitems FOREIGN KEY (parent)
        REFERENCES conia.menuitems (item)
);


CREATE TABLE conia.topics (
    topic integer GENERATED ALWAYS AS IDENTITY,
    uid text NOT NULL CHECK (char_length(uid) <= 64),
    name jsonb NOT NULL,
    color text NOT NULL CHECK (char_length(color) <= 128),
    CONSTRAINT pk_topics PRIMARY KEY (topic),
    CONSTRAINT uc_topics_uid UNIQUE (uid)
);


CREATE TABLE conia.tags (
    tag integer GENERATED ALWAYS AS IDENTITY,
    uid text NOT NULL CHECK (char_length(uid) <= 64),
    name jsonb NOT NULL,
    topic integer NOT NULL,
    CONSTRAINT pk_tags PRIMARY KEY (tag),
    CONSTRAINT uc_tags_uid UNIQUE (uid),
    CONSTRAINT fk_tags_topics FOREIGN KEY (topic)
        REFERENCES conia.topics (topic)
);


CREATE TABLE conia.nodetags (
    node integer NOT NULL,
    tag integer NOT NULL,
    sort smallint NOT NULL DEFAULT 0,
    CONSTRAINT pk_nodetags PRIMARY KEY (node, tag),
    CONSTRAINT fk_nodetags_nodes FOREIGN KEY (node)
        REFERENCES conia.nodes (node),
    CONSTRAINT fk_nodetags_tags FOREIGN KEY (tag)
        REFERENCES conia.tags (tag)
);


CREATE TABLE audit.nodes (
    node integer NOT NULL,
    parent integer,
    changed timestamp with time zone NOT NULL,
    published boolean NOT NULL,
    hidden boolean NOT NULL,
    locked boolean NOT NULL,
    type text NOT NULL,
    editor integer NOT NULL,
    deleted timestamp with time zone,
    content jsonb NOT NULL,
    CONSTRAINT pk_nodes PRIMARY KEY (node, changed),
    CONSTRAINT fk_audit_nodes FOREIGN KEY (node)
        REFERENCES conia.nodes (node)
);


CREATE TABLE audit.drafts (
    node integer NOT NULL,
    changed timestamp with time zone NOT NULL,
    editor integer NOT NULL,
    content jsonb NOT NULL,
    CONSTRAINT pk_drafts PRIMARY KEY (node, changed),
    CONSTRAINT fk_audit_drafts FOREIGN KEY (node)
        REFERENCES conia.drafts (node)
);


CREATE TABLE audit.users (
    usr integer NOT NULL,
    username text,
    email text,
    pwhash text NOT NULL,
    userrole text NOT NULL,
    active boolean NOT NULL,
    data jsonb NOT NULL,
    editor integer NOT NULL,
    changed timestamp with time zone NOT NULL DEFAULT now(),
    deleted timestamp with time zone,
    CONSTRAINT pk_users PRIMARY KEY (usr, changed),
    CONSTRAINT fk_audit_users FOREIGN KEY (usr)
        REFERENCES conia.users (usr)
);
