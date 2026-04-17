<aside class="sidebar">
	<header class="sidebar-header">
		<?php $this->insert('component/logo') ?>
	</header>

	<nav class="sidebar-nav" aria-label="Panel navigation">
		<div class="sidebar-scroll">
			<ul class="nav-list level-0">
				<li class="nav-item">
					<a
						class="nav-link"
						href="<?= $panelPath ?>"
						hx-target="#main"
						<?= (string) $currentPath === (string) $panelPath ? 'aria-current="page"' : '' ?>>
						Dashboard
					</a>
				</li>
			</ul>

			<?php $this->insert('component/collections', ['level' => 0]) ?>
		</div>
	</nav>

	<footer class="sidebar-footer">
		<form method="post" action="<?= $panelPath ?>/logout" hx-boost="false">
			<button class="sidebar-action" type="submit">Logout</button>
		</form>
	</footer>
</aside>
