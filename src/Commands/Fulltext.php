<?php

declare(strict_types=1);

namespace Conia\Core\Commands;

use Conia\Quma\Commands\Command;
use Conia\Quma\Database;

class Fulltext extends Command
{
    protected string $group = 'Database';
    protected string $name = 'fulltext';
    protected string $description = 'Updates the fulltext index';

    public function run(): int
    {
        $this->env->db->fulltext->clean();
        $this->update($this->env->db);

        return 0;
    }

    private function update(Database $db): void
    {
        foreach ($db->fulltext->pages()->lazy() as $page) {
            $json = json_decode($page['content'], true);
            error_log(print_r($json, true));
            break;
        }
    }
}
