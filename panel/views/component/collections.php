<?php if (count($collections) > 0): ?>
<ul class="nav-list level-<?= $level ?>">
<?php foreach ($collections as $item): ?>
	<li class="nav-item">
	<?php if ($item->slug() !== null): ?>
		<?php

		$href = $panelPath . '/collection/' . $item->slug();
		$icon = $renderIcon($item->meta->icon);
		?>
		<a
			class="nav-link"
			style="--depth: <?= $level ?>"
			href="<?= $href ?>"
			hx-target="#main"
			<?= (string) $currentPath === $href ? 'aria-current="page"' : '' ?>>
			<span class="nav-label">
				<?php if ($icon !== ''): ?>
					<span class="nav-icon" aria-hidden="true"><?= $icon ?></span>
				<?php endif ?>
				<span><?= $item->meta->label ?></span>
			</span>
			<?php if (trim((string) $item->meta->badge) !== ''): ?>
				<span class="nav-badge"><?= $item->meta->badge ?></span>
			<?php endif ?>
		</a>
	<?php else: ?>
		<?php $icon = $renderIcon($item->meta->icon); ?>
		<div
			class="nav-section"
			style="--depth: <?= $level ?>">
			<span class="nav-section-label nav-label">
				<?php if ($icon !== ''): ?>
					<span class="nav-icon" aria-hidden="true"><?= $icon ?></span>
				<?php endif ?>
				<span><?= $item->meta->label ?></span>
			</span>
			<?php $this->insert('component/collections', [
				'collections' => $item->children(),
				'level' => $level + 1,
			]) ?>
		</div>
	<?php endif ?>
	</li>
<?php endforeach ?>
</ul>
<?php endif ?>
