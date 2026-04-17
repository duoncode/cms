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
	<?= $this->body() ?>

<?php foreach ($scripts as $script): ?>
	<script src="<?= $script ?>"></script>
<?php endforeach ?>
</body>
</html>
