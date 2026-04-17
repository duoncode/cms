<nav>
	<ul>
		<li>
			<a href="<?= $panelPath ?>" hx-target="#main">Dashboard</a>
		</li>
	</ul>

	<?php $this->insert('component/collections', ['level' => 1]) ?>

	<form method="post" action="<?= $panelPath ?>/logout" hx-boost="false">
		<button type="submit">Logout</button>
	</form>
</nav>
