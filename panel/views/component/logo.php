<a
	class="cms-sidebar-logo"
	href="<?= $panelPath ?>"
	hx-target="#main"
	aria-label="Dashboard">
	<?php if ($logo !== null): ?>
		<img class="cms-sidebar-logo-image" src="<?= $logo ?>" alt="Panel Logo" />
	<?php else: ?>
		<span class="cms-sidebar-logo-mark" aria-hidden="true">D</span>
		<span class="cms-sidebar-logo-wordmark">Duon</span>
	<?php endif ?>
</a>
