# Secure Laravel Starter

A hardening layer for Laravel 11 apps, built around the OWASP Top 10 and typical VAPT audit findings. Sample code only; no employer or customer data.

![tests](https://github.com/mohammednooralam7-ops/secure-laravel-starter/actions/workflows/tests.yml/badge.svg)

## What it adds
| Area | File |
|---|---|
| Security headers (CSP, HSTS, X-Frame-Options, etc.) | `app/Http/Middleware/SecurityHeaders.php` |
| Login rate limiting + account lockout | `app/Http/Controllers/Auth/SecureLoginController.php`, `app/Providers/RateLimitServiceProvider.php` |
| Audit logging | `app/Services/AuditLogger.php`, migration |
| Safe file upload | `app/Services/SafeUpload.php` |
| Strong password policy | `app/Rules/StrongPassword.php` |
| IDOR protection example | `app/Policies/DocumentPolicy.php` |
| Tests proving each control | `tests/Feature/SecurityTest.php` |
| VAPT findings -> fixes | `VAPT_CHECKLIST.md` |

## Setup
```bash
composer create-project laravel/laravel secure-starter
cd secure-starter
# merge this package's folders into the project
# register middleware and provider as shown in INSTALL.md
php artisan migrate
php artisan test
```

## Principles
- Deny by default, validate every input, log every sensitive action
- No secrets in the repo; use `.env`
- Every control has a test
