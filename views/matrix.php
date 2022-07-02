<div class="<?= $prefix ?>matrix <?= $prefix ?>matrix-columns-<?= $columns ?>">
    <?php foreach ($fields as $field) : ?>
        <?php if ($field['type'] === 'wysiwyg') : ?>
            <div class="<?= $prefix ?>wysiwyg <?= $prefix ?>-colspan-<?= $field['colspan'] ?? $columns ?>">
                <?= $this->clean($field['data']) ?>
            </div>
        <?php else : ?>

        <?php endif ?>
    <?php endforeach ?>
</div>
