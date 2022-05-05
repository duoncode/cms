<script>
    import { _ } from './lib/locale';
    import { loginUser } from './lib/user';

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

<style lang="postcss">
    .login {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        padding: 0 var(--sz-6);
        height: 100vh;
        width: 100%;
    }

    .logo {
        img {
            display: block;
            height: var(--admin-logo-login-height, var(--sz-20));
            width: auto;
            margin: 0 auto;
        }
    }

    .fields {
        background: var(--white);
        margin-top: var(--sz-6);
        padding: var(--sz-8) var(--sz-4);
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
        padding: var(--sz-6);
        border: 1px solid black;
        background-color: var(--white);
        border-radius: var(--radius-md);
    }

    @media (--sm) {
        .form {
            margin-top: calc(var(--sz-32) * -1);
            width: 100%;
            max-width: var(--w-md);
        }

        .fields {
            border-radius: var(--radius-lg);
            padding-left: var(--sz-10);
            padding-right: var(--sz-10);
        }
    }

    @media (--lg) {
        .form {
            padding-left: var(--sz-8);
            padding-right: var(--sz-8);
        }
    }
</style>

<div class="login">
    <div class="form">
        <div class="logo">
            <img src="logo.svg" alt="Logo" />
        </div>
        {#if message}
            <div class="message">{message}</div>
        {/if}
        <div class="fields">
            <form>
                <div class="control">
                    <label for="login">{_('Username or email')}</label>
                    <input
                        id="login"
                        name="login"
                        type="text"
                        autocomplete="username"
                        required
                        bind:value={login} />
                </div>

                <div class="control">
                    <label for="password">{_('Password')}</label>
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
                        <label for="rememberme">{_('Remember me')}</label>
                    </div>

                    <a href="/forgot">{_('Forgot your password?')}</a>
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
                        {_('Sign in')}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
