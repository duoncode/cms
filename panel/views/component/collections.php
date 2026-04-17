<ul>
<?php foreach ($collections as $id => $collection): ?>
	<li><a href="<?= $panelPath ?>/collection/<?= $id ?>"><?= $collection->name() ?></a></li>
<?php endforeach ?>
</ul>
