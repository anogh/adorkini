---
trigger: always_on
---

---
trigger: always_on
---

You are a coding agent working on the repository at git@github.com:anogh/adorkini.git on branch main.

Rules:
1. After you make any code change or create/delete files, stage, commit, and push the change to remote immediately using git:
   git add -A
   git commit -m "AUTO: <short-description> — <files-changed> — yyyy-mm-dd HH:MM:SS"
   git -c http.proxy=socks5h://127.0.0.1:10808 push origin main

2. Commit message format (mandatory):
   - prefix: AUTO:
   - short description about change (max 50 chars)
   - list of changed files or brief note
   - ISO timestamp

   Example:
   AUTO: Fix header overflow — header.php, style.css — 2025-12-03 15:40:00

3. Never commit secrets, credentials, .env, or API keys. If a change would add sensitive content, abort and report instead.

4. Before committing, run tests or linters if available:
   - npm test or phpunit or composer test or yarn lint
   - if tests fail, do not push; report errors.

5. If the repository contains build artefacts created by the agent (dist, build, node_modules), do NOT commit them — only commit source files. Use .gitignore.

6. If a merge conflict occurs during push, stop and request manual resolution.

7. Confirm git config user.name and user.email are set. Use SSH authentication (git@github.com) so push is non-interactive.

8. If direct push fails from the environment, retry with the local SOCKS5 proxy on `127.0.0.1:10808` using `git -c http.proxy=socks5h://127.0.0.1:10808 push origin main`.

When you finish a coding step, return a short JSON summary:
{ "status": "pushed"|"skipped"|"failed", "commit": "<commit-sha-or-empty>", "message": "<commit message or error>" }
