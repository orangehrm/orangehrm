---
name: mail
description: Reference for OrangeHRM's email system — `EmailService` (the main service for composing and sending mail), the `EmailConfiguration` entity that stores SMTP/sendmail settings (with `smtpPassword` encrypted at rest), `MailerSubscriber` that processes the email queue on `KernelEvents::TERMINATE` (deferred sending so the user gets their response before SMTP latency), the Twig template structure under per-plugin `Mail/templates/<locale>/<eventName>/`, `queueEmailNotifications()` for the standard event-driven flow, and the `Mailer` / `MailMessage` / `MailTransport` low-level utilities wrapping Symfony Mailer. Use whenever the user is sending an email (notification on save, password reset, leave-approval notice), adding a new email template, debugging "why didn't the email send", or asking about SMTP configuration. Companion to `events` (event subscribers trigger emails), `security-primitives` (SMTP password is encrypted via the EmailConfigurationListener), `config` (`KEY_SENDMAIL_PATH`), `services` (EmailService is a typical service consumer).
---

# Mail — sending emails

OrangeHRM's mail system is **queue-and-deferred-send by default**: features queue an email by calling `EmailService::queueEmailNotifications()`, and a `KernelEvents::TERMINATE` subscriber (`MailerSubscriber`) flushes the queue **after** the response has been sent to the user. This means SMTP latency doesn't impact request latency.

Under the hood it uses **Symfony Mailer** (`symfony/mailer ~5.4`), with Twig templates per locale + per event. SMTP settings live on the `EmailConfiguration` entity with the password encrypted at rest.

This skill covers the standard pattern (queue + template + event subscriber) and the lower-level direct-send path for edge cases.

## The actors

```
Service does something
  → dispatches an event (see `events` skill)
     → EventSubscriber catches it
        → calls $emailService->queueEmailNotifications('event.name', $recipients, $performer, $event)
           → EmailService picks templates from <plugin>/Mail/templates/<locale>/<eventName>/
           → renders subject + body via Twig + the event payload
           → enqueues a row in ohrm_email_queue (via EmailQueueService)
           → sets a cache flag: 'core.send_email' = true
  ← Response sent to user
KernelEvents::TERMINATE
  → MailerSubscriber sees the cache flag
     → reads queued rows from ohrm_email_queue
     → for each: configures SMTP from EmailConfiguration → Mailer::send()
     → marks rows sent
```

**Five components** to be aware of:

| Component | Role |
|---|---|
| `EmailService` (Core plugin) | Compose + queue + send. The thing services and subscribers call. |
| `EmailQueueService` | Read/write the `ohrm_email_queue` table. Internal to `EmailService`. |
| `MailerSubscriber` (Core/Subscriber) | Drains the queue on TERMINATE. |
| `EmailConfiguration` entity | SMTP host / port / auth / encrypted password. One row. Configured in Admin → Configuration → Email Configuration. |
| `Mail/templates/<locale>/...` (per plugin) | Twig template pairs: `*Subject.txt.twig` + `*Body.html.twig`. |

## The standard pattern — `queueEmailNotifications`

This is **90% of email flows in OrangeHRM**. Used in `LeaveEventSubscriber`, `EmployeeEventSubscriber`, and others.

```php
namespace OrangeHRM\Leave\Subscriber;

use OrangeHRM\Core\Service\EmailService;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use OrangeHRM\Leave\Event\LeaveAllocate;
use OrangeHRM\Leave\Event\LeaveApply;
use OrangeHRM\Leave\Event\LeaveEvent;

class LeaveEventSubscriber extends AbstractEventSubscriber
{
    private ?EmailService $emailService = null;

    public static function getSubscribedEvents(): array
    {
        return [
            LeaveEvent::APPLY  => [['onAllocateEvent', 0]],
            LeaveEvent::APPROVE => [['onStatusChangeEvent', 0]],
            // …
        ];
    }

    public function getEmailService(): EmailService
    {
        return $this->emailService ??= new EmailService();
    }

    public function onAllocateEvent(LeaveAllocate $event): void
    {
        $emailName       = $event instanceof LeaveApply ? 'leave.apply' : 'leave.assign';
        $workflow        = $event->getWorkflow();
        $recipientRoles  = $workflow->getDecorator()->getRolesToNotify();
        $performerRole   = strtolower($workflow->getRole());

        $this->getEmailService()->queueEmailNotifications(
            $emailName,        // ← matches the template directory name
            $recipientRoles,   // ← who gets it ('admin', 'supervisor', 'ess')
            $performerRole,    // ← who triggered it
            $event,            // ← payload passed to the template
        );
    }
}
```

