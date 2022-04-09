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
            height: var(--admin-logo-login-height, var(--sz-12));
            width: auto;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            font-size: var(--text-3xl);
            margin-top: var(--sz-6);
        }
    }

    .fields {
        background: var(--white);
        padding: var(--sz-8) var(--sz-4);
        box-shadow: var(--shadow);
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
        <div class="logo sm:mx-auto sm:w-full sm:max-w-md">
            <img src="/logo.svg" alt="Logo" />
            <h1>Sign in to your account</h1>
        </div>
        <div class="fields">
            <form action="#" method="POST">
                <div>
                    <label for="email"> Email address </label>
                    <div>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required />
                    </div>
                </div>

                <div>
                    <label for="password"> Password </label>
                    <div>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required />
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input
                            id="rememberme"
                            name="rememberme"
                            type="checkbox" />
                        <label for="remember-me"> Remember me </label>
                    </div>

                    <div class="text-sm">
                        <a href="/forgot"> Forgot your password? </a>
                    </div>
                </div>

                <div>
                    <button type="submit">Sign in</button>
                </div>
            </form>
        </div>
    </div>
</div>
