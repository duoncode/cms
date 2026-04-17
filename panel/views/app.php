<?php $this->layout('base') ?>
<?php $this->insert('component/navigation') ?>

<main id="main">
	<?= $this->body() ?>
</main>

<?php if ($debug): ?>
	<p>DEBUG</p>
<?php endif ?>
	<p><?= $env ?></p>