**Four arguments to `queueEmailNotifications`:**

1. **`$emailName`** — string like `'leave.apply'`, `'leave.approve'`, `'pim.employee_added'`. Matches the template directory: `<plugin>/Mail/templates/<locale>/<emailName>/...`.
2. **`$recipientRoles`** — array of role names (`'admin'`, `'supervisor'`, `'ess'`). The service resolves each role to a list of actual employees + email addresses.
3. **`$performerRole`** — the role of whoever triggered the event. Used in template rendering ("Your application has been received" vs "An application has been received from..." depending on whether the recipient is the performer or someone else).
4. **`$event`** — the event object itself. The template gets access to it as `event` in Twig context.

`EmailService` resolves the template path via `getEmailTemplateBestMatch($emailName, $performerRole, $recipientRole, $locale)`:

- Locale: from `ConfigService::getAdminLocalizationDefaultLanguage()` (typically `en_US`)
- Tries `<plugin>/Mail/templates/<locale>/<emailName>/<performerRole>To<recipientRole>{Subject,Body}.{txt,html}.twig`
- Falls back through several layers (skip role-specific, fall back to `en_US` if locale missing)

## Template structure

```
src/plugins/orangehrm{Plugin}Plugin/Mail/templates/
  en_US/
    apply/                                    ← matches $emailName 'leave.apply'
      leaveApplicationSubject.txt.twig        ← email subject (plain text)
      leaveApplicationBody.html.twig          ← email body (HTML)
      leaveApplicationSubscriberSubject.txt.twig  ← variant for subscribers (everyone CC'd)
      leaveApplicationSubscriberBody.html.twig
    approve/
      leaveApprovalSubject.txt.twig
      leaveApprovalBody.html.twig
    reject/
    cancel/
    assign/
```

**Naming convention** for templates inside a directory:
- `<verb><Subject|Body>.txt.twig` for the primary recipient (the employee whose leave was approved, say)
- `<verb>Subscriber<Subject|Body>.html.twig` for subscribed third parties (supervisors, admins)

The full lookup path is **service-internal** — you don't compute it. Just match the existing directory structure when adding a new email.

### Template content

```twig
{# leaveApplicationSubject.txt.twig #}
Leave Application — {{ event.getEmployee().getFullName() }}
```

```twig
{# leaveApplicationBody.html.twig #}
<p>Hi {{ event.getEmployee().getFullName() }},</p>

<p>Your leave application for {{ event.getStartDate()|date('Y-m-d') }} has been received.</p>

<p>Status: <strong>{{ event.getStatus() }}</strong></p>

<p>— OrangeHRM</p>
```

The Twig variable `event` is the event object passed in. You call its getters in the template. Subject templates are `.txt.twig` (single-line plain text); body templates are `.html.twig`.

### Adding language packs

`ConfigService::FALLBACK_TEMPLATE_LOCALE = 'en_US'`. If a localized template isn't found, the service falls back to `en_US`. To add a Spanish leave email: drop `es_ES/apply/leaveApplicationSubject.txt.twig` etc., alongside the English versions.

## The email queue — `ohrm_email_queue`

`EmailService::queueEmailNotifications()` doesn't send immediately. It:

1. Renders subject + body for each recipient (via Twig).
2. Inserts a row in `ohrm_email_queue` with `to`, `subject`, `body`, `from`, etc.
3. Sets a cache item `'core.send_email' = true` so the subscriber knows there's work.

```sql
SELECT id, to_email, subject, status, sent_at FROM ohrm_email_queue ORDER BY id DESC LIMIT 5;
```

The queue rows persist — sent rows aren't deleted, just marked. Useful for auditing "did the welcome email actually go out?"

## `MailerSubscriber` — the queue drainer

