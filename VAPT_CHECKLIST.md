# VAPT Findings -> Fixes

A practical map from common audit findings to the control in this repo.

| # | Typical finding | Risk | Fix in this repo | Verified by |
|---|---|---|---|---|
| 1 | Missing security headers (CSP, X-Frame-Options, HSTS) | Clickjacking, XSS impact | `SecurityHeaders` middleware | `test_security_headers_present` |
| 2 | No brute-force protection on login | Credential stuffing | `throttle:login` limiter (5/min) | Rate limiter config |
| 3 | No account lockout | Password guessing | Lock 15 min after 5 failures | `test_account_locks_after_repeated_failures` |
| 4 | Username enumeration via login errors | Targeted attacks | Same generic error for all failures | `test_login_does_not_reveal_whether_email_exists` |
| 5 | Session fixation | Session hijack | `session()->regenerate()` on login | Code review |
| 6 | IDOR on resource IDs | Data leak | `DocumentPolicy` ownership check | `test_user_cannot_view_another_users_document` |
| 7 | Unrestricted file upload | RCE, malware | MIME sniffing, allow-list, size cap, random name, private disk | `test_upload_rejects_disallowed_type` |
| 8 | Weak password policy | Account takeover | `StrongPassword` rule (12+ chars, mixed) | Validation rule |
| 9 | No audit trail | Cannot investigate incidents | `AuditLogger` writes to `audit_logs` | Migration + usage |
| 10 | Secrets in repo | Credential leak | `.env` ignored; `.gitignore` | Repo scan |
| 11 | SQL injection | Data breach | Eloquent/query builder bindings only; no raw string SQL | Code review |
| 12 | XSS in output | Session theft | Blade `{{ }}` escaping + CSP | Code review |
| 13 | CSRF | Forced actions | Laravel CSRF middleware (keep enabled for web routes) | Framework default |
| 14 | Verbose errors / debug on | Info disclosure | `APP_DEBUG=false` in production | Deployment checklist |

## Remediation tracking template
| Finding ID | Severity | Owner | Fix | Target date | Status |
|---|---|---|---|---|---|

## Production checklist
- `APP_DEBUG=false`, `APP_ENV=production`
- HTTPS only; enable HSTS
- Run `composer audit` regularly
- Keep dependencies updated
- Back up and rotate keys
