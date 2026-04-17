<nav>
	<ul>
		<li>
			<a href="<?= $panelPath ?>" hx-target="#main">Dashboard</a>
		</li>
	</ul>

	<?php $this->insert('component/collections', ['level' => 1]) ?>
</nav>
