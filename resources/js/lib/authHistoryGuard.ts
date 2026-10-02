import { router } from '@inertiajs/vue3';

const AUTH_KEY = 'app_session_active';

export const isClientAuthenticated = (): boolean => {
    if (
        typeof window === 'undefined' ||
        typeof sessionStorage === 'undefined'
    ) {
        return false;
    }

    return sessionStorage.getItem(AUTH_KEY) === 'true';
};

export const setClientAuthenticated = (authenticated: boolean): void => {
    if (
        typeof window === 'undefined' ||
        typeof sessionStorage === 'undefined'
    ) {
        return;
    }

    if (authenticated) {
        sessionStorage.setItem(AUTH_KEY, 'true');
    } else {
        sessionStorage.removeItem(AUTH_KEY);
    }
};

export const initializeAuthHistoryGuard = (): (() => void) => {
    if (typeof window === 'undefined') {
        return () => {};
    }

    let isRedirecting = false;

    // Keep client auth state synchronized on every Inertia navigation
    const removeNavigateListener = router.on('navigate', (event) => {
        const user = (event.detail.page.props as { auth?: { user?: unknown } })
            ?.auth?.user;
        setClientAuthenticated(Boolean(user));
    });

    // Intercept popstate events in the capture phase before Inertia's popstate listener runs
    const handlePopState = (event: PopStateEvent): void => {
        const historicalState = event.state?.page;
        const historyHasAuthenticatedProps = Boolean(
            historicalState?.props?.auth?.user,
        );
        const currentlyAuthenticated = isClientAuthenticated();

        // If a logged-out user tries to navigate back to a cached authenticated page
        if (!currentlyAuthenticated && historyHasAuthenticatedProps) {
            // Always stop Inertia from executing its internal popstate handler and restoring the page
            event.stopImmediatePropagation();

            if (isRedirecting) {
                return;
            }

            isRedirecting = true;

            // Sanitize historical state with structured object to avoid null reference errors
            window.history.replaceState(
                { page: null },
                '',
                window.location.href,
            );

            // Smoothly visit the target URL via Inertia so Laravel auth middleware handles redirection
            router.visit(window.location.href, {
                replace: true,
                preserveScroll: false,
                preserveState: false,
                onFinish: () => {
                    isRedirecting = false;
                },
            });
        }
    };

    // Handle bfcache page restoration when navigating back after logout
    const handlePageShow = (event: PageTransitionEvent): void => {
        if (event.persisted && !isClientAuthenticated()) {
            if (isRedirecting) {
                return;
            }

            isRedirecting = true;

            router.visit(window.location.href, {
                replace: true,
                preserveScroll: false,
                preserveState: false,
                onFinish: () => {
                    isRedirecting = false;
                },
            });
        }
    };

    window.addEventListener('popstate', handlePopState, true);
    window.addEventListener('pageshow', handlePageShow);

    return () => {
        removeNavigateListener();
        window.removeEventListener('popstate', handlePopState, true);
        window.removeEventListener('pageshow', handlePageShow);
    };
};
