Conia CMS/CMF Core
==================


> :warning: **Note**: This library is under active development, some of the listed features are still experimental and subject to change. Large parts of the documentation are missing. 


Settings 

    'session.authcookie' => '<app>_auth', // Name of the auth cookie
    'session.expires' => 60 * 60 * 24,    // One day by default


Test database:

    CREATE DATABASE conia_db WITH TEMPLATE = template0 ENCODING = 'UTF8';
    CREATE USER conia_user PASSWORD 'conia_password';
    GRANT ALL PRIVILEGES ON DATABASE conia_db TO conia_user;
    ALTER DATABASE conia_db OWNER TO conia_user;

to allow recreation via command RecreateDb:

    ALTER USER conia_user SUPERUSER;

System Requirements:

    apt install php8.2 php8.2-pgsql php8.2-gd php8.2-xml php8.2-intl
