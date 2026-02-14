// EXPERT LOCAL 2026 - SCRIPT PRINCIPAL
console.log('✅ Expert Local 2026 - JavaScript chargé');

// =======================
// FORMULAIRE DIAGNOSTIC 3 ÉTAPES
// =======================
document.addEventListener('DOMContentLoaded', function() {
  console.log('📋 Initialisation formulaire diagnostic');
  
  const quickDiagnostic = document.getElementById('quickDiagnostic');
  if (!quickDiagnostic) {
    console.error('❌ Formulaire introuvable');
    return;
  }
  
  // SUIVANT
  const nextButtons = quickDiagnostic.querySelectorAll('.btn-next');
  nextButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      console.log('▶️ Bouton Suivant cliqué');
      
      const currentStep = this.closest('.form-step');
      const nextStepNumber = this.getAttribute('data-next');
      const nextStep = quickDiagnostic.querySelector(`[data-step="${nextStepNumber}"]`);
      
      // Validation
      const radioInputs = currentStep.querySelectorAll('input[type="radio"]');
      if (radioInputs.length > 0) {
        let isChecked = false;
        radioInputs.forEach(radio => {
          if (radio.checked) isChecked = true;
        });
        if (!isChecked) {
          alert('⚠️ Veuillez sélectionner une option');
          return;
        }
      }
      
      // Changement d'étape
      currentStep.classList.remove('active');
      currentStep.style.display = 'none';
      nextStep.classList.add('active');
      nextStep.style.display = 'block';
      
      // Mise à jour indicateur
      updateStepIndicator(nextStepNumber);
    });
  });
  
  // RETOUR
  const prevButtons = quickDiagnostic.querySelectorAll('.btn-prev');
  prevButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      console.log('◀️ Bouton Retour cliqué');
      
      const currentStep = this.closest('.form-step');
      const prevStepNumber = this.getAttribute('data-prev');
      const prevStep = quickDiagnostic.querySelector(`[data-step="${prevStepNumber}"]`);
      
      currentStep.classList.remove('active');
      currentStep.style.display = 'none';
      prevStep.classList.add('active');
      prevStep.style.display = 'block';
      
      updateStepIndicator(prevStepNumber);
    });
  });
  
  // Fonction indicateur de pas
  function updateStepIndicator(stepNumber) {
    console.log(`📊 Étape ${stepNumber}`);
    const indicators = quickDiagnostic.querySelectorAll('.step-indicator span');
    indicators.forEach((indicator, index) => {
      indicator.classList.remove('step-active', 'step-complete');
      
      if (index < (stepNumber - 1) * 2) {
        indicator.classList.add('step-complete');
      } else if (index === (stepNumber - 1) * 2) {
        indicator.classList.add('step-active');
      }
    });
  }
  
  // Initialisation
  updateStepIndicator(1);
});

// =======================
// FORMULAIRE RAPIDE - SOUMISSION CLASSIQUE (pas d'AJAX)
// =======================
// Le formulaire se soumet normalement en POST vers quick-diagnostic.php
// Pas besoin de code JavaScript ici
// =======================
// TABS AVANT/APRÈS
// =======================
document.addEventListener('DOMContentLoaded', function() {
  const comparisonTabs = document.querySelectorAll('.comparison-tabs button');
  console.log(`🎯 ${comparisonTabs.length} tabs trouvés`);
  
  comparisonTabs.forEach(tab => {
    tab.addEventListener('click', function(e) {
      e.preventDefault();
      const tabId = this.getAttribute('data-tab');
      console.log(`🔘 Tab ${tabId} cliqué`);
      
      // Activation tab
      comparisonTabs.forEach(t => t.classList.remove('tab-active'));
      this.classList.add('tab-active');
      
      // Affichage contenu
      document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
      });
      document.getElementById(tabId).classList.add('active');
    });
  });
});

// =======================
// SMOOTH SCROLL
// =======================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function(e) {
    e.preventDefault();
    const targetId = this.getAttribute('href');
    if (targetId === '#') return;
    
    const targetElement = document.querySelector(targetId);
    if (targetElement) {
      window.scrollTo({
        top: targetElement.offsetTop - 80,
        behavior: 'smooth'
      });
    }
  });
});

// =======================
// ANIMATIONS AU SCROLL
// =======================
document.addEventListener('DOMContentLoaded', function() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-in');
      }
    });
  }, { threshold: 0.1 });
  
  document.querySelectorAll('.method-step, .threat-card, .offer-card').forEach(el => {
    observer.observe(el);
  });
});

// =======================
// AIDE GOOGLE MAPS - FIX
// =======================
document.addEventListener('DOMContentLoaded', function() {
  // Version spécifique pour le lien avec ID
  const googleHelpLink = document.getElementById('google-help-link');
  
  if (googleHelpLink) {
    googleHelpLink.addEventListener('click', function(e) {
      e.preventDefault();
      
      const helpMessage = `🔍 Comment trouver votre lien Google :

1. Ouvrez Google Maps sur votre téléphone ou ordinateur
2. Recherchez le nom exact de votre commerce
3. Cliquez sur votre fiche d'entreprise
4. Appuyez sur le bouton "Partager"
5. Sélectionnez "Copier le lien"

Ou directement via l'URL Google :
• Allez sur : https://www.google.com/maps
• Cherchez votre commerce
• Copiez l'URL depuis la barre d'adresse

Collez ce lien dans le champ ci-dessus !`;
      
      alert(helpMessage);
    });
  }
  
  // Version pour tous les liens d'aide
  const helpLinks = document.querySelectorAll('.help-link');
  helpLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const helpMessage = `🔍 Comment trouver votre lien Google :

1. Ouvrez Google Maps
2. Recherchez votre commerce
3. Cliquez sur votre fiche
4. Appuyez sur "Partager"
5. Copiez le lien

Collez-le dans le formulaire !`;
      
      alert(helpMessage);
    });
  });
});

// =======================
// DEBUG
// =======================
window.addEventListener('error', function(e) {
  console.error('❌ Erreur:', e.message, 'dans', e.filename, 'ligne', e.lineno);
});

// =======================
// INITIALISATION GÉNÉRALE
// =======================
console.log('🚀 Script Expert Local 2026 initialisé avec succès');