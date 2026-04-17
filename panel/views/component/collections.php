<ul>
<?php foreach ($collections as $id => $collection): ?>
	<li><a href="<?= $panelPath ?>/collection/<?= $id ?>" hx-target="#collection"><?= $collection->name() ?></a></li>
<?php endforeach ?>
</ul>
