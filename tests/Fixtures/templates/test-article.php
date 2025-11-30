<!DOCTYPE html>
<html lang="<?= $locale?->id ?? 'en' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($page->title()); ?></title>
</head>
<body>
    <article>
        <h1><?= htmlspecialchars($page->title()); ?></h1>
        <?php if (isset($page->content)): ?>
            <div class="content">
                <?= nl2br((string) $page->content); ?>
            </div>
        <?php endif; ?>
    </article>
</body>
</html>
