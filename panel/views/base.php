<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Duon CMS Panel</title>
<?php foreach ($cssFiles as $cssFile): ?>
	<link rel="stylesheet" href="<?= $cssFile ?>">
<?php endforeach ?>
</head>

<body>
	<?= $this->body() ?>
<?php if ($debug): ?>
	<p>DEBUG</p>
<?php endif ?>
	<p><?= $env ?></p>

<?php foreach ($jsFiles as $jsFile): ?>
	<script src="<?= $panelPath ?>/assets/app/<?= $jsFile ?>"></script>
<?php endforeach ?>
</body>
</html>
