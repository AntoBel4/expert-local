# Skill : Expert Local SEO

## Description
Gestion complète du SEO pour expert-local.fr. Génération de landing pages, audits,
pipeline SEO, posts LinkedIn, JSON-LD, sitemap, .htaccess, FAQ.

## Repo GitHub
- Repo : expert-local (déjà cloné dans le workspace)
- Branche de travail : toujours créer une nouvelle branche, jamais push sur main
- Workflow : branche → modifications → commit → PR

## Structure du site
- / : page d'accueil (index.html)
- /metier/ : dossiers par métier (coiffeur/, plombier/, electricien/, etc.)
- Chaque dossier métier contient des pages ville (ex: coiffeur/chartres.html)
- Design : midnight (#0a1628) + coral (#ff6b47) + mint (#4ecdc4)

## Template landing page
Chaque page métier/ville doit contenir :
1. Hero section avec H1 "[Métier] à [Ville] — Boostez vos avis Google"
2. Section problème (pourquoi les avis comptent)
3. Section solution (méthode Expert Local)
4. FAQ avec 5+ questions SEO (schema FAQ en JSON-LD)
5. CTA vers diagnostic gratuit
6. Schema JSON-LD : LocalBusiness + BreadcrumbList + FAQ

## Commandes disponibles
- "Génère une landing page [métier] [ville]" → crée le HTML complet
- "Audit SEO [URL]" → analyse et rapport markdown
- "Sitemap" → génère sitemap.xml avec toutes les URLs
- "FAQ [métier] [ville]" → génère 10 questions/réponses SEO
- "JSON-LD [page]" → génère le schema structuré
- "Post LinkedIn [sujet]" → rédige un post B2B
- "Calendrier éditorial [thème] [durée]" → planifie les publications
- "Rapport meta" → audite tous les title/description du repo
- "Pipeline SEO [mot-clé]" → recherche → rédaction → commit → PR

## Règles
- Toujours en français
- Jamais de faux témoignages ou statistiques inventées
- Respecter la charte graphique (midnight/coral/mint)
- Toujours créer une branche + PR, jamais push sur main
