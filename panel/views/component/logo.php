<a
	class="sidebar-logo"
	href="<?= $panelPath ?>"
	hx-target="#main"
	aria-label="Dashboard">
	<?php if ($logo !== null): ?>
		<img class="sidebar-logo-image" src="<?= $logo ?>" alt="Panel Logo" />
	<?php else: ?>
		<span class="sidebar-logo-mark" aria-hidden="true">D</span>
		<span class="sidebar-logo-wordmark">Duon</span>
	<?php endif ?>
</a>
