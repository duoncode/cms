<aside class="cms-sidebar">
	<header class="cms-sidebar-header">
		<?php $this->insert('component/logo') ?>
	</header>

	<nav class="cms-sidebar-nav" aria-label="Panel navigation">
		<div class="cms-sidebar-scroll">
			<ul class="cms-nav-list level-0">
				<li class="cms-nav-item">
					<a
						class="cms-nav-link"
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

	<footer class="cms-sidebar-footer">
		<form method="post" action="<?= $panelPath ?>/logout" hx-boost="false">
			<button class="cms-sidebar-action" type="submit">Logout</button>
		</form>
	</footer>
</aside>
