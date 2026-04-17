<?php if (count($collections) > 0): ?>
<ul class="level-<?= $level ?>">
<?php foreach ($collections as $item): ?>
	<li>
	<?php if ($item->slug() !== null): ?>
		<a href="<?= $panelPath ?>/collection/<?= $item->slug() ?>"
			hx-target="#collection">
			<?= $item->meta->label ?>
		</a>
	<?php else: ?>
		<span><?= $item->meta->label ?></span>
		<?php $this->insert('component/collections', [
			'collections' => $item->children(),
			'level' => $level + 1,
		]) ?>
	<?php endif ?>
	</li>
<?php endforeach ?>
</ul>
<?php endif ?>