`OrangeHRM\Core\Subscriber\MailerSubscriber`. Listens on `KernelEvents::TERMINATE` (the very last lifecycle hook — fires after the response is sent to the user).

```php
public function onTerminateEvent(TerminateEvent $event): void
{
    $cacheItem = $this->getCache()->getItem('core.send_email');
    if ($cacheItem->isHit() && $cacheItem->get()) {
        // drain the queue: fetch unsent rows, send via Mailer, mark sent
    }
}
```

The cache flag is a short-circuit: most requests have no queued mail, and a DB query on every TERMINATE event would be wasteful. Only requests that actually queued mail flip the flag.

**Three implications:**

1. **Emails are sent only when a queueing request reaches TERMINATE.** A scheduled job that queues but doesn't go through the kernel won't drain its own queue. Either run a scheduler that drains it (the `runScheduleCommand` / Crunz integration handles this), or call `EmailService::sendQueuedEmails()` explicitly.
2. **A request that fatal-errors after queueing but before TERMINATE won't send the email.** The queue row persists, so a later request can pick it up — but only if it sets the flag itself. There's no perpetual sweep.
3. **SMTP latency is invisible to the user.** Even an unresponsive mail server only delays TERMINATE, not the response.

## Lower-level direct send — `sendEmail`

When you need to send a single email **right now** (admin sending a test, password-reset link), use the lower-level methods:

```php
$emailService = new EmailService();

$emailService->setMessageSubject('Welcome');
$emailService->setMessageFrom(['admin@example.com' => 'OrangeHRM']);
$emailService->setMessageTo(['user@example.com']);
$emailService->setMessageBody('<p>Welcome to OrangeHRM!</p>');

$ok = $emailService->sendEmail();   // returns bool
```

This bypasses the queue and sends synchronously. **Use sparingly** — it blocks the request for as long as SMTP takes. The standard async-via-queue pattern is preferred unless the user is explicitly waiting for the email outcome.

`EmailService::sendTestEmail($toEmail)` exists for the Admin "Send Test Email" button — uses the configured SMTP settings to verify they work.

## SMTP configuration — `EmailConfiguration`

`OrangeHRM\Entity\EmailConfiguration` — one row per instance (admin-configurable):

```
mail_type            sendmail | smtp                   (which transport to use)
sent_as              'noreply@example.com'             (From address)
smtp_host
smtp_port
smtp_auth_type       none | login                      (whether SMTP auth is required)
smtp_username
smtp_password        encrypted                          ← see security-primitives skill
smtp_security        none | tls | ssl                  (TLS variant)
```

Admin UI: **Admin → Configuration → Email Configuration**.

The `smtp_password` is encrypted at rest by `EmailConfigurationListener` (the only OHRM EntityListener for this entity — see `entities` skill and `security-primitives` skill for the pattern).

`sendmail_path` for the `sendmail` transport comes from `ConfigService::KEY_SENDMAIL_PATH` (default `/usr/sbin/sendmail`). Override via config if the path differs.

