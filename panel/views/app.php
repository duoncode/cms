<?php $this->layout('base') ?>

<div class="app">
	<?php $this->insert('component/navigation') ?>

	<main id="main" class="main">
		<div class="page">
			<?= $this->body() ?>
		</div>
	</main>
</div>
