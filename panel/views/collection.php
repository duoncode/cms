<?php if (!$boosted)
	$this->layout('app'); ?>

<h1>Collection</h1>
<p><?= $uid ?></p>
<?php if ($htmx): ?>
<p><b>BOOSTED!</b></p>
<?php endif ?>
