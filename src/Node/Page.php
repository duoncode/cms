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

    protected function persistUrlPaths(Database $db, array $data, int $editor, int $node): void
    {
        $defaultLocale = $this->config->locales->getDefault();
        $defaultPath = trim($data['paths'][$defaultLocale->id] ?? '');

        if (!$defaultPath) {
            throw new RuntimeException(_('Die URL für die Hauptsprache {$defaultLocale->title} muss gesetzt sein'));
        }

        $currentPaths = array_column($db->nodes->getPaths(['node' => $node])->all(), 'path', 'locale');

        foreach ($data['paths'] as $locale => $path) {
            if ($path) {
                if (!str_starts_with($path, '/')) {
                    $path = '/' . $path;
                }
                // If this is a new path it could already be in the
                // list of inactive ones. So delete it if it exists.
                $db->nodes->deleteInactivePath(['path' => $path])->run();

                $currentPath = $currentPaths[$locale] ?? null;

                if ($currentPath && $currentPath === $path) {
                    // The path is the same as the existing. Move on.
                    continue;
                }

                if ($currentPath) {
                    $db->nodes->deactivatePath([
                        'path' => $currentPath,
                        'locale' => $locale,
                        'editor' => $editor,
                    ])->run();
                }

                if ($db->nodes->pathExists(['path' => $path])->one()) {
                    // The new path already exists, add a unique part
                    $path = $path . '-' . nanoid();
                }

                $db->nodes->savePath([
                    'node' => $node,
                    'path' => $path,
                    'locale' => $locale,
                    'editor' => $editor,
                ])->run();
            } else {
                // A localized path was emptied
                if (array_key_exists($locale, $currentPaths)) {
                    $db->nodes->deactivatePath([
                        'path' => $currentPaths[$locale],
                        'locale' => $locale,
                        'editor' => $editor,
                    ])->run();
                }
            }
        }
    }
}
