const Ziggy = {
    url: 'http:\/\/laravel-vue-template.test:8080',
    port: 8080,
    defaults: {},
    routes: {
        login: { uri: 'login', methods: ['GET', 'HEAD'] },
        'login.store': { uri: 'login', methods: ['POST'] },
        logout: { uri: 'logout', methods: ['POST'] },
        'password.request': {
            uri: 'forgot-password',
            methods: ['GET', 'HEAD'],
        },
        'password.reset': {
            uri: 'reset-password\/{token}',
            methods: ['GET', 'HEAD'],
            parameters: ['token'],
        },
        'password.email': { uri: 'forgot-password', methods: ['POST'] },
        'password.update': { uri: 'reset-password', methods: ['POST'] },
        register: { uri: 'register', methods: ['GET', 'HEAD'] },
        'register.store': { uri: 'register', methods: ['POST'] },
        'verification.notice': {
            uri: 'email\/verify',
            methods: ['GET', 'HEAD'],
        },
        'verification.verify': {
            uri: 'email\/verify\/{id}\/{hash}',
            methods: ['GET', 'HEAD'],
            parameters: ['id', 'hash'],
        },
        'verification.send': {
            uri: 'email\/verification-notification',
            methods: ['POST'],
        },
        'password.confirm': {
            uri: 'user\/confirm-password',
            methods: ['GET', 'HEAD'],
        },
        'password.confirmation': {
            uri: 'user\/confirmed-password-status',
            methods: ['GET', 'HEAD'],
        },
        'password.confirm.store': {
            uri: 'user\/confirm-password',
            methods: ['POST'],
        },
        'two-factor.login': {
            uri: 'two-factor-challenge',
            methods: ['GET', 'HEAD'],
        },
        'two-factor.login.store': {
            uri: 'two-factor-challenge',
            methods: ['POST'],
        },
        'two-factor.enable': {
            uri: 'user\/two-factor-authentication',
            methods: ['POST'],
        },
        'two-factor.confirm': {
            uri: 'user\/confirmed-two-factor-authentication',
            methods: ['POST'],
        },
        'two-factor.disable': {
            uri: 'user\/two-factor-authentication',
            methods: ['DELETE'],
        },
        'two-factor.qr-code': {
            uri: 'user\/two-factor-qr-code',
            methods: ['GET', 'HEAD'],
        },
        'two-factor.secret-key': {
            uri: 'user\/two-factor-secret-key',
            methods: ['GET', 'HEAD'],
        },
        'two-factor.recovery-codes': {
            uri: 'user\/two-factor-recovery-codes',
            methods: ['GET', 'HEAD'],
        },
        'two-factor.regenerate-recovery-codes': {
            uri: 'user\/two-factor-recovery-codes',
            methods: ['POST'],
        },
        'passkey.login-options': {
            uri: 'passkeys\/login\/options',
            methods: ['GET', 'HEAD'],
        },
        'passkey.login': { uri: 'passkeys\/login', methods: ['POST'] },
        'passkey.confirm-options': {
            uri: 'passkeys\/confirm\/options',
            methods: ['GET', 'HEAD'],
        },
        'passkey.confirm': { uri: 'passkeys\/confirm', methods: ['POST'] },
        'passkey.registration-options': {
            uri: 'user\/passkeys\/options',
            methods: ['GET', 'HEAD'],
        },
        'passkey.store': { uri: 'user\/passkeys', methods: ['POST'] },
        'passkey.destroy': {
            uri: 'user\/passkeys\/{passkey}',
            methods: ['DELETE'],
            parameters: ['passkey'],
            bindings: { passkey: 'id' },
        },
        home: { uri: '\/', methods: ['GET', 'HEAD'] },
        dashboard: { uri: 'dashboard', methods: ['GET', 'HEAD'] },
        'profile.edit': { uri: 'settings\/profile', methods: ['GET', 'HEAD'] },
        'profile.update': { uri: 'settings\/profile', methods: ['PATCH'] },
        'profile.destroy': { uri: 'settings\/profile', methods: ['DELETE'] },
        'security.edit': {
            uri: 'settings\/security',
            methods: ['GET', 'HEAD'],
        },
        'user-password.update': { uri: 'settings\/password', methods: ['PUT'] },
        'sessions.index': {
            uri: 'settings\/sessions',
            methods: ['GET', 'HEAD'],
        },
        'sessions.destroy': {
            uri: 'settings\/sessions\/{session}',
            methods: ['DELETE'],
            parameters: ['session'],
        },
        'sessions.destroy-other': {
            uri: 'settings\/sessions',
            methods: ['DELETE'],
        },
        'appearance.edit': {
            uri: 'settings\/appearance',
            methods: ['GET', 'HEAD'],
        },
        'well-known.passkeys': {
            uri: '.well-known\/passkey-endpoints',
            methods: ['GET', 'HEAD'],
        },
        'security.users.resend-invitation': {
            uri: 'security\/users\/{user}\/resend-invitation',
            methods: ['POST'],
            parameters: ['user'],
            bindings: { user: 'id' },
        },
        'security.users.index': {
            uri: 'security\/users',
            methods: ['GET', 'HEAD'],
        },
        'security.users.create': {
            uri: 'security\/users\/create',
            methods: ['GET', 'HEAD'],
        },
        'security.users.store': { uri: 'security\/users', methods: ['POST'] },
        'security.users.show': {
            uri: 'security\/users\/{user}',
            methods: ['GET', 'HEAD'],
            parameters: ['user'],
            bindings: { user: 'id' },
        },
        'security.users.edit': {
            uri: 'security\/users\/{user}\/edit',
            methods: ['GET', 'HEAD'],
            parameters: ['user'],
            bindings: { user: 'id' },
        },
        'security.users.update': {
            uri: 'security\/users\/{user}',
            methods: ['PUT', 'PATCH'],
            parameters: ['user'],
            bindings: { user: 'id' },
        },
        'security.users.destroy': {
            uri: 'security\/users\/{user}',
            methods: ['DELETE'],
            parameters: ['user'],
            bindings: { user: 'id' },
        },
        'security.roles.index': {
            uri: 'security\/roles',
            methods: ['GET', 'HEAD'],
        },
        'security.roles.create': {
            uri: 'security\/roles\/create',
            methods: ['GET', 'HEAD'],
        },
        'security.roles.store': { uri: 'security\/roles', methods: ['POST'] },
        'security.roles.show': {
            uri: 'security\/roles\/{role}',
            methods: ['GET', 'HEAD'],
            parameters: ['role'],
            bindings: { role: 'id' },
        },
        'security.roles.edit': {
            uri: 'security\/roles\/{role}\/edit',
            methods: ['GET', 'HEAD'],
            parameters: ['role'],
            bindings: { role: 'id' },
        },
        'security.roles.update': {
            uri: 'security\/roles\/{role}',
            methods: ['PUT', 'PATCH'],
            parameters: ['role'],
            bindings: { role: 'id' },
        },
        'security.roles.destroy': {
            uri: 'security\/roles\/{role}',
            methods: ['DELETE'],
            parameters: ['role'],
            bindings: { role: 'id' },
        },
        'security.permissions.index': {
            uri: 'security\/permissions',
            methods: ['GET', 'HEAD'],
        },
        'security.permissions.create': {
            uri: 'security\/permissions\/create',
            methods: ['GET', 'HEAD'],
        },
        'security.permissions.store': {
            uri: 'security\/permissions',
            methods: ['POST'],
        },
        'security.permissions.edit': {
            uri: 'security\/permissions\/{permission}\/edit',
            methods: ['GET', 'HEAD'],
            parameters: ['permission'],
            bindings: { permission: 'id' },
        },
        'security.permissions.update': {
            uri: 'security\/permissions\/{permission}',
            methods: ['PUT', 'PATCH'],
            parameters: ['permission'],
            bindings: { permission: 'id' },
        },
        'security.permissions.destroy': {
            uri: 'security\/permissions\/{permission}',
            methods: ['DELETE'],
            parameters: ['permission'],
            bindings: { permission: 'id' },
        },
        'security.modules.index': {
            uri: 'security\/modules',
            methods: ['GET', 'HEAD'],
        },
        'security.modules.create': {
            uri: 'security\/modules\/create',
            methods: ['GET', 'HEAD'],
        },
        'security.modules.store': {
            uri: 'security\/modules',
            methods: ['POST'],
        },
        'security.modules.edit': {
            uri: 'security\/modules\/{module}\/edit',
            methods: ['GET', 'HEAD'],
            parameters: ['module'],
            bindings: { module: 'id' },
        },
        'security.modules.update': {
            uri: 'security\/modules\/{module}',
            methods: ['PUT', 'PATCH'],
            parameters: ['module'],
            bindings: { module: 'id' },
        },
        'security.modules.destroy': {
            uri: 'security\/modules\/{module}',
            methods: ['DELETE'],
            parameters: ['module'],
            bindings: { module: 'id' },
        },
        'storage.local': {
            uri: 'storage\/{path}',
            methods: ['GET', 'HEAD'],
            wheres: { path: '.*' },
            parameters: ['path'],
        },
        'storage.local.upload': {
            uri: 'storage\/{path}',
            methods: ['PUT'],
            wheres: { path: '.*' },
            parameters: ['path'],
        },
    },
};

if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}

export { Ziggy };
