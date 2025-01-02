# FiveOrbs Content Management Framework

> :warning: **Note**: This library is under active development, some of the listed features are still experimental and subject to change. Large parts of the documentation are missing. 


Settings 

    'session.authcookie' => '<app>_auth', // Name of the auth cookie
    'session.expires' => 60 * 60 * 24,    // One day by default


Test database:

    CREATE DATABASE fiveorbs_cms_test_db WITH TEMPLATE = template0 ENCODING = 'UTF8';
	CREATE USER fiveorbs_cms_test_user PASSWORD 'fiveorbs_cms_test_password';
	GRANT ALL PRIVILEGES ON DATABASE fiveorbs_cms_test_db TO fiveorbs_cms_test_user;
	ALTER DATABASE fiveorbs_cms_test_db OWNER TO fiveorbs_cms_test_user;

to allow recreation via command RecreateDb:

	ALTER USER fiveorbs_cms_test_user SUPERUSER;

System Requirements:

    apt install php8.2 php8.2-pgsql php8.2-gd php8.2-xml php8.2-intl php8.2-curl

For development

    apt install php8.2 php8.2-xdebug
