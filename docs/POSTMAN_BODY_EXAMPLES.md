# Exemples de Body pour Postman - API Écoles

## 📝 CRÉER UNE ÉCOLE (POST)

### Exemple 1: École complète
```json
{
    "code_ecole": "ESSTIC",
    "libelle_ecole": "École Supérieure des Sciences et Techniques de l'Information et de la Communication",
    "region": "CENTRE",
    "localisation": "Yaoundé",
    "email_ecole": "contact@esstic.cm",
    "telephone_ecole": "+237222789012",
    "siteweb_ecole": "https://esstic.cm",
    "devise": "Communiquer pour Innover",
    "bp_ecole": "BP 1234",
    "est_actif": true
}
```

### Exemple 2: École minimale (champs requis uniquement)
```json
{
    "code_ecole": "TEST2024",
    "libelle_ecole": "École de Test 2024",
    "region": "CENTRE"
}
```

### Exemple 3: École à Douala
```json
{
    "code_ecole": "ISMA",
    "libelle_ecole": "Institut Supérieur de Management",
    "region": "LITTORAL",
    "localisation": "Douala",
    "email_ecole": "info@isma.cm",
    "telephone_ecole": "+237233456789",
    "siteweb_ecole": "https://isma.cm",
    "devise": "Excellence en Management",
    "bp_ecole": "BP 5678",
    "est_actif": true
}
```

### Exemple 4: École inactive
```json
{
    "code_ecole": "ANCIENNE",
    "libelle_ecole": "Ancienne École",
    "region": "OUEST",
    "localisation": "Bafoussam",
    "est_actif": false
}
```

---

## ✏️ MODIFIER UNE ÉCOLE (PUT)

### Exemple 1: Modifier plusieurs champs
```json
{
    "libelle_ecole": "École Supérieure des Sciences - Mise à jour",
    "email_ecole": "nouveau@esstic.cm",
    "telephone_ecole": "+237222999999",
    "devise": "Innovation et Excellence"
}
```

### Exemple 2: Modifier uniquement l'email
```json
{
    "email_ecole": "contact.nouveau@ecole.cm"
}
```

### Exemple 3: Modifier la localisation
```json
{
    "localisation": "Yaoundé - Ngoa Ekelle",
    "bp_ecole": "BP 9999"
}
```

### Exemple 4: Changer de région
```json
{
    "region": "LITTORAL",
    "localisation": "Douala"
}
```

---

## 🗑️ SUPPRIMER UN FICHIER (DELETE)

### Supprimer le logo
```json
{
    "type": "logo"
}
```

### Supprimer l'emblème
```json
{
    "type": "embleme"
}
```

### Supprimer le header frame
```json
{
    "type": "header_frame"
}
```

---

## 📄 GÉNÉRER UNE ATTESTATION PDF (POST)

### Exemple 1: Attestation complète
```json
{
    "etudiant_nom": "KAMGA Jean Pierre",
    "numero": "ATT-2024-001",
    "date_naissance": "1995-05-15",
    "lieu_naissance": "Yaoundé",
    "contenu": "a été régulièrement inscrit et a suivi avec assiduité les cours de l'année académique 2023-2024."
}
```

### Exemple 2: Attestation simple
```json
{
    "etudiant_nom": "NKOMO Marie",
    "numero": "ATT-2024-002"
}
```

### Exemple 3: Attestation de réussite
```json
{
    "etudiant_nom": "MBARGA Paul",
    "numero": "ATT-REUSSITE-2024-003",
    "date_naissance": "1998-03-20",
    "lieu_naissance": "Douala",
    "contenu": "a brillamment réussi l'examen de fin d'année académique 2023-2024 avec mention Très Bien."
}
```

---

## 📤 UPLOAD DE FICHIERS (form-data)

### Upload Logo
**⚠️ Utilise "form-data" dans Postman, PAS "raw"**

| KEY  | VALUE | TYPE |
|------|-------|------|
| file | [Sélectionne ton fichier logo.png] | File |
| type | logo | Text |

### Upload Emblème
| KEY  | VALUE | TYPE |
|------|-------|------|
| file | [Sélectionne ton fichier embleme.png] | File |
| type | embleme | Text |

### Upload Header Frame
| KEY  | VALUE | TYPE |
|------|-------|------|
| file | [Sélectionne ton fichier header.jpg] | File |
| type | header_frame | Text |

---

## 🎯 EXEMPLES PAR RÉGION

### Région CENTRE
```json
{
    "code_ecole": "CENTRE01",
    "libelle_ecole": "École du Centre",
    "region": "CENTRE",
    "localisation": "Yaoundé",
    "email_ecole": "contact@centre.cm",
    "est_actif": true
}
```

### Région LITTORAL
```json
{
    "code_ecole": "LITT01",
    "libelle_ecole": "École du Littoral",
    "region": "LITTORAL",
    "localisation": "Douala",
    "email_ecole": "contact@littoral.cm",
    "est_actif": true
}
```

### Région OUEST
```json
{
    "code_ecole": "OUEST01",
    "libelle_ecole": "École de l'Ouest",
    "region": "OUEST",
    "localisation": "Bafoussam",
    "email_ecole": "contact@ouest.cm",
    "est_actif": true
}
```

### Région NORD
```json
{
    "code_ecole": "NORD01",
    "libelle_ecole": "École du Nord",
    "region": "NORD",
    "localisation": "Garoua",
    "email_ecole": "contact@nord.cm",
    "est_actif": true
}
```

