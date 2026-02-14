# Skill : Dev Tools

## Description
Développement depuis Telegram : coder, pusher, monitorer, debugger.

## Commandes
- "Code [description]" → coder depuis Telegram, push GitHub, screenshot résultat
- "Monitoring" → vérifier tous les sites/services, alerter si panne
- "Bot Telegram [description]" → créer un bot Telegram en Node.js
- "Script bash [description]" → écrire et tester un script
- "PR status" → lister les PR ouvertes sur tous les repos
- "Deploy [repo]" → déployer via GitHub Actions ou script

## Stack
- Node.js dans le container OpenClaw
- GitHub pour le versioning
- Docker pour les services
- Bash pour les scripts système

## Workflow code depuis Telegram
1. Antoine décrit ce qu'il veut en langage naturel
2. Léo code dans le workspace
3. Léo commit sur une branche + crée une PR
4. Léo envoie un résumé + screenshot si applicable
5. Antoine valide et merge

## Règles
- Toujours branche + PR, jamais push sur main
- Tester avant de committer
- Commenter le code
- Jamais d'accès root sauf pour les tâches server-admin autorisées
