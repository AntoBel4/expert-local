📘 README – EXPERT LOCAL

Architecture · Sécurité · SEO · Email · Stripe · Maintenance

🏷️ 1. Informations générales

Nom du site : Expert Local
URL : https://expert-local.fr

Objectif : vendre des solutions QR Code / Google Avis via Stripe
Technologies : HTML · CSS · PHP · Stripe API · cPanel (o2switch)

🧱 2. Architecture du site
/
├── index.html
├── contact.php
├── contact-success.html
├── creer-session-stripe.php
├── merci-commande.html
├── erreur-commande.html
├── style.css
├── og-expert-local.png
├── photo-profil.jpg
└── /vendor (Stripe SDK)


Pages clés :

index.html → page d’accueil, offres, formulaire

contact.php → traitement + email + confirmation

creer-session-stripe.php → création d’une session Stripe

contact-success.html → page premium après validation

merci-commande.html → message suite au paiement Stripe

erreur-commande.html → fallback sécurisé Stripe

🔐 3. Sécurité du site
✔ .htaccess premium installé

Inclut :

Protection Content-Security-Policy (CSP)

X-Frame-Options

X-Content-Type-Options

Referrer-Policy

Cache-Control optimisé

Protection contre MIME-Sniffing

Désactivation des serveurs externes

🎯 Effets pour tes clients

Leur navigation est protégée contre les scripts malicieux

Leur navigateur bloque les tentatives d’injection

Moins de risques d’attaques “man in the middle”

Impossible d’embarquer ton site dans des iframes frauduleuses

==> Sécurité premium digne d’un SaaS, même si ton site est simple.

✉️ 4. Email – Configuration complète
✔ SPF
v=spf1 +mx +a +ip4:109.234.162.178 ~all

✔ DKIM

Enregistré via cPanel → valide

✔ DMARC
v=DMARC1; p=none; pct=100; rua=mailto:contact@expert-local.fr

✔ Formulaire – double email

Email PRO reçu par toi
→ propre, lisible, UTF-8, sans icônes cassées
→ envoyé à : contact@expert-local.fr

Accusé de réception HTML premium envoyé au prospect
→ envoyé depuis : no-reply@expert-local.fr
→ design professionnel
→ signature avec photo
→ testé et fonctionnel

Score Mail-Tester : 9,8/10

(rare)

📦 5. Paiement – Stripe Checkout
✔ Mode test validé

Paiement test → OK

Redirection success → OK

Redirection cancel → OK

✔ Whitelist de sécurité intégrée

Seuls ces Price_ID sont autorisés :

price_1SUetcFMYCzrMVCgTnjb9Q8T

price_1SUeudFMYCzrMVCg3qzWk0LW

✔ Webhooks

Non installés volontairement
→ tu n’en as pas besoin pour un lancement
→ tu les ajouteras uniquement si tu veux automatiser ta livraison.

🌐 6. SEO – Configuration
✔ OG Meta Tags
og:title
og:description
og:image
og:url
og:type


⚠ WhatsApp met 24–48h à actualiser l’image → normal

✔ Favicon pack complet

PNG 32×32

PNG 16×16

Manifest JSON

Apple Touch Icon

OK

✔ Sitemap validé

Accessible :
👉 https://expert-local.fr/sitemap.xml

✔ Google Search Console

Domaine vérifié

DNS OK

Indexation en cours

Inspection d’URL → OK

🎨 7. Design – Points importants

Titres premium souslignés

Cards uniformisées

Offer-cards alignées

Testimonials en grille responsive

Boutons stylisés (hover / shadow)

Palette harmonisée sable / bleu nuit / corail