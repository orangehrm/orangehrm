---
name: auth-providers
description: Reference for OrangeHRM's pluggable authentication system — `AuthProviderChain` (DI-registered as `Services::AUTH_PROVIDER_CHAIN`, holds an ordered list of `AbstractAuthProvider` implementations and dispatches `authenticate()` to the highest-priority one that accepts), `AbstractAuthProvider` (`authenticate(AuthParams): bool` + `getPriority(): int`), `LocalAuthProvider` (password verification against the bcrypt hash in `users.user_password`), `LDAPAuthProvider` (queries an LDAP server + syncs users via `LDAPService` and `LDAPSyncService`), the OAuth2 server (`orangehrmCoreOAuthPlugin` based on `league/oauth2-server`, separate from interactive login), and OpenID Connect / SSO (`orangehrmOpenidAuthenticationPlugin` based on `jumbojett/openid-connect-php`, handles Google / Microsoft / generic OIDC providers via the `AuthProviderExtraDetails` entity). Use whenever the user is wiring a new auth provider, debugging "why won't this user log in", asking about LDAP sync, configuring OAuth clients for an external integration, or integrating an SSO provider. Companion to `security-primitives` (password hashing via `PasswordHash`, OAuth client_secret encryption), `authorization` (different concept — this skill is about *who you are*, authorization is about *what you can do*), `events` (auth providers can dispatch events but currently don't), `scheduled-jobs` (LDAP sync runs as a scheduled task).
---

# Authentication providers

OrangeHRM has **a pluggable chain of authentication providers**. Login attempts are routed through each provider in priority order until one accepts. Three providers ship in-tree:

1. **`LocalAuthProvider`** — username + password against the bcrypt hash in `users.user_password` (the default). Provided by `orangehrmAuthenticationPlugin`.
2. **`LDAPAuthProvider`** — username + password against an LDAP/AD server with on-the-fly user sync. Provided by `orangehrmLDAPAuthenticationPlugin`.
3. **OAuth2 server** (different model — see below) and **OpenID Connect (OIDC) SSO** — these are auxiliary auth paths, not direct password providers.

This skill covers the provider chain, the three concrete providers, and the patterns for adding new ones. **Authentication is "who you are"; for "what you can do" see `authorization`.** For password hashing, see `security-primitives`.

## The provider chain — `AuthProviderChain`

`OrangeHRM\Authentication\Auth\AuthProviderChain`. Registered in the DI container by `Framework::configureContainer()` as `Services::AUTH_PROVIDER_CHAIN`.

```php
class AuthProviderChain
{
    public function addProvider(AbstractAuthProvider $authProvider): void
    {
        // … rejects duplicate provider classes
        // … rejects conflicting priorities
        $this->providers[] = $authProvider;
        $this->priorities[] = $authProvider->getPriority();
    }

    public function authenticate(AuthParamsInterface $authParams): bool
    {
        array_multisort($this->priorities, SORT_DESC, $this->providers);
        foreach ($this->providers as $authProvider) {
            if ($authProvider->authenticate($authParams)) {
                return true;
            }
        }
        return false;
    }
}
```

Rules:
- Providers are **registered by plugins** during `PluginConfiguration::initialize()`.
- Each provider has a **unique priority** (the chain throws if two providers share one).
- **Higher priority runs first.**
- Authentication **short-circuits on first success** — once a provider returns true, the chain stops.
- A provider returning false means "I don't accept these credentials" — the next provider tries.

## `AbstractAuthProvider` — the contract

```php
abstract class AbstractAuthProvider
{
    abstract public function authenticate(AuthParamsInterface $authParams): bool;
    abstract public function getPriority(): int;
}
```

Two methods to implement:

