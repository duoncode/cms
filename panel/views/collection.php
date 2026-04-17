<?php if (!$boosted)
	$this->layout('app'); ?>

<div id="collection">
	<h1>Collection</h1>
	<p> <?= $uid ?></p>
	<?php if ($htmx): ?>
	<p><b>BOOSTED!</b></p>
	<?php endif ?>
</div>
