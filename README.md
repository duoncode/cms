# FiveOrbs Content Management Framework

> :warning: **Note**: This library is under active development, some of the listed features are still experimental and subject to change. Large parts of the documentation are missing. 


Settings 

    'session.authcookie' => '<app>_auth', // Name of the auth cookie
    'session.expires' => 60 * 60 * 24,    // One day by default


Test database:

    CREATE DATABASE fiveorbs_db WITH TEMPLATE = template0 ENCODING = 'UTF8';
    CREATE USER fiveorbs_user PASSWORD 'fiveorbs_password';
    GRANT ALL PRIVILEGES ON DATABASE fiveorbs_db TO fiveorbs_user;
    ALTER DATABASE fiveorbs_db OWNER TO fiveorbs_user;

to allow recreation via command RecreateDb:

    ALTER USER fiveorbs_user SUPERUSER;

System Requirements:

    apt install php8.2 php8.2-pgsql php8.2-gd php8.2-xml php8.2-intl php8.2-curl

For development

    apt install php8.2 php8.2-xdebug
