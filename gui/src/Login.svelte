<style lang="postcss">
    .login {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        padding: 0 var(--sz-6);
        height: 100vh;
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

        button {
            padding-left: var(--sz-6);
            padding-right: var(--sz-6);
        }
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
            <img src="/logo.svg" alt="Logo" />
        </div>
        <div class="fields">
            <form action="#" method="POST">
                <div class="control">
                    <label for="email"> Email address </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        placeholder="hans"
                        required />
                </div>

                <div class="control">
                    <label for="password"> Password </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required />
                </div>

                <div class="control remember-forgot">
                    <div class="checkbox">
                        <input
                            id="rememberme"
                            name="rememberme"
                            type="checkbox" />
                        <label for="rememberme"> Remember me </label>
                    </div>

                    <a href="/forgot"> Forgot your password? </a>
                </div>

                <div class="button-bar">
                    <button type="submit">Sign in</button>
                </div>
            </form>
        </div>
    </div>
</div>
