# Google OAuth setup

## Google Cloud

1. Create a project in Google Cloud Console.
2. Configure OAuth consent screen (app name, support email, domains).
3. Create **Web application** OAuth client.
4. **Authorised redirect URI:** `{APP_URL}/auth/google/callback`  
   Local example: `http://127.0.0.1:8001/auth/google/callback`

## Environment

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

## Application routes

| Route | Name |
|-------|------|
| GET `/auth/google` | `auth.google.redirect` |
| GET `/auth/google/callback` | `auth.google.callback` |
| GET `/auth/google/select-account-type` | `auth.google.select-account-type` |
| GET `/auth/google/complete-profile` | `auth.google.complete-profile` |
| GET `/auth/google/link-account` | `auth.google.link-account` |
| GET `/account/linked-accounts` | `account.linked-accounts` |

## Flow summary

- **New Google user:** OAuth → choose Client or Student → complete phone + terms → role-based portal redirect.
- **Returning Google user:** OAuth → login → portal redirect.
- **Email already registered:** password confirmation required before link (no auto-link by email alone).
- **Logged-in user:** Account → linked accounts → link/unlink Google.

See `docs/authentication/account-linking.md` and `docs/authentication/security.md`.
