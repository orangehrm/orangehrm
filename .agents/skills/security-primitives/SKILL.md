---
name: security-primitives
description: Reference for OrangeHRM's cryptographic and password-handling primitives — the `Cryptographer` class (AES-256-GCM with random nonces for new ciphertext, legacy AES-128-ECB still accepted on decrypt for backward compatibility), `KeyHandler` for the file-based crypto key lifecycle (created at install when data encryption is enabled, stored at `lib/confs/cryptokeys/key.ohrm`), `EncryptionHelperTrait` for accessing the Cryptographer from EntityListeners with the `encryptionEnabled()` guard, `PasswordHash` wrapping PHP's `password_hash`/`password_verify` with `PASSWORD_BCRYPT` cost 12, the `GCMAES256.` prefix marker that distinguishes new vs legacy ciphertext, the encrypt-on-write + decrypt-on-read symmetric round-trip pattern via EntityListeners, and the CSRF token handling via Symfony's `CsrfTokenManagerTrait`. Use whenever the user is adding a new sensitive column that needs encryption, debugging crypto failures, asking about key rotation, handling passwords, or implementing CSRF protection. Companion to `entities` (EntityListener side of encryption), `migrations` (column sizing — ciphertext needs 512 chars), `config` (where OAuth encryption keys are stored).
---

# Security primitives

OrangeHRM's security primitives are small, focused, and almost entirely contained in `src/plugins/orangehrmCorePlugin/Utility/`. There are exactly three concerns documented here:

1. **Field-level encryption at rest** (sensitive employee data — SSN, basic salary, SMTP password, OAuth client secret)
2. **Password hashing** (user passwords)
3. **CSRF tokens** (form submission protection)

The patterns are stable, well-established across the codebase, and don't change often. This skill is a reference, not a tutorial — the underlying crypto is delegated to OpenSSL and PHP's password functions, not hand-rolled.

## Field-level encryption — the moving parts

```
At install: KeyHandler::createKey()
  → generates a high-entropy string
  → writes it to lib/confs/cryptokeys/key.ohrm
  (only if the operator enabled "data encryption" during install)

At runtime: EntityListener intercepts persist / update events
  → use EncryptionHelperTrait
  → if encryptionEnabled() (i.e., the key file exists): getCryptographer()->encrypt(...)
  → stores the ciphertext back on the entity before flush
  → postUpdate / postLoad decrypts back so in-memory entity stays readable
```

Three classes in play: `KeyHandler` (the key file), `Cryptographer` (the actual encrypt/decrypt), `EncryptionHelperTrait` (the convenience accessor).

## `KeyHandler` — the key file

`OrangeHRM\Core\Utility\KeyHandler`. Manages the symmetric key stored at `lib/confs/cryptokeys/key.ohrm`.

```php
KeyHandler::keyExists(): bool                       // does the file exist?
KeyHandler::createKey(): void                       // creates if missing; throws if present
KeyHandler::readKey(): string                       // reads the file
```

**Lifecycle:**

- **Install time** — if the operator selected "enable data encryption" during install (`enableDataEncryption: y` in `cli_install_config.yaml` or the equivalent web installer step), `AppSetupUtility::writeKeyFile()` calls `KeyHandler::createKey()` once.
- **Runtime** — `KeyHandler::readKey()` is called by `EncryptionHelperTrait::getCryptographer()` to instantiate a Cryptographer.
- **Never rotated** — there's no key-rotation mechanism in the codebase. If the key file is lost, all encrypted fields are unrecoverable.

**Operational note:** The upgrader warns operators explicitly to **copy `lib/confs/cryptokeys/key.ohrm` from the old install to the new install** before upgrading. Lose that file and SSN / salary / SMTP password columns become un-decryptable.

### What the key file looks like

A 128-character string built from four md5 hashes of random ints, shuffled:

```php
// KeyHandler::createKey() simplified
$cryptKey = '';
for ($i = 0; $i < 4; $i++) {
    $cryptKey .= md5(random_int(10000000, 99999999));
}
$cryptKey = str_shuffle($cryptKey);
file_put_contents(self::getPathToKey(), $cryptKey);
```

