<?php $this->layout('base') ?>

<div class="cms-app">
	<?php $this->insert('component/navigation') ?>

	<main id="main" class="cms-main">
		<div class="cms-page">
			<?= $this->body() ?>
		</div>
	</main>
</div>
