<?php

declare(strict_types=1);

namespace Conia\Core\Commands;

use Conia\Cli\Command;

const RECREATE_SCRIPT = '-- A script to re-initialize the empty :dbname database
--
-- Deletes and re-creates the database. Only for dev purposes.
\\connect postgres
SELECT
    pg_terminate_backend(pid)
FROM
    pg_stat_activity
WHERE
    -- don\'t kill my own connection!
    pid <> pg_backend_pid()
    -- don\'t kill the connections to other databases
    AND datname = \'%1$s\';
DROP DATABASE IF EXISTS %1$s;
CREATE DATABASE %1$s WITH TEMPLATE = template0 ENCODING = \'UTF8\';
\\connect %1$s';

class RecreateDb extends Command
{
    protected string $group = 'Database';
    protected string $name = 'recreate-db';
    protected string $description = 'Drop and recreate a PostgeSQL Database';

    public function __construct(
        protected readonly string $db,
        protected readonly string $user,
        protected readonly string $password,
        protected readonly string $host = 'localhost',
        protected readonly int $port = 5432
    ) {
    }

    public function run(): int
    {
        $tmpname = tempnam(sys_get_temp_dir(), 'conia_recreatedb_');

        try {
            $tmpfile = fopen($tmpname, 'w');
            fwrite($tmpfile, sprintf(RECREATE_SCRIPT, $this->db));

            echo "Recreate Database {$this->db}" . PHP_EOL;
            system(sprintf(
                'PGPASSWORD=%s psql -U %s -d %s -h %s -p %s -f %s',
                $this->password,
                $this->user,
                $this->db,
                $this->host,
                (string)$this->port,
                $tmpname,
            ));
            system('php run create-migrations-table');
            system('php run migrations --apply');
        } finally {
            fclose($tmpfile);
            unlink($tmpname);
            if (file_exists($tmpname)) {
                echo 'NOTE: Could not delete temporary file `' . $tmpname . '`' . PHP_EOL;
            }
        }

        return 0;
    }
}