It's not the actual AES key — it's a seed. `Cryptographer` derives:
- A 16-byte key for legacy AES-128-ECB (XOR fold of the seed into 16 bytes)
- A 32-byte key for AES-256-GCM (SHA-256 of the legacy key XOR-folded result with a domain-separator suffix `\x00OrangeHRM-GCM-v2`)

The derivation is deterministic so reading the same key file always produces the same AES keys.

## `Cryptographer` — AES-256-GCM with legacy fallback

`OrangeHRM\Core\Utility\Cryptographer`. Two-mode encrypt/decrypt with backward compatibility for older installs.

```php
$crypto = new Cryptographer($seed);                  // $seed from KeyHandler::readKey()

$cipher = $crypto->encrypt($plaintext);              // always new format
$plain  = $crypto->decrypt($cipher);                 // accepts both new and legacy
```

### New format — AES-256-GCM (since V5_8_x)

- **Algorithm**: AES-256-GCM via `openssl_encrypt('aes-256-gcm', ...)`
- **Nonce**: 12 random bytes per value
- **Tag**: 16-byte authentication tag
- **Storage**: `GCMAES256.<base64(nonce || ciphertext || tag)>`

```
GCMAES256.aGVsbG8gZXhhbXBsZSBjaXBoZXJ0ZXh0...
^^^^^^^^^^ ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
   prefix       base64 payload (~28 bytes overhead + ciphertext)
```

The `GCMAES256.` prefix is the **signal** that decryption uses the new path. Older ciphertext is hex-encoded and starts with hex characters; the prefix check distinguishes them.

### Legacy format — AES-128-ECB (pre-V5_8_x)

The OLD format used `phpseclib3\Crypt\AES` in ECB mode with the 16-byte XOR-folded key:

```
0123456789abcdef0123456789abcdef...  (hex string of raw ciphertext bytes)
```

ECB is deterministic (same plaintext → same ciphertext) and unauthenticated — both known weaknesses. V5_8_x replaced new encryption with AES-256-GCM but **still decrypts legacy ECB ciphertext** so existing data keeps working without forcing a re-encryption.

