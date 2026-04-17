<?php $this->layout('base'); ?>

<main id="main">
	<h1>Login</h1>

	<?php if ($message !== ''): ?>
	<p><?= $message ?></p>
	<?php endif ?>

	<form method="post" action="<?= $panelPath ?>/login" hx-boost="false">
		<input type="hidden" name="next" value="<?= $next ?>" />

		<p>
			<label for="login">Username or email</label>
			<input
				id="login"
				name="login"
				type="text"
				autocomplete="username"
				value="<?= $login ?>"
				required />
		</p>

		<p>
			<label for="password">Password</label>
			<input
				id="password"
				name="password"
				type="password"
				autocomplete="current-password"
				required />
		</p>

		<p>
			<label>
				<input
					type="checkbox"
					name="rememberme"
					value="1"
					<?= $rememberme ? 'checked' : '' ?> />
				Remember me
			</label>
		</p>

		<p>
			<button type="submit">Login</button>
		</p>
	</form>
</main>
