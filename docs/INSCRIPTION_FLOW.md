# Flux d'inscription avec OCR du reçu de paiement

## Vue d'ensemble

Le candidat doit d'abord payer les frais d'inscription et obtenir un reçu. Ce reçu est ensuite utilisé pour créer son compte.

## Flux complet

### Étape 1 : Upload du reçu (AVANT inscription)

**Endpoint :** `POST /api/auth/pre-register/upload-receipt`

**Accès :** Anonyme (pas d'authentification requise)

**Request :**
```http
POST /api/auth/pre-register/upload-receipt
Content-Type: multipart/form-data

receipt_image: [fichier image JPG/PNG]
```

**Response Success (200) :**
```json
{
  "success": true,
  "data": {
    "numero_recu": "REC-2024-001234",
    "montant": 5000,
    "banque": "BICEC",
    "date_paiement": "2024-12-25",
    "image_path": "receipts/abc123.jpg",
    "ocr_data": { ... },
    "message": "Reçu validé. Utilisez le numéro de reçu comme identifiant pour créer votre compte."
  }
}
```

**Response Error (400) :**
```json
{
  "success": false,
  "message": "Image de mauvaise qualité. Veuillez prendre une photo plus nette."
}
```

ou

```json
{
  "success": false,
  "message": "Ce numéro de reçu a déjà été utilisé pour une inscription."
}
```

---

### Étape 1 bis : Saisie manuelle (si OCR échoue)

**Endpoint :** `POST /api/auth/pre-register/manual-receipt`

**Request :**
```http
POST /api/auth/pre-register/manual-receipt
Content-Type: multipart/form-data

numero_recu: REC-2024-001234
receipt_image: [fichier image]
montant: 5000
banque: BICEC
date_paiement: 2024-12-25
```

---

### Étape 2 : Création du compte

**Endpoint :** `POST /api/auth/register`

**Request :**
```json
{
  "user_name": "REC-2024-001234",
  "mot_de_passe": "MonMotDePasse123!",
  "mot_de_passe_confirmation": "MonMotDePasse123!",
  "nationalite_cand": "Camerounaise",
  "receipt_data": {
    "image_path": "receipts/abc123.jpg",
    "montant": 5000,
    "banque": "BICEC",
    "date_paiement": "2024-12-25",
    "ocr_data": { ... }
  }
}
```

**Response Success (201) :**
```json
{
  "success": true,
  "message": "Inscription réussie",
  "data": {
    "user": {
      "id": "uuid",
      "user_name": "REC-2024-001234",
      "type_utilisateur": "candidat",
      "est_actif": true
    },
    "token": "1|abc123..."
  }
}
```

---

### Étape 3 : Connexion

**Endpoint :** `POST /api/auth/login`

**Request :**
```json
{
  "user_name": "REC-2024-001234",
  "mot_de_passe": "MonMotDePasse123!"
}
```

**Response Success (200) :**
```json
{
  "success": true,
  "message": "Connexion réussie",
  "data": {
    "user": { ... },
    "token": "2|xyz789..."
  }
}
```

---

## Vérifications

### Vérifier si un numéro de reçu est disponible

**Endpoint :** `POST /api/auth/pre-register/check-receipt`

**Request :**
```json
{
  "numero_recu": "REC-2024-001234"
}
```

**Response :**
```json
{
  "success": true,
  "data": {
    "available": false,
    "message": "Ce numéro de reçu a déjà été utilisé"
  }
}
```

---

## Diagramme de flux

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Candidat paie les frais d'inscription                   │
│    → Obtient un reçu bancaire                              │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. Upload du reçu (photo)                                  │
│    POST /api/auth/pre-register/upload-receipt              │
│    → OCR extrait le numéro automatiquement                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
                    ┌───────┴───────┐
                    │ OCR réussit ? │
                    └───────┬───────┘
                    Oui ↓   ↓ Non
                        │   │
                        │   └──→ Saisie manuelle
                        │        POST /api/auth/pre-register/manual-receipt
                        ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. Création du compte                                       │
│    POST /api/auth/register                                  │
│    - user_name = numéro de reçu                            │
│    - mot_de_passe = choisi par le candidat                 │
│    - receipt_data = données de l'étape 1                   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. Connexion                                                │
│    POST /api/auth/login                                     │
│    - user_name = numéro de reçu                            │
│    - mot_de_passe = mot de passe défini                    │
└─────────────────────────────────────────────────────────────┘
```

---

## Règles métier

1. **Un reçu = Un compte** : Chaque numéro de reçu ne peut être utilisé qu'une seule fois
2. **Numéro de reçu = Username** : Le numéro extrait du reçu devient l'identifiant de connexion
3. **Validation OCR** : Si la confiance OCR < 40%, l'utilisateur doit saisir manuellement
4. **Montant attendu** : 5000 FCFA (configurable dans `config/concours.php`)
5. **Formats acceptés** : JPG, PNG (max 5MB)
6. **Vérification admin** : Le reçu reste "en_attente" jusqu'à validation par un admin

---

## Exemple d'intégration Frontend

```javascript
// Étape 1 : Upload du reçu
const uploadReceipt = async (imageFile) => {
  const formData = new FormData();
  formData.append('receipt_image', imageFile);
  
  const response = await fetch('/api/auth/pre-register/upload-receipt', {
    method: 'POST',
    body: formData
  });
  
  const data = await response.json();
  
  if (data.success) {
    // Stocker les données pour l'étape 2
    localStorage.setItem('receipt_data', JSON.stringify(data.data));
    // Rediriger vers le formulaire d'inscription
    router.push('/register');
  }
};

// Étape 2 : Inscription
const register = async (password, passwordConfirmation) => {
  const receiptData = JSON.parse(localStorage.getItem('receipt_data'));
  
  const response = await fetch('/api/auth/register', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      user_name: receiptData.numero_recu,
      mot_de_passe: password,
      mot_de_passe_confirmation: passwordConfirmation,
      nationalite_cand: 'Camerounaise',
      receipt_data: {
        image_path: receiptData.image_path,
        montant: receiptData.montant,
        banque: receiptData.banque,
        date_paiement: receiptData.date_paiement,
        ocr_data: receiptData.ocr_data
      }
    })
  });
  
  const data = await response.json();
  
  if (data.success) {
    // Stocker le token
    localStorage.setItem('token', data.data.token);
    // Rediriger vers le dashboard
    router.push('/dashboard');
  }
};
```

---

## Codes d'erreur

| Code | Message | Solution |
|------|---------|----------|
| 400 | Image de mauvaise qualité | Reprendre une photo plus nette |
| 400 | Numéro de reçu déjà utilisé | Contacter l'administration |
| 400 | Impossible de détecter le numéro | Utiliser la saisie manuelle |
| 422 | Validation échouée | Vérifier les champs requis |
| 500 | Erreur serveur | Réessayer plus tard |
