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


CREATE TABLE conia.migrations (
    migration text NOT NULL CHECK (char_length(migration) <= 512),
    executed timestamp with time zone DEFAULT now() NOT NULL,
    CONSTRAINT pk_migrations PRIMARY KEY (migration)
);


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
CREATE TRIGGER update_users_changed_trigger BEFORE UPDATE ON conia.users
    FOR EACH ROW EXECUTE FUNCTION conia.update_changed_column();


CREATE TABLE conia.pages (
    page integer GENERATED ALWAYS AS IDENTITY,
    uid text NOT NULL CHECK (char_length(uid) = 13),
    published boolean DEFAULT false NOT NULL,
    hidden boolean DEFAULT false NOT NULL,
    locked boolean DEFAULT false NOT NULL,
    template text NOT NULL,
    creator integer NOT NULL,
    editor integer NOT NULL,
    created timestamp with time zone NOT NULL DEFAULT now(),
    changed timestamp with time zone NOT NULL DEFAULT now(),
    deleted timestamp with time zone,
    content jsonb NOT NULL,
    CONSTRAINT pk_pages PRIMARY KEY (page),
    CONSTRAINT uc_pages_uid UNIQUE (uid),
    CONSTRAINT fk_pages_users_creator FOREIGN KEY (creator)
        REFERENCES conia.users (usr),
    CONSTRAINT fk_pages_users_editor FOREIGN KEY (editor)
        REFERENCES conia.users (usr)
);
CREATE OR REPLACE FUNCTION conia.process_pages_audit()
    RETURNS TRIGGER AS $$