1. **`authenticate(AuthParams): bool`** — try to authenticate. Return `true` if successful (the user is now logged in, the provider's own side effects have run). Return `false` if these credentials aren't for this provider. **Throw `AuthenticationException`** if something is wrong with the provider itself (e.g. LDAP server down) — the chain doesn't catch this, so it propagates up.

2. **`getPriority(): int`** — higher = checked first. Existing providers:
   - `LocalAuthProvider` priority: `0` (lowest, fallback)
   - `LDAPAuthProvider` priority: typically `500` (when LDAP is enabled, it's tried first)

The pattern is **specialized auth first, local last**: if LDAP is configured, try it; failing that, fall back to local. If neither is configured, only local is registered.

## Provider registration — in plugin `initialize()`

Each auth plugin registers its provider with the chain. The `AuthenticationPluginConfiguration` always registers `LocalAuthProvider`:

```php
namespace OrangeHRM\Authentication;

use OrangeHRM\Authentication\Auth\AuthProviderChain;
use OrangeHRM\Authentication\Auth\LocalAuthProvider;
use OrangeHRM\Framework\Services;

class AuthenticationPluginConfiguration implements PluginConfigurationInterface
{
    use ServiceContainerTrait;

    public function initialize(Request $request): void
    {
        /** @var AuthProviderChain $authProviderChain */
        $authProviderChain = $this->getContainer()->get(Services::AUTH_PROVIDER_CHAIN);
        $authProviderChain->addProvider(new LocalAuthProvider());
        // … plus other subscribers and config
    }
}
```

And `LDAPAuthenticationPluginConfiguration` adds the LDAP one **conditionally on LDAP being enabled** (so we don't try LDAP on instances that don't use it):

```php
class LDAPAuthenticationPluginConfiguration implements
    PluginConfigurationInterface,
    ConsoleConfigurationInterface,
    SchedulerConfigurationInterface
{
    use ConfigServiceTrait;
    use ServiceContainerTrait;

    public function initialize(Request $request): void
    {
        $ldapSettings = $this->getConfigService()->getLDAPSetting();
        if ($ldapSettings instanceof LDAPSetting && $ldapSettings->isEnable()) {
            /** @var AuthProviderChain $authProviderChain */
            $authProviderChain = $this->getContainer()->get(Services::AUTH_PROVIDER_CHAIN);
            $authProviderChain->addProvider(new LDAPAuthProvider());
        }
        // …
    }
}
```

The pattern: **adding a provider is a one-liner in `initialize()`**, gated on configuration so disabled providers don't waste cycles.

## Provider 1 — `LocalAuthProvider` (the default)

`OrangeHRM\Authentication\Auth\LocalAuthProvider`. The everyday password-based login.

```php
class LocalAuthProvider extends AbstractAuthProvider
{
    use ConfigServiceTrait;
    use PasswordStrengthServiceTrait;
    use I18NHelperTrait;

    public function authenticate(AuthParamsInterface $authParams): bool
    {
        // … queries User by username
        // … (new PasswordHash())->verify($plainPassword, $user->getUserPassword())
        // … starts the session if valid
        // … checks password strength against policy; if weak, throws PasswordEnforceException
        //    to redirect to "must change password" screen
        // returns true on success, false on bad credentials
    }

    public function getPriority(): int
    {
        return 0;                                                // fallback
    }
}
```

**Storage:** bcrypt hash via `Utility/PasswordHash` (see `security-primitives` skill). The `users.user_password` column stores the hash.

**Side effects:**
- Sets session attributes (`AuthUser::userId`, etc.)
- May throw `PasswordEnforceException` if the configured password policy has changed and the user's current password no longer meets it — the login flow catches this and redirects to "must change password" screen
- Increments login-attempt counter (used by lockout features, if enabled)

## Provider 2 — `LDAPAuthProvider` (when LDAP is enabled)

`OrangeHRM\LDAP\Auth\LDAPAuthProvider`. When the org uses Active Directory or another LDAP-compliant directory.

```php
class LDAPAuthProvider extends AbstractAuthProvider
{
    use LoggerTrait;

    public function authenticate(AuthParamsInterface $authParams): bool
    {
        // 1. Bind to LDAP server with the provided credentials
        //    (via LDAPService::authenticate)
        // 2. If bind succeeds:
        //    - Sync the user (LDAPSyncService) — create local User row if missing, update fields
        //    - Mark this user as "comes from LDAP" via UserAuthProvider entity
        //    - Start the session
        //    - Return true
        // 3. If bind fails: return false (chain falls through to local provider)
    }

    public function getPriority(): int
    {
        // typically higher than LocalAuthProvider, so LDAP is tried first
    }
}
```

**Two services do the work:**

| Service | Use |
|---|---|
| `LDAPService` | Low-level LDAP bind + search. Wraps `symfony/ldap`. |
| `LDAPSyncService` | On successful auth, syncs the LDAP user into the local `users` + `hs_hr_employee` tables. Creates a Doctrine `User` if one doesn't exist, updates name/email/etc. if it does. |

**The `UserAuthProvider` entity** tracks the per-user mapping — a `users` row can have one or more rows in `ohrm_user_auth_provider` indicating which providers it has linked credentials with. Lets a user authenticate via LDAP and also (separately) via OIDC if both are configured.

**LDAP sync also runs as a scheduled task** (see `scheduled-jobs` skill — `orangehrm:ldap-sync-user` runs hourly by default). This pre-creates `User` rows for LDAP users so they exist before they first try to log in.

**Settings storage:** `KEY_LDAP_SETTINGS` in `hs_hr_config` (see `config` skill) — a JSON blob containing host, port, bind DN, search base, user mapping, etc. Configured via Admin → Configuration → LDAP Configuration UI.

**LDAP-specific commands:**
- `php bin/console orangehrm:ldap-sync-user` — manual sync trigger (also scheduled hourly)
- See `console-commands` for how the command is registered

## OAuth2 server — `orangehrmCoreOAuthPlugin`

**Different model from the providers above.** OAuth2 is for **third-party application access** to OrangeHRM's REST API, not interactive user login. Uses `league/oauth2-server`.

```
External app    OrangeHRM (this codebase)
   ↓
GET /oauth2/authorize?client_id=...&response_type=code&redirect_uri=...
   ↓ (user grants permission)
Authorization code returned
   ↓
POST /oauth2/token (with auth code + client_secret)
   ↓
Access token returned
   ↓
External app uses access token for /api/v2/... requests
```

Three main components:

| Piece | Role |
|---|---|
| `Server/` directory | Wraps `league/oauth2-server` configuration: authorization-code grant, refresh-token grant, scopes. |
| `OAuthClient` entity (`ohrm_oauth_client`) | Each registered third-party app. `client_id`, `client_secret` (encrypted), `redirect_uri`, etc. |
| `AccessToken`, `RefreshToken`, `AuthCode` entities | The tokens. Stored in DB so revocation is possible. |
| `OAuthSubscriber` | Intercepts `KernelEvents::REQUEST` for the `/oauth2/*` endpoints. |
| `AuthorizationController` (public, see `authorization` skill) | The user-facing consent screen ("Allow X to access your data?"). |

**The interactive user is still logged in via `LocalAuthProvider` / `LDAPAuthProvider`** — OAuth2 just adds a separate token-based access path for non-interactive callers. The OAuth flow uses the user's existing session for the consent step.

**Token TTLs** are config-driven (see `config` skill):
- `oauth.access_token_ttl` — typically 3600s
- `oauth.refresh_token_ttl` — longer, configurable
- `oauth.auth_code_ttl` — typically 600s

**Encryption keys** for OAuth (separate from the field-encryption key — see `security-primitives`):
- `oauth.encryption_key` and `oauth.token_encryption_key` in `hs_hr_config`. Used by the OAuth server for token signing.

**Admin UI**: Admin → Configuration → Register OAuth Client → set up `client_id` / `client_secret` / `redirect_uri`.

When integrating a new external app, you typically:
1. Register the client via the UI (gets you `client_id` + `client_secret`)
2. External app starts the auth flow at `/oauth2/authorize`
3. User logs in (via Local / LDAP), consents, gets the auth code
4. External app exchanges for an access token

## OpenID Connect / SSO — `orangehrmOpenidAuthenticationPlugin`

**For "log in with Google / Microsoft / generic OIDC provider"** instead of password. Uses `jumbojett/openid-connect-php`.

```
User clicks "Log in with Google" on /auth/login
  → Redirect to Google's OIDC authorization endpoint
  → User authenticates with Google
  → Google redirects back with an ID token
  → OpenidAuthenticationController exchanges the token, looks up the local user
  → User is logged in (session started, same as Local/LDAP)
```

Key bits:

| Piece | Role |
|---|---|
| `AuthProviderExtraDetails` entity (`ohrm_auth_provider_extra_details`) | Stores OIDC client config: `client_secret` (encrypted), discovery URL, scopes per provider. |
| `OpenidProvider` entity (`ohrm_openid_provider`) | The registered providers ("Google", "Microsoft Azure", etc.). |
| `SocialMediaAuthenticationService` | The login flow's coordinator. |
| `LoginController` | Renders the "Log in with X" buttons from the configured providers. |

**Configuration**: Admin → Configuration → OpenID Connect → Add Provider → name, client_id, client_secret, discovery URL.

A user authenticating via OIDC gets a `UserAuthProvider` row marking which OIDC provider they used. The first OIDC login auto-creates a `User` row (matching by email); subsequent logins just authenticate.

**`KEY_OPENID_PROVIDER_ADDED` config flag** signals to the login page whether to render the "Log in with…" buttons. When false (no providers configured), the buttons are hidden.

## Choosing the right auth approach

| Scenario | Use |
|---|---|
| Interactive user login with username + password | **`LocalAuthProvider`** (always available; the default) |
| Interactive login authenticated against AD/LDAP directory | **`LDAPAuthProvider`** + LDAP plugin enabled |
| Interactive login via Google / Microsoft / OIDC SSO | **OpenID plugin** + provider configured |
| Third-party app / integration accessing the REST API | **OAuth2 server** + client registered |
| API client that already has a username + password | They hit `/api/v2/...` with credentials in HTTP Basic header (rare); typically OAuth2 is preferred |

The four approaches **coexist** — an OrangeHRM instance can have local + LDAP + OIDC + OAuth all enabled at once. A single user can have credentials in multiple (e.g. their AD username, plus they linked their Google account).

## Where authentication state lives

After a successful authenticate(), the session has:

- `AuthUser::SESSION_USER_ID` — the user's `User.id`
- `AuthUser::SESSION_EMPLOYEE_NUMBER` — their `emp_number`
- `AuthUser::SESSION_AUTHENTICATED` — `true`
- `AuthUser::SESSION_USER_ROLE` — their static role (Admin or ESS — dynamic roles like Supervisor are computed per request, see `authorization` skill)
- Plus assorted flash attributes (login errors, redirect URLs)

Accessed via `AuthUserTrait::getAuthUser()` from anywhere in the request lifecycle (see `helpers` skill).

`AuthenticationSubscriber` (see `authorization` skill) is the runtime gate that checks `getAuthUser()->isAuthenticated()` on every request and forces re-login when the session is invalid.

---

# Recipes

## Recipe 1 — Add a new auth provider

Goal: integrate with a hypothetical SAML provider.

1. **Define the provider class** in your plugin's `Auth/` directory:

```php
namespace OrangeHRM\X\Auth;

use OrangeHRM\Authentication\Auth\AbstractAuthProvider;
use OrangeHRM\Authentication\Dto\AuthParamsInterface;

class SamlAuthProvider extends AbstractAuthProvider
{
    public function authenticate(AuthParamsInterface $authParams): bool
    {
        // … your SAML validation logic
        // … on success: start session via AuthenticationService, return true
        // … on "not for me": return false
        // … on broken-state: throw AuthenticationException
    }

    public function getPriority(): int
    {
        return 700;                                              // higher than LDAP (500), lower than admin override
    }
}
```

2. **Register** in your plugin's `<Plugin>PluginConfiguration::initialize()`:

```php
public function initialize(Request $request): void
{
    if ($this->getConfigService()->isSamlEnabled()) {
        /** @var AuthProviderChain $chain */
        $chain = $this->getContainer()->get(Services::AUTH_PROVIDER_CHAIN);
        $chain->addProvider(new SamlAuthProvider());
    }
}
```

3. **Make sure your provider's `authenticate()` returns false** (not throws) when the credentials are for someone else (e.g. they're plain username/password, not a SAML assertion). The chain falls through to the next provider on false.

## Recipe 2 — Register a new OAuth2 client (for an external integration)

For a third-party app to consume the OrangeHRM API:

1. Admin → Configuration → Register OAuth Client → set name, redirect URI
2. Save → get `client_id` + `client_secret` (the secret is shown once; record it)
3. External app uses the credentials in the standard OAuth2 authorization-code flow

For programmatic client setup, write to `ohrm_oauth_client` directly (rare — the UI is the canonical path). `client_secret` is encrypted at rest via the column-level encryption pattern (see `security-primitives`).

## Recipe 3 — Configure LDAP (operator side)

Admin → Configuration → LDAP Configuration:

- Host, port, encryption (TLS / SSL / none)
- Bind credentials (or anonymous if directory allows)
- Search base, user filter
- User mapping (which LDAP attribute → which `users` field)
- Sync interval (1-23 hours; drives the scheduled `orangehrm:ldap-sync-user` task)

Save → triggers `LDAPSyncService` to do an initial sync. New `User` rows are created with `users.user_password = NULL` (LDAP-only auth — no local password). `UserAuthProvider` rows mark them as LDAP-sourced.

## Recipe 4 — Debug "user can't log in"

| Symptom | Check |
|---|---|
| "Invalid credentials" on a known good password | Local provider: is the password the right one? Use `password_verify($plain, $hash)` manually. LDAP provider: are LDAP settings correct? Try the bind via `ldapsearch` from the same host. |
| User exists in LDAP but doesn't get logged in | Is `LDAPAuthenticationPluginConfiguration::initialize()` adding the provider? Is `ldapSettings->isEnable()` true? Is the LDAP server reachable from the OrangeHRM host? |
| OAuth client_id rejected | Is the `ohrm_oauth_client` row enabled? Has the `client_secret` been mistyped? Decrypt and compare. |
| OIDC button doesn't appear | Is `KEY_OPENID_PROVIDER_ADDED` true in `hs_hr_config`? Is at least one `OpenidProvider` row enabled? |
| Password policy rejection on otherwise-valid creds | `LocalAuthProvider` throws `PasswordEnforceException` if the current password doesn't meet the configured policy. User must visit "weak password reset" — the login flow handles this redirect. |

---

# Checklists

## Add a new auth provider

- [ ] Provider class extends `AbstractAuthProvider`; implements `authenticate(AuthParams): bool` + `getPriority(): int`
- [ ] On "credentials match but invalid", throw `AuthenticationException` — don't return false (that means "not for me")
- [ ] On "credentials are for someone else" (different protocol, different format), return false — chain falls through
- [ ] On success, start the session via `AuthenticationService`, set the `User` lookup, return true
- [ ] Unique priority — verify it doesn't collide with `LocalAuthProvider (0)` / `LDAPAuthProvider`
- [ ] Register in the plugin's `<Plugin>PluginConfiguration::initialize()` via `$chain->addProvider(new Provider())`
- [ ] If config-gated: read from `ConfigService` first; only register when enabled
- [ ] Add a `UserAuthProvider` row pattern if users need to be tagged as "authenticated via this provider"

## Configure LDAP for a new deployment

- [ ] Admin → Configuration → LDAP → enter host/port/credentials/search base
- [ ] Test bind via "Test Connection" button (uses `LDAPService::testConnection`)
- [ ] Configure user mapping (LDAP attribute → user field)
- [ ] Set sync interval (1-23 hours)
- [ ] Verify `php bin/console orangehrm:ldap-sync-user` runs successfully
- [ ] Verify host cron is set up (see `scheduled-jobs`) so the periodic sync actually happens
- [ ] Have a non-LDAP admin account available as fallback in case LDAP misconfiguration locks people out

## Register an OAuth2 client for an external integration

- [ ] Admin → Configuration → Register OAuth Client
- [ ] Choose grant type (authorization code is most common)
- [ ] Set redirect URI(s)
- [ ] Record the `client_id` and one-time-shown `client_secret`
- [ ] (External app side) implement the OAuth2 authorization-code flow against `/oauth2/authorize` + `/oauth2/token`
- [ ] Verify token issuance: a successful login should produce rows in `ohrm_oauth_access_token`

## Things that bite

- **Provider priorities must be unique** — `AuthProviderChain::addProvider` throws if two providers share a priority. If you copy-paste a `getPriority()` and forget to change it, the plugin breaks on `initialize()`.
- **`authenticate()` returning false vs throwing** is the difference between "next provider, please" and "stop and show this error." Get it wrong and the user sees confusing behavior.
- **The `UserAuthProvider` table is per-user-per-provider**, not per-session. Multiple rows per user are fine and indicate "this user can authenticate via these providers."
- **LDAP-synced users have NULL `users.user_password`** — they can't log in via Local because `password_verify($plain, null)` returns false. Don't try to "fix" the null; it's intentional and correct.
- **OAuth2 access tokens are stored encrypted** in `ohrm_oauth_access_token` — but they're not column-encrypted via `Cryptographer`. The OAuth2 server uses its own `oauth.encryption_key` (see `config`) for signing/encryption. Don't confuse the two encryption layers.
- **OIDC `client_secret` IS column-encrypted** (`AuthProviderExtraDetails::clientSecret` via an EntityListener) — see `security-primitives`. The key file is shared with employee SSN encryption.
- **LDAP sync runs hourly by default** — a brand-new LDAP user can't log in until the next sync (or you trigger it manually). The auth provider's just-in-time sync handles this on login, but only if LDAP bind succeeds; if your provider has a "must exist before auth" model, users get locked out between syncs.
- **The chain doesn't catch exceptions from `authenticate()`.** A thrown `AuthenticationException` propagates up to the login controller, which renders the appropriate error page. **Don't catch and swallow** in your provider — let the chain propagate.
- **`AuthenticationException::sessionExpired()` vs invalid creds** are different — sessions get force-logged-out by `AuthenticationSubscriber` (see `authorization`), not by providers. Providers handle initial login only.
