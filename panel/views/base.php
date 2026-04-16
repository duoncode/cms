<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Duon CMS Panel</title>
<?php foreach ($cssFiles as $cssFile): ?>
	<link rel="stylesheet" href="<?= $panelPath ?>/assets/styles/<?= $cssFile ?>">
<?php endforeach ?>
</head>

<body>
	<?= $this->body() ?>

<?php foreach ($jsFiles as $jsFile): ?>
	<script src="<?= $panelPath ?>/assets/app/<?= $jsFile ?>"></script>
<?php endforeach ?>
</body>
</html>