BEGIN
    IF (TG_OP = 'UPDATE') THEN
        INSERT INTO audit.pages (
            page, changed, published, hidden, locked,
            template, editor, deleted, content
        ) VALUES (
            OLD.page, OLD.changed, OLD.published, OLD.hidden, OLD.locked,
            OLD.template, OLD.editor, OLD.deleted, OLD.content
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


CREATE TABLE conia.contents (
    page integer NOT NULL,
    lang text NOT NULL CHECK (char_length(lang) <= 32),
    title text NOT NULL,
    slug text NOT NULL CHECK (char_length(slug) <= 512),
    fulltextlang text NOT NULL CHECK (char_length(lang) <= 32),
    deleted timestamp with time zone,
    content jsonb NOT NULL,
    tsv tsvector NOT NULL,
    CONSTRAINT pk_contents PRIMARY KEY (page, lang),
    CONSTRAINT fk_contents_pages FOREIGN KEY (page)
        REFERENCES conia.pages (page)
);
CREATE UNIQUE INDEX uix_contents_slug ON conia.contents
    USING btree (lang, lower(slug)) WHERE (deleted IS NULL);
CREATE INDEX ix_contents_tsv ON conia.contents USING GIN(tsv);
CREATE OR REPLACE FUNCTION conia.update_contents_fulltext() RETURNS
TRIGGER AS $$
BEGIN
    NEW.tsv :=
        to_tsvector(fulltextlang, NEW.title) ||
        to_tsvector(fulltextlang, NEW.content);

    RETURN NEW;
END
$$ LANGUAGE plpgsql;
CREATE OR REPLACE FUNCTION conia.process_contents_audit()
    RETURNS TRIGGER AS $$
DECLARE
    page_changed integer;
BEGIN
    -- IF (TG_OP = 'UPDATE') THEN
    -- Get changed date from conia.pages as
    -- conia.contents has no changed column

    -- TODO: Check if editor is system then cancel

    SELECT cp.changed
    INTO page_changed
    FROM (
        SELECT p.changed
        FROM conia.pages p
        WHERE p.page = OLD.page
    ) cp;

    INSERT INTO audit.contents (
        page, changed, lang, title,
        slug, deleted, content
    ) VALUES (
        OLD.page, page_changed, OLD.lang, OLD.title,
        OLD.slug, OLD.deleted, OLD.content
    );
    RETURN OLD;
    -- END IF;
EXCEPTION WHEN unique_violation THEN
    RAISE WARNING 'duplicate contents audit row skipped. content: %, changed: %', OLD.content, OLD.changed;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER update_contents_01_changed_trigger BEFORE UPDATE ON conia.contents
    FOR EACH ROW EXECUTE FUNCTION conia.update_changed_column();
CREATE TRIGGER update_contents_02_fulltext_trigger BEFORE INSERT OR UPDATE
    ON conia.contents FOR EACH ROW EXECUTE PROCEDURE
    conia.update_contents_fulltext();
CREATE TRIGGER update_contents_03_audit_trigger AFTER UPDATE
    ON conia.contents FOR EACH ROW EXECUTE PROCEDURE
    conia.process_contents_audit();


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


CREATE TABLE conia.draftcontents (
  page integer NOT NULL,
  lang text not null check (char_length(lang) <= 32),
  title text NOT NULL,
  slug text not null check (char_length(slug) <= 512),
  content jsonb NOT NULL,
  CONSTRAINT pk_draftcontents PRIMARY KEY (page, lang),
  CONSTRAINT fk_draftcontents_drafts FOREIGN KEY (page) REFERENCES conia.drafts (page)
);
CREATE OR REPLACE FUNCTION conia.process_draftcontents_audit()
    RETURNS TRIGGER AS $$
DECLARE
    draft_changed integer;
BEGIN
    SELECT cp.changed
    INTO draft_changed
    FROM (
        SELECT d.changed
        FROM conia.drafts d
        WHERE d.page = OLD.page
    ) cd;

    INSERT INTO audit.draftcontents (
        page, changed, lang, title, slug, content
    ) VALUES (
        OLD.page, draft_changed, OLD.lang, OLD.title, OLD.slug, OLD.content
    );
    RETURN OLD;
    -- END IF;
EXCEPTION WHEN unique_violation THEN
    RAISE WARNING 'duplicate draftcontents audit row skipped. draft: %, changed: %', OLD.page, OLD.changed;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER update_draftcontents_01_audit_trigger AFTER UPDATE
    ON conia.draftcontents FOR EACH ROW EXECUTE PROCEDURE
    conia.process_draftcontents_audit();



CREATE TABLE conia.itemtypes (
    type text NOT NULL CHECK (char_length(type) <= 32),
    CONSTRAINT pk_itemtypes PRIMARY KEY (type)
);


CREATE TABLE conia.menues (
    menu text NOT NULL CHECK (char_length(menu) <= 32),
    title text NOT NULL,
    CONSTRAINT pk_menues PRIMARY KEY (menu),
    CONSTRAINT uc_menues_uid UNIQUE (title)
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


CREATE TABLE conia.tags (
    tag text NOT NULL,
    descritption text NOT NULL,
    CONSTRAINT pk_tags PRIMARY KEY (tag)
);


CREATE TABLE conia.pagetags (
    page integer NOT NULL,
    tag text NOT NULL,
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
    template text NOT NULL,
    editor integer NOT NULL,
    deleted timestamp with time zone,
    content jsonb NOT NULL,
    CONSTRAINT pk_pages PRIMARY KEY (page, changed),
    CONSTRAINT fk_audit_pages FOREIGN KEY (page)
        REFERENCES conia.pages (page)
);


CREATE TABLE audit.contents (
    page integer NOT NULL,
    lang text NOT NULL CHECK (char_length(lang) <= 32),
    changed timestamp with time zone NOT NULL,
    title text NOT NULL,
    slug text NOT NULL,
    deleted timestamp with time zone,
    content jsonb NOT NULL,
    CONSTRAINT pk_contents PRIMARY KEY (page, lang, changed),
    CONSTRAINT fk_audit_contents FOREIGN KEY (page, lang)
        REFERENCES conia.contents (page, lang) ON UPDATE CASCADE
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


CREATE TABLE audit.draftcontents (
    page integer NOT NULL,
    changed timestamp with time zone NOT NULL,
    lang text not null check (char_length(lang) <= 32),
    title text NOT NULL,
    slug text not null check (char_length(slug) <= 512),
    content jsonb NOT NULL,
    CONSTRAINT pk_draftcontents PRIMARY KEY (page, lang),
    CONSTRAINT fk_audit_draftcontents FOREIGN KEY (page, lang)
        REFERENCES conia.draftcontents (page, lang)
);