`EmailService::getMailer()` builds a Symfony Mailer instance from these settings — picks SMTP or sendmail, configures auth + TLS, returns a `Mailer` (OHRM's thin wrapper around `Symfony\Component\Mailer\MailerInterface`).

## Adding a new email — end-to-end

Concrete walkthrough for "send an email when a Widget is saved":

### 1. Define an event (see `events` skill)

```php
// src/plugins/orangehrmXPlugin/Event/WidgetEvents.php
class WidgetEvents
{
    public const WIDGET_SAVED = 'x.widget_saved';
}

// src/plugins/orangehrmXPlugin/Event/WidgetSavedEvent.php
class WidgetSavedEvent extends Event
{
    public function __construct(private Widget $widget) {}
    public function getWidget(): Widget { return $this->widget; }
}
```

### 2. Dispatch from the service (see `services` skill)

```php
class WidgetService
{
    use EventDispatcherTrait;

    public function saveWidget(Widget $w): Widget
    {
        $w = $this->getWidgetDao()->saveWidget($w);
        $this->getEventDispatcher()->dispatch(new WidgetSavedEvent($w), WidgetEvents::WIDGET_SAVED);
        return $w;
    }
}
```

### 3. Add the templates

```
src/plugins/orangehrmXPlugin/Mail/templates/en_US/widget.saved/
  widgetSavedSubject.txt.twig
  widgetSavedBody.html.twig
```

`widgetSavedSubject.txt.twig`:
```twig
Widget "{{ event.getWidget().getName() }}" saved
```

`widgetSavedBody.html.twig`:
```twig
<p>Hi,</p>
<p>The widget <strong>{{ event.getWidget().getName() }}</strong> has been saved.</p>
<p>— OrangeHRM</p>
```

### 4. Write a subscriber that queues the email

```php
namespace OrangeHRM\X\Subscriber;

use OrangeHRM\Core\Service\EmailService;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use OrangeHRM\X\Event\WidgetEvents;
use OrangeHRM\X\Event\WidgetSavedEvent;

class WidgetMailSubscriber extends AbstractEventSubscriber
{
    private ?EmailService $emailService = null;

    public static function getSubscribedEvents(): array
    {
        return [WidgetEvents::WIDGET_SAVED => [['onSaved', 0]]];
    }

    public function getEmailService(): EmailService
    {
        return $this->emailService ??= new EmailService();
    }

    public function onSaved(WidgetSavedEvent $event): void
    {
        $this->getEmailService()->queueEmailNotifications(
            'widget.saved',           // matches template dir name
            ['admin'],                // notify admins
            'system',                 // performer role
            $event,
        );
    }
}
```

### 5. Register the subscriber (see `events` skill)

```php
// In XPluginConfiguration::initialize()
$this->getEventDispatcher()->addSubscriber(new WidgetMailSubscriber());
```

That's it. On the next widget save:
- The service dispatches `WIDGET_SAVED`
- The subscriber queues a templated email to admins
- The response goes back to the user
- After response, `MailerSubscriber` drains the queue → SMTP send

## Configuring email in dev

For local development:

1. Admin → Configuration → Email Configuration
2. Set `mail_type = smtp`, `smtp_host = mailhog` (if using mailhog container) or `host.docker.internal` with a local catch-all
3. Set credentials if your SMTP requires them
4. "Send Test Email" button (uses `EmailService::sendTestEmail`) — confirm it lands

Without a working SMTP target, queued emails just sit unsent. They don't fail the application — `MailerSubscriber` catches exceptions and logs them — but they pile up in the queue table.

---

# Recipes

## Recipe 1 — Queue an email triggered by a domain event

See "Adding a new email — end-to-end" above. Five steps: event class, dispatch from service, templates, subscriber, register subscriber.

## Recipe 2 — Send a single email immediately (not queued)

```php
$emailService = new EmailService();
$emailService->setMessageFrom(['noreply@orangehrm.com' => 'OrangeHRM']);
$emailService->setMessageTo(['user@example.com']);
$emailService->setMessageSubject('Welcome');
$emailService->setMessageBody('<p>Welcome to OrangeHRM!</p>');

$ok = $emailService->sendEmail();
if (!$ok) {
    // SMTP failed; the error was logged. Decide whether to surface to the user.
}
```

Use only when the user is explicitly waiting for the email (password reset, "send test email").

## Recipe 3 — A reset-password flow email

```php
class RequestPasswordService
{
    use UserServiceTrait;

    public function requestReset(string $email): void
    {
        $user = $this->getUserService()->findByEmail($email);
        if (!$user) return;                                  // don't reveal whether email exists

        $token = Base64Url::encode(random_bytes(32));
        $this->saveResetToken($user, $token);

        $emailService = new EmailService();
        $emailService->setMessageTo([$email]);
        $emailService->setMessageFrom(['noreply@orangehrm.com' => 'OrangeHRM']);
        $emailService->setMessageSubject('Reset your OrangeHRM password');
        $emailService->setMessageBody(
            sprintf('<a href="%s/auth/reset/%s">Reset password</a>',
                $this->getBaseUrl(), $token)
        );
        $emailService->sendEmail();   // immediate — user is at the "request" screen waiting
    }
}
```

`sendEmail()` (not `queueEmailNotifications`) — the user is staring at "We've sent you an email" and needs it actually sent before they refresh.

## Recipe 4 — Email with localized template

Drop new locale-specific template directories alongside the existing `en_US/`:

```
Mail/templates/
  en_US/widget.saved/widgetSavedSubject.txt.twig
  en_US/widget.saved/widgetSavedBody.html.twig
  es_ES/widget.saved/widgetSavedSubject.txt.twig
  es_ES/widget.saved/widgetSavedBody.html.twig
  fr_FR/widget.saved/...
```

`EmailService` picks based on the user's configured locale (per recipient — different recipients can get different language versions of the same email). Falls back to `en_US` if missing.

---

# Checklists

## Add a new event-triggered email

- [ ] Define an event class (see `events` skill) and event-name constant
- [ ] Dispatch from the service after the persistence operation
- [ ] Create `Mail/templates/en_US/<emailName>/` directory in the plugin
- [ ] Add `<verb>Subject.txt.twig` (subject) + `<verb>Body.html.twig` (body) using the `event` Twig variable
- [ ] Write a subscriber class extending `AbstractEventSubscriber` that calls `$emailService->queueEmailNotifications($emailName, $recipientRoles, $performerRole, $event)`
- [ ] Register the subscriber in the plugin's `<Plugin>PluginConfiguration::initialize()` (see `events` skill)
- [ ] Test by triggering the event + verifying a row in `ohrm_email_queue` is created, then verifying it sends

## Add localized templates

- [ ] Drop matching `<locale>/<emailName>/` directories beside the existing `en_US/`
- [ ] Match the same filename pattern (`<verb>Subject.txt.twig`, etc.)
- [ ] No code changes — `EmailService::getEmailTemplateBestMatch` handles the lookup

## Debug "email didn't send"

- [ ] **Was it queued?** `SELECT * FROM ohrm_email_queue ORDER BY id DESC LIMIT 5` — if nothing, the subscriber didn't fire or didn't call queue
- [ ] **Is the subscriber registered?** Check `<Plugin>PluginConfiguration::initialize()` for `addSubscriber()` — missing registration is the most common cause (see `events` skill)
- [ ] **Was the event dispatched?** Add a temporary `error_log()` in the subscriber method to confirm it fired
- [ ] **Did MailerSubscriber run?** Check `src/log/orangehrm.log` for `MailerSubscriber >>` entries — every drain attempt logs
- [ ] **Is SMTP configured?** Admin → Configuration → Email Configuration. "Send Test Email" should succeed.
- [ ] **Are credentials right?** SMTP password is encrypted at rest — if `KeyHandler::keyExists()` returns false but the password column has GCM ciphertext, the EntityListener can't decrypt, and auth will fail with garbage credentials.
- [ ] **Is the template path right?** A missing template means EmailService logs "template not found" and the email isn't queued. Check the directory name matches `$emailName` exactly.

## Things that bite

- **Emails are queued, not sent immediately.** `MailerSubscriber` flushes on TERMINATE. If your code expects the email to be in transit by the time the function returns, **use `sendEmail()`, not `queueEmailNotifications()`**.
- **A request that errors out before TERMINATE drops the cache flag, not the queue row.** The row persists; the next request that queues something else will trigger drain and pick up the orphan. But on its own, the orphan sits indefinitely.
- **`KernelEvents::TERMINATE` doesn't fire for console commands.** Emails queued in a console command won't drain via the subscriber. The `php bin/console orangehrm:run-schedule ` flow has its own handling — or you can call `EmailService::sendQueuedEmails()` explicitly.
- **The Twig `event` variable is the raw event object.** Templates have full access to its getters; that's powerful but also a leak risk if you put sensitive fields on the event. Don't put plaintext passwords or tokens on an event payload.
- **Template filename casing matters on Linux.** `widgetSavedSubject.txt.twig` ≠ `WidgetSavedSubject.txt.twig`. Match exactly.
- **`smtpPassword` is encrypted at rest** — when working with the EmailConfiguration entity, use the EntityListener's decrypted value (which `postLoad` populates). Don't try to decrypt manually; the listener handles it.
- **The cache flag `'core.send_email'` is checked in `MailerSubscriber`** — if cache is broken or wiped between queue + TERMINATE, the flag is lost and the drain doesn't happen. Cache failures here are silent.
- **A failing template render throws** during `queueEmailNotifications`, blocking the request. Don't put complex logic in templates — keep them dumb, do the work in the event payload's getters.