**When is legacy ciphertext rewritten to GCM?** Only when the row is updated. The pattern (see `entities` skill's EntityListener section):

```php
public function preUpdate(Employee $employee, PreUpdateEventArgs $args): void
{
    if ($this->encryptionEnabled() && $args->hasChangedField('ssnNumber')) {
        $employee->setSsnNumber(
            $this->getCryptographer()->encrypt($employee->getSsnNumber())
        );
    }
}
```

`hasChangedField('ssnNumber')` is `true` after a `postLoad` decryption + a UI edit, so the next persist re-encrypts using the new format. Untouched legacy rows stay in ECB until edited.

### Column sizing for ciphertext

Migration V5_8_1 widened columns that store Cryptographer output to **VARCHAR(512)**:

```php
// V5_8_1 representative
$string512 = ['Type' => Type::getType(Types::STRING), 'Length' => 512, 'Notnull' => false];
$this->getSchemaHelper()->changeColumn('hs_hr_employee', 'emp_ssn_num', $string512);
$this->getSchemaHelper()->changeColumn('ohrm_email_configuration', 'smtp_password', $string512);
$this->getSchemaHelper()->changeColumn('ohrm_auth_provider_extra_details', 'client_secret', $string512);
```

**When adding a new encrypted column**, size it for GCM ciphertext from the start — VARCHAR(512) is the safe baseline. Legacy hex ciphertext can be smaller; GCM with overhead + base64 needs more.

## `EncryptionHelperTrait` — the runtime accessor

`OrangeHRM\Core\Utility\EncryptionHelperTrait`. Static convenience for accessing the Cryptographer from anywhere that needs it — primarily EntityListeners.

```php
trait EncryptionHelperTrait
{
    protected static ?Cryptographer $cryptographer = null;

    protected static function getCryptographer(): ?Cryptographer
    {
        if (KeyHandler::keyExists() && !self::$cryptographer instanceof Cryptographer) {
            self::$cryptographer = new Cryptographer(KeyHandler::readKey());
        }
        return self::$cryptographer;
    }

    protected static function encryptionEnabled(): bool
    {
        return self::getCryptographer() instanceof Cryptographer;
    }
}
```

Three things:

1. **Static** — the cryptographer is held on a per-class basis (`self::$cryptographer`). One instance per listener class per process.
2. **`encryptionEnabled()` is the universal guard.** If encryption wasn't enabled during install, the key file doesn't exist, and `encryptionEnabled()` returns false. **Every listener must check this** before encrypting/decrypting:
   ```php
   if ($this->encryptionEnabled() && $args->hasChangedField('ssnNumber')) { ... }
   ```
   Without the guard, an instance without the key file errors on every save.
3. **No instantiation needed** — `EncryptionHelperTrait` lazy-builds the Cryptographer the first time you call `getCryptographer()`.

The trait is **already used by `BaseListener`** (see `entities` skill), so every concrete `EntityListener` automatically has it.

## The end-to-end encrypted-field pattern

Combining `entities`, `migrations`, and this skill:

### 1. Migration: widen the column for ciphertext

```php
// installer/Migration/V5_9_0/Migration.php
public function up(): void
{
    $opts = ['Type' => Type::getType(Types::STRING), 'Length' => 512, 'Notnull' => false];
    $this->getSchemaHelper()->changeColumn('ohrm_widget', 'sensitive_field', $opts);
}
```

### 2. Entity: mark for EntityListener (and column type)

```php
/**
 * @ORM\Entity
 * @ORM\Table(name="ohrm_widget")
 * @ORM\EntityListeners({"OrangeHRM\Entity\Listener\WidgetListener"})
 */
class Widget
{
    /**
     * @ORM\Column(name="sensitive_field", type="string", length=512, nullable=true)
     */
    private ?string $sensitiveField = null;
    // …
}
```

### 3. Listener: encrypt on write, decrypt on read

```php
namespace OrangeHRM\Entity\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use OrangeHRM\Entity\Widget;

class WidgetListener extends BaseListener
{
    public function prePersist(Widget $widget, LifecycleEventArgs $args): void
    {
        if ($this->encryptionEnabled()) {
            $widget->setSensitiveField(
                $this->getCryptographer()->encrypt($widget->getSensitiveField())
            );
        }
    }

    public function preUpdate(Widget $widget, PreUpdateEventArgs $args): void
    {
        if ($this->encryptionEnabled() && $args->hasChangedField('sensitiveField')) {
            $widget->setSensitiveField(
                $this->getCryptographer()->encrypt($widget->getSensitiveField())
            );
        }
    }

    public function postLoad(Widget $widget, LifecycleEventArgs $args): void
    {
        if ($this->encryptionEnabled()) {
            $widget->setSensitiveField(
                $this->getCryptographer()->decrypt($widget->getSensitiveField())
            );
        }
    }

    public function postUpdate(Widget $widget, LifecycleEventArgs $args): void
    {
        if ($this->encryptionEnabled()) {
            $widget->setSensitiveField(
                $this->getCryptographer()->decrypt($widget->getSensitiveField())
            );
        }
    }
}
```

The four callbacks form the symmetric round-trip:

- `prePersist` / `preUpdate` — encrypt before SQL `INSERT`/`UPDATE`
- `postLoad` / `postUpdate` — decrypt back so the in-memory entity is readable

**Why `postUpdate` decrypts:** after `preUpdate` encrypted the field, the entity in memory now holds ciphertext. If the same object is used after the save (e.g. for the response), it would expose ciphertext to the API. `postUpdate` restores plaintext.

**Why `hasChangedField()` in `preUpdate`:** ensures the field is only re-encrypted when it actually changed. A `postLoad`-decrypted value followed by a save without modification wouldn't otherwise know whether to re-encrypt — the change detector tells us.

### 4. Optional: handle missing key file

If your feature should still function without encryption (most do), the `if ($this->encryptionEnabled())` guard makes the listener a no-op when the key file is missing. The field is then stored as plaintext.

If your feature **requires** encryption, throw in `prePersist` / `preUpdate`:

```php
public function prePersist(Widget $widget, LifecycleEventArgs $args): void
{
    if (!$this->encryptionEnabled()) {
        throw new RuntimeException('Widget requires encryption — enable it via the installer');
    }
    // ...
}
```

But the codebase precedent is to make encryption optional and store plaintext when disabled — operators choosing not to enable encryption are aware of the tradeoff.

## Existing encrypted fields in the codebase

For reference:

| Entity | Field | Listener |
|---|---|---|
| `Employee` | `ssnNumber` (`emp_ssn_num`) | `EmployeeListener` |
| `EmployeeSalary` | `basicSalary` (`ebsal_basic_salary`) | `EmployeeSalaryListener` |
| `EmailConfiguration` | `smtpPassword` | `EmailConfigurationListener` |
| (OAuth) | `client_secret` (in `ohrm_auth_provider_extra_details`) | via handling code in OAuth flow, not via EntityListener |

When adding a new sensitive field, follow the same pattern as the existing three encrypted entities.

## Password hashing — `PasswordHash`

`OrangeHRM\Core\Utility\PasswordHash`. Thin wrapper around PHP's password functions.

```php
$hasher = new PasswordHash();

$hash = $hasher->hash($plainPassword);           // → bcrypt hash string
$ok   = $hasher->verify($plainPassword, $hash);  // → bool
```

Constants:
- `ALGORITHM = PASSWORD_BCRYPT`
- `COST = 12` (2^12 iterations)

**Don't roll your own password hashing.** PHP's `password_hash` / `password_verify` are the right primitives; `PasswordHash` exists to centralize the cost and algorithm choice. If you need to change either, change the constants — every hash/verify path goes through this class.

Used by:
- `UserService` for password changes
- Login flow (`AuthenticationService::credentialsAreValid` verifies against the stored hash)
- Password reset flow

**Password storage**: in `ohrm_user.user_password` as the bcrypt hash. The actual plaintext is never stored.

## CSRF tokens

Standard Symfony `CsrfTokenManager` is used. Access via:

```php
use OrangeHRM\Authentication\Traits\CsrfTokenManagerTrait;

class LoginController extends AbstractVueController
{
    use CsrfTokenManagerTrait;

    public function preRender(Request $request): void
    {
        $token = $this->getCsrfTokenManager()->getToken('login')->getValue();
        // pass to Vue as a prop, or inject into form
    }
}
```

CSRF tokens are tied to the session (via `SessionTokenStorage`). The token is generated server-side, included in the login form / sensitive submission, and validated on the controller side before processing the request.

**For Vue components** that hit REST API endpoints behind authentication, CSRF is **not** strictly required (the auth flow uses session cookies + Same-Origin requests). For the **login flow specifically**, CSRF is enforced because the request changes session state.

If you're adding a new authentication-related controller (forgot password, reset), look at the existing `LoginController` / `ResetPasswordController` flow for the token-generation + validation pattern.

## Other utilities worth knowing about

| Class | Use |
|---|---|
| `Base64Url` (`Utility/Base64Url.php`) | URL-safe base64 (replaces `+` `/` `=` for safe inclusion in URLs / query strings). Used by reset-password tokens, JWT-style ID tokens. |
| `Sanitizer` (`Utility/Sanitizer.php`) | HTML sanitization for user-generated content (Buzz posts). Wraps `enshrined/svg-sanitize` and HTML-purifier-style cleanup. **Always use this for HTML strings that came from users.** |

---

# Recipes

## Recipe 1 — Encrypt a new sensitive field end-to-end

See "The end-to-end encrypted-field pattern" above for the full four-step recipe (migration sizes the column, entity references the listener, listener implements the four callbacks, optional require-encryption check).

## Recipe 2 — Verify a password (login flow)

```php
use OrangeHRM\Core\Utility\PasswordHash;

class LoginService
{
    private PasswordHash $hasher;

    public function __construct()
    {
        $this->hasher = new PasswordHash();
    }

    public function isValidLogin(string $username, string $plainPassword): bool
    {
        $user = $this->getUserDao()->findByUsername($username);
        if (!$user) {
            return false;
        }
        return $this->hasher->verify($plainPassword, $user->getUserPassword());
    }
}
```

Don't try to compare passwords with `==` or `===` against stored hashes — `password_verify` is constant-time and handles the bcrypt format. Use it.

## Recipe 3 — Hash a new password on user save

```php
public function saveNewUser(User $user, string $plainPassword): User
{
    $hasher = new PasswordHash();
    $user->setUserPassword($hasher->hash($plainPassword));
    return $this->getUserDao()->saveUser($user);
}
```

Always hash before persisting. The Doctrine entity stores the hash; the plaintext should never appear in the DB.

## Recipe 4 — Sanitize user-generated HTML

```php
use OrangeHRM\Core\Utility\Sanitizer;

$cleanHtml = (new Sanitizer())->sanitize($userInput);
```

For any HTML coming from a user (rich text posts, comment bodies) that will be rendered back to other users — always sanitize. Plain-text fields don't need this; only when the output is rendered as HTML.

## Recipe 5 — Generate a one-time URL-safe token

```php
use OrangeHRM\Core\Utility\Base64Url;

$randomBytes = random_bytes(32);
$token = Base64Url::encode($randomBytes);
// $token is now URL-safe — can go in a query string
```

Used by password-reset flow to generate the token that goes in the email link.

---

# Checklists

## Add a new encrypted column

- [ ] Migration sizes the column to VARCHAR(512) via `SchemaHelper::changeColumn` (see `migrations` skill)
- [ ] Entity declares `length=512, nullable=true, type="string"` on the column
- [ ] Entity adds `@ORM\EntityListeners({"...Listener"})` annotation
- [ ] Listener class extends `BaseListener` (which `use`s `EncryptionHelperTrait`)
- [ ] Listener implements `prePersist`, `preUpdate`, `postLoad`, `postUpdate` — the symmetric four
- [ ] Every callback guards with `if ($this->encryptionEnabled())`
- [ ] `preUpdate` additionally guards with `$args->hasChangedField('fieldName')`
- [ ] Documented in CHANGELOG that the field is encrypted at rest (operators need to know to copy the key file on upgrade)

## Add password handling for a new user-like entity

- [ ] Entity stores the **hash** column (typically VARCHAR(255)), not the plaintext
- [ ] Hash on persist: `(new PasswordHash())->hash($plainPassword)`
- [ ] Verify on login: `(new PasswordHash())->verify($plainPassword, $storedHash)`
- [ ] Don't reuse the same hash for tokens — generate fresh `random_bytes()` for those

## Add CSRF protection to a new sensitive form

- [ ] Controller `use CsrfTokenManagerTrait`
- [ ] In `preRender`, generate token via `getCsrfTokenManager()->getToken('namespace')->getValue()`
- [ ] Pass to Vue as a prop
- [ ] Vue submits the token with the form
- [ ] Controller validates on receive — see `LoginController` for the working example

## Things that bite

- **Losing the key file = unrecoverable data.** `lib/confs/cryptokeys/key.ohrm` must be backed up alongside the database. The upgrader screen specifically calls this out.
- **`encryptionEnabled()` check is mandatory** in every encrypt/decrypt call. Without it, an instance without the key file errors on every save. The codebase pattern always guards.
- **Legacy ECB ciphertext is still in the DB** for old rows. The decrypt path handles both formats automatically (via the `GCMAES256.` prefix check), so you don't need special-case logic — but be aware when looking at raw DB values that you might see two different encodings side by side.
- **`hasChangedField()` in `preUpdate` is the only thing keeping encryption symmetric.** Without it, every save re-encrypts the already-encrypted ciphertext (since the in-memory value is plaintext after `postLoad` but the DB has ciphertext) — leading to double-encrypted values that won't decrypt cleanly.
- **AES-256-GCM ciphertext includes a 16-byte authentication tag.** Tampered ciphertext **throws** on decrypt; it doesn't silently produce garbage. This is intentional (authenticated encryption) — but it means a corrupt row breaks the read. Logging + alerting is wise.
- **`PasswordHash::COST = 12`** is set globally; changing it makes existing hashes still valid for verify, but new hashes are slower. Don't tune this without measuring login latency.
- **`password_verify` is constant-time** — don't try to shortcut with string comparisons. Timing attacks against bcrypt verify are mitigated specifically because of this.
- **CSRF tokens are session-scoped** — they're not transferable between sessions. If a user logs out and back in, old tokens are invalid (good).
- **`Sanitizer` is the only HTML-safe path for user content.** Don't try to write your own escape function or rely on PHP `htmlspecialchars()` for HTML-containing fields — it won't strip script tags or sanitize SVG.