### Région SUD
```json
{
    "code_ecole": "SUD01",
    "libelle_ecole": "École du Sud",
    "region": "SUD",
    "localisation": "Ebolowa",
    "email_ecole": "contact@sud.cm",
    "est_actif": true
}
```

### Région EST
```json
{
    "code_ecole": "EST01",
    "libelle_ecole": "École de l'Est",
    "region": "EST",
    "localisation": "Bertoua",
    "email_ecole": "contact@est.cm",
    "est_actif": true
}
```

### Région ADAMAOUA
```json
{
    "code_ecole": "ADAM01",
    "libelle_ecole": "École de l'Adamaoua",
    "region": "ADAMAOUA",
    "localisation": "Ngaoundéré",
    "email_ecole": "contact@adamaoua.cm",
    "est_actif": true
}
```

### Région NORD-OUEST
```json
{
    "code_ecole": "NW01",
    "libelle_ecole": "École du Nord-Ouest",
    "region": "NORD_OUEST",
    "localisation": "Bamenda",
    "email_ecole": "contact@nw.cm",
    "est_actif": true
}
```

### Région SUD-OUEST
```json
{
    "code_ecole": "SW01",
    "libelle_ecole": "École du Sud-Ouest",
    "region": "SUD_OUEST",
    "localisation": "Buea",
    "email_ecole": "contact@sw.cm",
    "est_actif": true
}
```

### Région EXTRÊME-NORD
```json
{
    "code_ecole": "EXNORD01",
    "libelle_ecole": "École de l'Extrême-Nord",
    "region": "EXTREME_NORD",
    "localisation": "Maroua",
    "email_ecole": "contact@extremenord.cm",
    "est_actif": true
}
```

---

## 🔥 EXEMPLES D'ÉCOLES RÉELLES

### ENSP - Yaoundé
```json
{
    "code_ecole": "ENSP",
    "libelle_ecole": "École Nationale Supérieure Polytechnique",
    "region": "CENTRE",
    "localisation": "Yaoundé",
    "email_ecole": "contact@ensp.cm",
    "telephone_ecole": "+237222234567",
    "siteweb_ecole": "https://ensp.cm",
    "devise": "Excellence et Innovation",
    "bp_ecole": "BP 8390",
    "est_actif": true
}
```

### ENSAI - Ngaoundéré
```json
{
    "code_ecole": "ENSAI",
    "libelle_ecole": "École Nationale Supérieure des Sciences Agro-Industrielles",
    "region": "ADAMAOUA",
    "localisation": "Ngaoundéré",
    "email_ecole": "contact@ensai.cm",
    "telephone_ecole": "+237222345678",
    "siteweb_ecole": "https://ensai.cm",
    "devise": "Savoir et Développement",
    "bp_ecole": "BP 455",
    "est_actif": true
}
```

### ENSET - Douala
```json
{
    "code_ecole": "ENSET",
    "libelle_ecole": "École Normale Supérieure d'Enseignement Technique",
    "region": "LITTORAL",
    "localisation": "Douala",
    "email_ecole": "contact@enset.cm",
    "telephone_ecole": "+237233456789",
    "siteweb_ecole": "https://enset.cm",
    "devise": "Former pour Transformer",
    "bp_ecole": "BP 1872",
    "est_actif": true
}
```

### ENAM - Yaoundé
```json
{
    "code_ecole": "ENAM",
    "libelle_ecole": "École Nationale d'Administration et de Magistrature",
    "region": "CENTRE",
    "localisation": "Yaoundé",
    "email_ecole": "contact@enam.cm",
    "telephone_ecole": "+237222567890",
    "siteweb_ecole": "https://enam.cm",
    "devise": "Servir avec Excellence",
    "bp_ecole": "BP 7171",
    "est_actif": true
}
```

### IRIC - Yaoundé
```json
{
    "code_ecole": "IRIC",
    "libelle_ecole": "Institut des Relations Internationales du Cameroun",
    "region": "CENTRE",
    "localisation": "Yaoundé",
    "email_ecole": "contact@iric.cm",
    "telephone_ecole": "+237222678901",
    "siteweb_ecole": "https://iric.cm",
    "devise": "Diplomatie et Coopération",
    "bp_ecole": "BP 1637",
    "est_actif": true
}
```

---

## ⚠️ VALEURS VALIDES

### Régions (MAJUSCULES obligatoires):
- `CENTRE`
- `LITTORAL`
- `OUEST`
- `SUD`
- `EST`
- `NORD`
- `ADAMAOUA`
- `NORD_OUEST`
- `SUD_OUEST`
- `EXTREME_NORD`

### Types de fichiers:
- `logo`
- `embleme`
- `header_frame`

### Formats d'email valides:
- `contact@ecole.cm`
- `info@ecole.cm`
- `admin@ecole.cm`

### Formats de téléphone:
- `+237222234567` (fixe Yaoundé)
- `+237233456789` (fixe Douala)
- `+237690123456` (mobile)

---

## 💡 CONSEILS

1. **Code école**: Doit être unique, max 20 caractères
2. **Région**: Toujours en MAJUSCULES
3. **Email**: Format valide requis si fourni
4. **Site web**: Doit commencer par http:// ou https://
5. **est_actif**: true ou false (boolean)
6. **Champs optionnels**: Peuvent être omis ou null
