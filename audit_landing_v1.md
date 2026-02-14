# Audit SEO — expert-local.fr

**Date:** 2026-02-14  
**Auditeur:** Léo (agent SEO Expert Local)  
**URL:** https://expert-local.fr

---

## Résumé Exécutif

Site landing page monopage (one-pager) pour le service "10 avis Google 5★ en 10 jours". Présence de éléments de conversion corrects mais carences SEO techniques importantes. Pas de JSON-LD détecté, maillage interne inexistant.

---

## 1. Structure & Hiérarchie

### ✅ Ce qui fonctionne
- Une seule page = crawl budget optimisé
- Contenu structuré en sections logiques
- Hiérarchie visuelle claire (gros titres → sous-titres → paragraphes)

### ⚠️ Gaps identifiés
| Élément | État | Issue |
|---------|------|-------|
| H1 | Non détecté (ou multiple) | **Risque de confusion sémantique** |
| H2 | Multiple (section titles) | OK |
| H3 | Absents ou peu utilisés | **Manque de profondeur** |
| Hiérarchie | Plate | **Perte de signaux SEO** |

**Impact:** ⭐⭐⭐⭐☆ (4/5)  
**Effort:** ★☆☆☆☆ (1/5) — Facile à corriger

---

## 2. Meta Title & Description

### ✅ Ce qui fonctionne
- Title visible: "Expert Local 2026 | La Méthode Qui Multiplie Vos Avis Google"

### ❌ Gaps identifiés
| Élément | État | Issue |
|---------|------|-------|
| Meta title | Probablementtitle> (non extraction) | **Non vérifiable via fetch** |
| Meta description | Non extraite | **Non vérifiable via fetch** |
| Canonical URL | Non vérifié | **Risque de duplicate content** |
| Open Graph | Non vérifié | **Partage réseaux sociaux poor** |

**Impact:** ⭐⭐⭐⭐☆ (4/5)  
**Effort:** ★☆☆☆☆ (1/5)

---

## 3. JSON-LD (Schema.org)

### ❌ Gap critique
- **Aucun JSON-LD détecté** sur la page principale
- Pas de `LocalBusiness`, pas de `FAQPage`, pas de `Review`
- Perte de rich snippets dans les SERPs

**Impact:** ⭐⭐⭐⭐⭐ (5/5)  
**Effort:** ★★☆☆☆ (2/5)

---

## 4. Maillage Interne

### ❌ Gap critique
- **Aucune lien interne** vers d'autres pages
- Site monopage = pas de navigation
- Pas de blog / ressources pour backlinks internes

**Impact:** ⭐⭐⭐⭐☆ (4/5)  
**Effort:** ★★★★☆ (4/5) — Nécessite création de pages

---

## 5. CTA (Calls to Action)

### ✅ Ce qui fonctionne
- CTA multiples: "Vérifier mon statut", "Analyser ma fiche gratuitement", "Obtenir mon plan personnalisé"
- Ancrages vers #diagnostic (scroll)
- Offres packagées (Express, PRO)

### ⚠️ Optimisations possibles
- CTA principal pas assez mis en avant (visuel)
- Pas de bouton flottant sticky
- Formulaire de diagnostic non visible above the fold

**Impact:** ⭐⭐☆☆☆ (2/5)  
**Effort:** ★★☆☆☆ (2/5)

---

## 6. Contenu & Keywords

### ✅ Ce qui fonctionne
- Mots-clés principaux: "avis Google", "10 avis en 10 jours", "Eure-et-Loir"
- Preuve sociale (témoignages, stats)
- Arguments de différenciation clairs

### ⚠️ Gaps identifiés
- Pas de section FAQ structurée (pour JSON-LD + SEO)
- Contenu orienté "urgence" mais peu de variations de mots-clés
- Pas de contenu long-tail

**Impact:** ⭐⭐⭐☆☆ (3/5)  
**Effort:** ★★★☆☆ (3/5)

---

## 7. Vitesse & Technique

### Non analysé (limitation fetch)
- Core Web Vitals
- Mobile-first
- Images optimisées
- Minification CSS/JS

---

## Top 10 des Corrections (Impact × Effort)

| # | Correction | Impact | Effort | Priorité |
|---|------------|--------|--------|----------|
| 1 | **Ajouter JSON-LD LocalBusiness** | ★★★★★ | ★★☆☆☆ | 🔴 HAUTE |
| 2 | **Ajouter JSON-LD FAQPage** (7 questions) | ★★★★★ | ★★☆☆☆ | 🔴 HAUTE |
| 3 | **Corriger hiérarchie H1/H2/H3** | ★★★★☆ | ★☆☆☆☆ | 🔴 HAUTE |
| 4 | **Créer 3-5 pages long-tail** (ville + commerce) | ★★★★☆ | ★★★★☆ | 🟡 MOYENNE |
| 5 | **Ajouter liens internes** entre pages | ★★★★☆ | ★★★☆☆ | 🟡 MOYENNE |
| 6 | **Vérifier meta title/description uniques** | ★★★☆☆ | ★☆☆☆☆ | 🟡 MOYENNE |
| 7 | **Ajouter Open Graph + Twitter Cards** | ★★★☆☆ | ★☆☆☆☆ | 🟢 BASSE |
| 8 | **Structurer FAQ en HTML visible** | ★★★☆☆ | ★★☆☆☆ | 🟡 MOYENNE |
| 9 | **Optimiser CTA principal** (bouton sticky) | ★★☆☆☆ | ★★☆☆☆ | 🟢 BASSE |
| 10 | **Ajouter schema Review/AggregateRating** | ★★★★☆ | ★★☆☆☆ | 🟡 MOYENNE |

---

## Plan d'Action Immédiat

### Cette semaine
1. Créer JSON-LD LocalBusiness pour page principale
2. Créer JSON-LD FAQPage avec 7 questions SEO
3. Structurer hiérarchie H1 (1 seul) + H2 + H3

### Cette mois
4. Créer premières pages long-tail (Chartres, Dreux + restaurants, coiffeurs)
5. Ajouter maillage interne entre pages
6. Ajouter Open Graph

---

## Fichiers à créer

- `json-ld/localbusiness.jsonld`
- `json-ld/faqpage.jsonld`
- `templates/landing-page-master.html`

---

*Audit réalisé par Léo — Prochaine étape: validation Antoine avant implémentation*
