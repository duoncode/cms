<?php

declare(strict_types=1);

namespace Conia\Core\Node;

use Conia\Core\Exception\RuntimeException;
use Conia\Core\Locale;
use Conia\Quma\Database;

abstract class Page extends Node
{
    use RendersTemplate;

    protected static string|array $route = ''; // The route pattern of node instances

    public function path(Locale $locale = null): string
    {
        $paths = $this->data['paths'];

        if (!$locale) {
            $locale = $this->request->get('locale');
        }

        while ($locale) {
            if (isset($paths[$locale->id])) {
                return $paths[$locale->id];
            }

            $locale = $locale->fallback();
        }

        throw new RuntimeException('No url path found');
    }

    public function blueprint(array $values = []): array
    {
        $result = parent::blueprint($values);
        $result['data']['route'] = static::$route;

        return $result;
    }

    /**
     * Overrides Node::persist to add persisting of url paths.
     */
    protected function persist(Database $db, array $data, int $editor): void
    {
        $node = $this->persistNode($db, $data, $editor);
        $this->persistUrlPaths($db, $data, $editor, $node);
    }

    protected function persistUrlPaths(Database $db, array $data, int $editor, int $node): int
    {
        $defaultLocale = $this->config->locales->getDefault();
        $defaultPath = trim($data['paths'][$defaultLocale->id] ?? '');

        // if (!$defaultPath) {
            //     throw new RuntimeException(_("Die URL für die Hauptsprache {$defaultLocale->title} muss gesetzt sein"));
        // }

        error_log(print_r($data['paths'], true));
        // foreach ($data['paths'] as $locale => $path) {
            //     if ($path) {
            //         $db->nodes->deleteInactivePath(['path' => $path])->run();
            //     }
        // }

        $currentPaths = $db->nodes->getPaths(['node' => $node])->all();
        error_log(print_r($currentPaths, true));

        return 0;
    }
}
