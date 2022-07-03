<div class="<?= $prefix ?>matrix <?= $prefix ?>matrix-columns-<?= $columns ?>">
    <?php foreach ($fields as $field) : ?>
        <?= $this->insert(
            strtolower($field->valueType),
            $this->context(['field' => $field])
        ) ?>
    <?php endforeach ?>
</div>
