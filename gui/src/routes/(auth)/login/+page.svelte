<script lang="ts">
	import { preventDefault } from 'svelte/legacy';

	import { _ } from '$lib/locale';
	import { system } from '$lib/sys';
	import { loginUser } from '$lib/user';
	import Logo from '$shell/Logo.svelte';
	import Link from '$shell/Link.svelte';
	import Button from '$shell/Button.svelte';
	import IcoLogin from '$shell/icons/IcoLogin.svelte';

	type Props = {
		message?: string;
	};

	let { message = $bindable('') }: Props = $props();

	async function handleSubmit() {
		const data = new FormData(this);
		const login = data.get('login');
		const password = data.get('password');
		let rememberme = false;

		if (data.get('rememberme') === 'true') {
			rememberme = true;
		}

		if (!login || !password) {
			message = _('Please provide username and password');
			return;
		}

		let result = await loginUser(login, password, rememberme);

		console.log(result);
		if (result !== true) {
			message = result;
		}
	}
</script>

<div class="flex min-h-full flex-col justify-center bg-gray-50 py-12 sm:px-6 lg:px-8">
	{#if $system.initialized}
		{#if $system.logo}
			<div class="-mt-32 sm:mx-auto sm:w-full sm:max-w-md">
				<div class="mx-auto w-auto">
					<img
						style="width: 10rem; display: block; margin: 0 auto;"
						src={$system.logo}
						alt="Panel Logo" />
				</div>
			</div>
		{:else}
			<div class="-mt-32 sm:mx-auto sm:w-full sm:max-w-md">
				<div class="mx-auto h-16 w-auto">
					<Logo />
				</div>
			</div>
		{/if}
	{/if}

	{#if message}
		<div
			class="mt-8 rounded border border-rose-600 bg-rose-200 px-4 py-2 text-center text-rose-600 sm:mx-auto sm:w-full sm:max-w-md">
			{message}
		</div>
	{/if}

	<div class="mt-10 sm:mx-auto sm:w-full sm:max-w-md">
		<div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
			<form
				method="POST"
				onsubmit={preventDefault(handleSubmit)}
				class="space-y-6">
				<div>
					<label
						for="login"
						class="block text-sm font-semibold leading-6 text-gray-900">
						{_('Benutzername oder E-Mail-Adresse')}
					</label>
					<div class="mt-2">
						<input
							id="login"
							name="login"
							type="text"
							autocomplete="username"
							required
							class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-gray-600 sm:text-sm sm:leading-6" />
					</div>
				</div>

				<div>
					<label
						for="password"
						class="block text-sm font-semibold leading-6 text-gray-900">
						{_('Passwort')}
					</label>
					<div class="mt-2">
						<input
							id="password"
							name="password"
							type="password"
							autocomplete="current-password"
							required
							class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-gray-600 sm:text-sm sm:leading-6" />
					</div>
				</div>

				<!--<div
                    class="flex flex-col sm:flex-row items-center justify-between">
                    <div class="flex items-center">
                        <input
                            id="rememberme"
                            name="rememberme"
                            type="checkbox"
                            value="true"
                            class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-600" />
                        <label
                            for="rememberme"
                            class="ml-3 block text-sm leading-6 text-gray-900"
                            >{_('Angemeldet bleiben')}</label>
                    </div>

                    <div class="text-sm leading-6 mt-4 sm:mt-0">
                        <Link
                            href="forgot"
                            class="font-semibold text-emerald-700 underline">
                            {_('Passwort vergessen?')}
                        </Link>
                    </div>
                </div>-->

				<div class="flex justify-end">
					<Button
						class="primary w-full"
						type="submit"
						icon={IcoLogin}>
						Anmelden
					</Button>
				</div>
			</form>
		</div>
	</div>
</div>
