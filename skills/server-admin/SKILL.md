# Skill : Server Admin

## Description
Gestion du VPS Hetzner et du serveur maison (Nextcloud). Backups, monitoring,
rangement de fichiers, sync, sécurité.

## Infrastructure
- VPS Hetzner : 2 CPU, 4Go RAM, 38Go disque, Ubuntu
- Docker containers : openclaw, n8n ×2, caddy, gotenberg, prospect-app
- Serveur maison : Nextcloud (sync fichiers, photos)

## Containers — NE JAMAIS TOUCHER sans autorisation :
- n8n-docker-n8n-1 (port 5678)
- n8n-docker-n8n-coaching-1 (port 5679)
- n8n-expert-local (port 5680)
- n8n-docker-caddy-1 (ports 80/443)
- n8n_expert_local-gotenberg-1

## Commandes disponibles
- "Backup" → script de sauvegarde chiffrée (repos GitHub, exports, fichiers VPS)
- "Monitoring" → vérifier l'état de tous les services + alertes
- "Espace disque" → rapport d'utilisation disque
- "Rangement [dossier]" → scanner, renommer, classer par type
- "Sync Nextcloud" → synchroniser et dédupliquer
- "Archivage photos" → trier par date/lieu, compresser, créer albums
- "Moteur de recherche" → indexer fichiers et chercher en langage naturel
- "Docs admin" → gérer l'arborescence /impots, /assurances, /immobilier, /santé

## Scripts de backup
- Rotation 30 jours
- Chiffrement GPG
- Cron quotidien à 3h du matin
- Cibles : repos GitHub, exports Notion, fichiers workspace, configs Docker

## Règles
- JAMAIS de rm -rf — toujours archiver d'abord
- JAMAIS toucher aux containers n8n
- Toujours demander confirmation avant suppression
- Logger toutes les actions dans /var/log/leo-admin.log
