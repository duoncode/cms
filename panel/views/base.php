<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Duon CMS Panel</title>
<?php foreach ($stylesheets as $stylesheet): ?>
	<link rel="stylesheet" href="<?= $stylesheet ?>">
<?php endforeach ?>
</head>

<body hx-boost:inherited="true">
	<?php $this->insert('component/collections', ['level' => 1]) ?>

	<?= $this->body() ?>

<?php if ($debug): ?>
	<p>DEBUG</p>
<?php endif ?>
	<p><?= $env ?></p>

<?php foreach ($scripts as $script): ?>
	<script src="<?= $script ?>"></script>
<?php endforeach ?>
</body>
</html>
