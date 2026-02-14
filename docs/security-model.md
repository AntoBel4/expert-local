# Security model — Expert Local (OpenClaw A)

## Principles
- No public exposure for OpenClaw (localhost only).
- Access only through Tailscale Serve (tailnet only).
- Gateway protected by token (Authorization: Bearer).
- No secrets in the repository or PRs.
- PR-only workflow: every change via branch + PR + human validation.

## Secrets handling
- Secrets are stored on the VPS in /opt/openclaw/.env (chmod 600).
- Never paste tokens/keys into issues, commits, PR descriptions, logs.

## Publication control
- OpenClaw may generate drafts and PRs.
- OpenClaw must never deploy/publish directly.
- Any public action requires explicit human approval ("yes" only).

## Incident response (minimal)
- If a secret leaks: rotate immediately, invalidate tokens, audit recent PRs.
- If OpenClaw misbehaves: stop containers, disable Tailscale Serve, review logs.
