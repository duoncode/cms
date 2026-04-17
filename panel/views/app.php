<?php $this->layout('base') ?>
<?php $this->insert('component/collections', ['level' => 1]) ?>

<?= $this->body() ?>

<?php if ($debug): ?>
	<p>DEBUG</p>
<?php endif ?>
	<p><?= $env ?></p>
