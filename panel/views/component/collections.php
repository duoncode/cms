<?php
$render = function (array $items) use (&$render, $panelPath): void {
	if ($items === []) {
		return;
	}
	?>
	<ul>
	<?php foreach ($items as $item): ?>
		<li>
			<?php if (($item['type'] ?? null) === 'section'): ?>
				<span><?= $item['name'] ?></span>
				<?php $render($item['children'] ?? []) ?>
			<?php else: ?>
				<a href="<?= $panelPath ?>/collection/<?= $item['slug'] ?>" hx-target="#collection"><?= $item['name'] ?></a>
			<?php endif ?>
		</li>
	<?php endforeach ?>
	</ul>
	<?php
};

$render($collections);
