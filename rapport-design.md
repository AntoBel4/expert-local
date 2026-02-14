# Rapport Design - Expert Local

## Analyse du design existant - expert-local.fr

Deux fichiers CSS utilisés :
- `style.css` (version principale)
- `style-2026.css` (nouvelle version 2026 avec thème sombre)

---

## 1. Palette de couleurs (style.css - version actuelle)

### Couleurs principales
```css
:root {
    --blue: #1A365D;         /* Bleu roi - fond sections, titres */
    --blue-dark: #0F172A;    /* Bleu nuit sombre - hero, footer */
    --gold: #F59E0B;         /* Or - boutons CTA, étoiles */
    --gold-light: #FBBF24;   /* Or clair - hover */
    --gray-light: #F3F4F6;   /* Gris clair - fonds sections */
    --text: #1F2937;         /* Texte principal */
    --white: #FFFFFF;
}
```

---

## 2. Palette de couleurs (style-2026.css - NOUVELLE version)

### Couleurs principales
```css
:root {
    /* Palette 2026 */
    --midnight: #0F172A;           /* Bleu nuit profond */
    --twilight: #1E293B;           /* Bleu nuit clair */
    --mint: #10B981;              /* Vert menthe vif */
    --mint-light: #34D399;        /* Vert menthe clair */
    --coral: #F97316;             /* Corail chaleureux */
    --coral-light: #FB923C;       /* Corail clair */
    --ivory: #F8FAFC;             /* Blanc cassé */
    --charcoal: #334155;          /* Gris texte */
    --slate: #64748B;             /* Gris secondaire */
    --light: #F1F5F9;             /* Gris très clair */
}
```

---

## 3. Typographie

### Fonts
```css
body {
    font-family: 'Inter', sans-serif;
}

h1, h2, h3, h4 {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
}
```

### Tailles
```css
h1 { font-size: 3rem; }
h2 { font-size: 2.5rem; }
h3 { font-size: 1.75rem; }
h4 { font-size: 1.25rem; }
```

---

## 4. Boutons

### CTA Principal (Or)
```css
.cta-main, .cta-secondary, .form-submit-btn {
    background: var(--gold);
    color: var(--blue-dark);
    padding: 16px 34px;
    font-weight: 800;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: background 0.2s ease;
}

.cta-main:hover, .cta-secondary:hover {
    background: var(--gold-light);
}
```

### Boutons version 2026 (Corail)
```css
.btn-submit {
    background: linear-gradient(90deg, var(--coral), var(--coral-light));
    color: white;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
}
.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(249, 115, 22, 0.3);
}
```

### Boutons valid (Mint - 2026)
```css
.btn-next, .offer_button-primary {
    background: var(--mint);
    color: var(--midnight);
}
.btn-next:hover {
    background: var(--mint-light);
}
```

---

## 5. Structure Hero

### Version actuelle (style.css)
```css
.hero {
    background: linear-gradient(135deg, var(--blue), var(--blue-dark));
    padding: 80px 20px;
    color: var(--white);
}
.hero-inner {
    max-width: 1200px;
    display: flex;
    flex-wrap: wrap;
    gap: 50px;
}
```

### Version 2026 (style-2026.css)
```css
.hero-2026 {
    background: linear-gradient(135deg, var(--midnight) 0%, var(--twilight) 100%);
    position: relative;
}
.hero-2026::before {
    background: 
        radial-gradient(circle at 20% 80%, rgba(16, 185, 129, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(249, 115, 22, 0.1) 0%, transparent 50%);
}
```

---

## 6. Sections

### Problem/Solution
```css
.problem-section, .solution-section {
    padding: 80px 20px;
}
.problem-section {
    background: var(--gray-light);
}
.problem-card, .solution-card {
    background: var(--white);
    padding: 26px;
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
}
```

---

## 7. Formulaires

```css
.form-container {
    background-color: var(--gray-light);
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.form-input, .form-textarea {
    width: 100%;
    padding: 14px 18px;
    border: 1px solid #E5E7EB;
    border-radius: 10px;
    font-size: 16px;
}

.form-input:focus {
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
    background: #FFFBF3;
}
```

---

## 8. Témoignages

```css
.testimonial-card {
    background: var(--white);
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 22px 26px;
    border-radius: 14px;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08);
}
.testimonial-avatar {
    border: 3px solid var(--gold);
}
.stars-row {
    color: #F59E0B;
}
```

---

## 9. FAQ

```css
.faq-section {
    background-color: #FFFFFF;
    max-width: 800px;
    margin: 0 auto;
}
.faq-item {
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 30px;
}
.faq-question {
    color: #1A365D;
    font-weight: 800;
    font-size: 1.2rem;
}
.faq-answer {
    color: #4B5563;
}
```

---

## 10. Footer

```css
footer {
    background: var(--blue-dark);
    color: var(--white);
    padding: 40px 20px;
    text-align: center;
}
```

---

## 11. Variables CSS à reprendre pour les landing pages

### Option A : Version actuelle (style.css)
```css
:root {
    --blue: #1A365D;
    --blue-dark: #0F172A;
    --gold: #F59E0B;
    --gold-light: #FBBF24;
    --gray-light: #F3F4F6;
    --text: #1F2937;
    --white: #FFFFFF;
}
```

### Option B : Version 2026 (style-2026.css) - RECOMMANDÉ
```css
:root {
    --midnight: #0F172A;
    --twilight: #1E293B;
    --mint: #10B981;
    --mint-light: #34D399;
    --coral: #F97316;
    --coral-light: #FB923C;
    --ivory: #F8FAFC;
    --charcoal: #334155;
    --slate: #64748B;
    --light: #F1F5F9;
    
    --font-primary: 'Inter', sans-serif;
    --font-secondary: 'Poppins', sans-serif;
    
    --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.08);
    --shadow-lg: 0 10px 40px rgba(0, 0, 0, 0.12);
    
    --transition-fast: 0.2s ease;
    --transition-normal: 0.3s ease;
}
```

---

## 12. Checklist pour mise à jour des 60 pages

Si on utilise la **version 2026** :

- [ ] **Reset CSS** : importer Inter + Poppins, box-sizing
- [ ] **Variables** : déclarer toutes les couleurs --midnight, --mint, --coral, etc.
- [ ] **Hero** : fond gradient midnight, titre avec title-gradient (mint→coral)
- [ ] **Boutons CTA** : background: linear-gradient(90deg, var(--coral), var(--coral-light))
- [ ] **Boutons valid** : background: var(--mint), color: var(--midnight)
- [ ] **Cards** : border-radius 12-20px, box-shadow --shadow-md
- [ ] **Formulaires** : inputs avec border 2px --light, focus --mint
- [ ] **Sections** : background ivory (#F8FAFC) ou white
- [ ] **Container** : max-width 1280px, padding 2rem
- [ ] **Responsive** : media queries pour mobile

---

*Rapport généré par Léo - 2026-02-14*
