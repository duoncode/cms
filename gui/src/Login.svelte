<script>
    import { __ } from './lib/locale';
    import { loginUser } from './lib/user';
    import Logo from './shell/Logo.svelte';

    let message = null;
    let login = null;
    let password = null;
    let rememberme = false;

    async function doLogin() {
        if (login === null || password === null) {
            message = __('Please provide username and password');
            return;
        }

        let result = await loginUser(login, password, rememberme);

        if (result !== true) {
            message = result;
        }
    }
</script>

<style lang="postcss">
    .login {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        padding: 0 var(--s-6);
        height: 100vh;
        width: 100%;
    }

    .logo {
        :global(svg) {
            display: block;
            height: var(--admin-logo-login-height, var(--s-20));
            width: auto;
            margin: 0 auto;
        }
    }

    .fields {
        background: var(--white);
        margin-top: var(--s-6);
        padding: var(--s-8) var(--s-4);
        box-shadow: var(--shadow);
    }

    .remember-forgot {
        display: flex;
        justify-content: space-between;
        align-items: center;

        a {
            font-size: var(--text-sm);
        }
    }

    .button-bar {
        text-align: right;
        margin-top: 1.5rem;
    }

    .message {
        margin-top: var(--sz-6);
        text-align: center;
        padding: var(--s-6);
        border: 1px solid black;
        background-color: var(--white);
        border-radius: var(--radius-md);
    }

    @media (--sm) {
        .form {
            margin-top: calc(var(--s-32) * -1);
            width: 100%;
            max-width: var(--s-md);
        }

        .fields {
            border-radius: var(--radius-lg);
            padding-left: var(--s-10);
            padding-right: var(--s-10);
        }
    }

    @media (--lg) {
        .form {
            padding-left: var(--s-8);
            padding-right: var(--s-8);
        }
    }
</style>

<div class="login">
    <div class="form">
        <div class="logo">
            <Logo />
        </div>
        {#if message}
            <div class="message">{message}</div>
        {/if}
        <div class="fields">
            <form>
                <div class="control">
                    <label for="login">{__('Username or email')}</label>
                    <input
                        id="login"
                        name="login"
                        type="text"
                        autocomplete="username"
                        required
                        bind:value={login} />
                </div>

                <div class="control">
                    <label for="password">{__('Password')}</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        bind:value={password} />
                </div>

                <div class="control remember-forgot">
                    <div class="checkbox">
                        <input
                            id="rememberme"
                            name="rememberme"
                            type="checkbox"
                            bind:checked={rememberme} />
                        <label for="rememberme">{__('Remember me')}</label>
                    </div>

                    <a href="/forgot">{__('Forgot your password?')}</a>
                </div>

                <div class="button-bar">
                    <button type="button" on:click={doLogin}>
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
                        {__('Sign in')}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
