<script>
    import { _ } from '$lib/locale';
    import { loginUser } from '$lib/user';
    import Logo from '$shell/Logo.svelte';

    let message = null;
    let login = null;
    let password = null;
    let rememberme = false;

    async function doLogin() {
        if (login === null || password === null) {
            message = _('Please provide username and password');
            return;
        }

        let result = await loginUser(login, password, rememberme);

        if (result !== true) {
            message = result;
        }
    }
</script>

<div
    class="flex min-h-full flex-col justify-center py-12 bg-gray-50 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md -mt-32">
        <div class="mx-auto h-16 w-auto">
            <Logo />
        </div>
        <h2
            class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
            {_('Sign in to your account')}
        </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
            <form class="space-y-6">
                <div>
                    <label
                        for="login"
                        class="block text-sm font-medium leading-6 text-gray-900">
                        {_('Username or email')}
                    </label>
                    <div class="mt-2">
                        <input
                            id="login"
                            name="login"
                            type="text"
                            autocomplete="username"
                            required
                            bind:value={login}
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-gray-600 sm:text-sm sm:leading-6" />
                    </div>
                </div>

                <div>
                    <label
                        for="password"
                        class="block text-sm font-medium leading-6 text-gray-900">
                        {_('Password')}
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

                <div
                    class="flex flex-col sm:flex-row items-center justify-between">
                    <div class="flex items-center">
                        <input
                            id="rememberme"
                            name="rememberme"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-600" />
                        <label
                            for="rememberme"
                            class="ml-3 block text-sm leading-6 text-gray-900"
                            >{_('Remember me')}</label>
                    </div>

                    <div class="text-sm leading-6 mt-4 sm:mt-0">
                        <a
                            href="/forgot"
                            class="font-semibold text-gray-600 hover:text-gray-500">
                            {_('Forgot your password?')}
                        </a>
                    </div>
                </div>

                <div>
                    <button
                        class="flex w-full justify-center rounded-md bg-gray-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-gray-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600"
                        on:click={doLogin}>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Sign in
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
