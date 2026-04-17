<?php if (count($collections) > 0): ?>
<ul class="nav-list level-<?= $level ?>">
<?php foreach ($collections as $item): ?>
	<li class="nav-item">
	<?php if ($item->slug() !== null): ?>
		<?php $href = $panelPath . '/collection/' . $item->slug(); ?>
		<a
			class="nav-link"
			style="--depth: <?= $level ?>"
			href="<?= $href ?>"
			hx-target="#main"
			<?= (string) $currentPath === $href ? 'aria-current="page"' : '' ?>>
			<span><?= $item->meta->label ?></span>
			<?php if (trim((string) $item->meta->badge) !== ''): ?>
				<span class="nav-badge"><?= $item->meta->badge ?></span>
			<?php endif ?>
		</a>
	<?php else: ?>
		<div
			class="nav-section"
			style="--depth: <?= $level ?>">
			<span class="nav-section-label"><?= $item->meta->label ?></span>
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
