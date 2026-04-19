<?php $this->layout('base') ?>

<div class="app">
	<?php $this->insert('component/navigation') ?>

	<main class="main">
		<div class="page" id="main">
			<?= $this->body() ?>
		</div>
	</main>
</div>
