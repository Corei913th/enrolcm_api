{{-- Formulaire de test pour le reçu bancaire --}}
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Test Reçu Bancaire - Cameroun</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding: 20px;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      background: white;
      border-radius: 10px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      overflow: hidden;
    }

    .header {
      background: #0066cc;
      color: white;
      padding: 20px;
      text-align: center;
    }

    .header h1 {
      margin-bottom: 10px;
      font-size: 24px;
    }

    .header p {
      opacity: 0.9;
      font-size: 14px;
    }

    .form-container {
      padding: 30px;
    }

    .form-section {
      margin-bottom: 30px;
      padding: 20px;
      border: 1px solid #e9ecef;
      border-radius: 8px;
      background: #f8f9fa;
    }

    .section-title {
      font-size: 18px;
      font-weight: bold;
      color: #495057;
      margin-bottom: 15px;
      border-bottom: 2px solid #0066cc;
      padding-bottom: 10px;
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      font-weight: 600;
      color: #495057;
      margin-bottom: 5px;
      font-size: 14px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ced4da;
      border-radius: 4px;
      font-size: 14px;
      transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      border-color: #0066cc;
      outline: 0;
      box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
    }

    .form-group textarea {
      resize: vertical;
      min-height: 80px;
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .checkbox-group input[type="checkbox"] {
      width: auto;
      margin: 0;
    }

    .actions {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #dee2e6;
    }

    .btn {
      display: inline-block;
      padding: 12px 30px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-primary {
      background: #0066cc;
      color: white;
    }

    .btn-primary:hover {
      background: #0052a3;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
    }

    .btn-secondary {
      background: #6c757d;
      color: white;
      margin-left: 10px;
    }

    .btn-secondary:hover {
      background: #545b62;
    }

    .sample-data {
      background: #e7f3ff;
      border: 1px solid #b3d7ff;
      border-radius: 6px;
      padding: 15px;
      margin-bottom: 20px;
    }

    .sample-data pre {
      background: white;
      padding: 10px;
      border-radius: 4px;
      font-size: 12px;
      overflow-x: auto;
      border: 1px solid #dee2e6;
    }

    .sample-data .btn-small {
      padding: 6px 12px;
      font-size: 12px;
      margin-top: 10px;
    }

    .help-text {
      display: block;
      color: #6c757d;
      font-size: 12px;
      margin-top: 5px;
      font-style: italic;
    }

    .logo-preview {
      margin-top: 10px;
      padding: 10px;
      background: #f8f9fa;
      border-radius: 4px;
      border: 1px solid #dee2e6;
    }

    .logo-preview img {
      display: block;
      margin: 0 auto;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .form-grid {
        grid-template-columns: 1fr;
      }

      .container {
        margin: 10px;
      }

      .form-container {
        padding: 20px;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <h1>🧪 Test Reçu Bancaire Cameroun</h1>
      <p>Testez le rendu du reçu bancaire avec vos données</p>
    </div>

    <div class="form-container">
      <form action="{{ route('test.receipt.generate') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Données d'exemple -->
        <div class="sample-data">
          <strong>💡 Données d'exemple chargées automatiquement :</strong>
          <button type="button" class="btn btn-small btn-primary" onclick="loadSampleData()">
            Charger les données d'exemple
          </button>
          <pre id="sampleData">{{ json_encode([
                        "banque_nom" => "Ecobank Cameroun",
                        "numero_compte" => "ECO123456789",
                        "nom_beneficiaire" => "MINESUP",
                        "devise" => "XAF",
                        "code_banque" => "ECOCMCMX",
                        "agence_banque" => "Yaoundé Ngoa-Ekelle",
                        "iban" => "CM4512345678901234567890123",
                        "type_paiement" => "virement",
                        "frais_paiement" => "1800.00",
                        "montant" => "25000.00",
                        "date_limite" => "2026-02-26",
                        "instructions" => "Effectuer le virement à Ecobank et conserver le reçu original.",
                        "commentaires" => "Les virements interbancaires sont acceptés.",
                        "est_actif" => true
                    ], JSON_PRETTY_PRINT) }}</pre>
        </div>

        <!-- Informations bancaires -->
        <div class="form-section">
          <div class="section-title">🏦 Informations Bancaires</div>
          <div class="form-grid">
            <div class="form-group">
              <label for="banque_nom">Nom de la banque</label>
              <input type="text" id="banque_nom" name="banque_nom" value="Ecobank Cameroun" required>
            </div>

            <div class="form-group">
              <label for="agence_banque">Agence bancaire</label>
              <input type="text" id="agence_banque" name="agence_banque" value="Yaoundé Ngoa-Ekelle" required>
            </div>

            <div class="form-group">
              <label for="code_banque">Code banque</label>
              <input type="text" id="code_banque" name="code_banque" value="ECOCMCMX" required>
            </div>

            <div class="form-group">
              <label for="numero_compte">Numéro de compte</label>
              <input type="text" id="numero_compte" name="numero_compte" value="ECO123456789" required>
            </div>

            <div class="form-group">
              <label for="iban">IBAN</label>
              <input type="text" id="iban" name="iban" value="CM4512345678901234567890123">
            </div>

            <div class="form-group">
              <label for="logo">Logo de la banque (PNG, JPG, JPEG)</label>
              <input type="file" id="logo" name="logo" accept="image/png,image/jpg,image/jpeg" onchange="previewLogo(this)">
              <small class="help-text">Taille recommandée : 200x100px maximum. Le logo sera affiché dans l'espace prévu à gauche du reçu.</small>
              <div id="logo-preview" class="logo-preview" style="display: none;">
                <img id="logo-preview-img" src="" alt="Aperçu du logo" style="max-width: 200px; max-height: 100px; border: 1px solid #ddd; padding: 5px;">
              </div>
            </div>

            <div class="form-group">
              <label for="devise">Devise</label>
              <select id="devise" name="devise" required>
                <option value="XAF" selected>XAF (Franc CFA)</option>
                <option value="EUR">EUR</option>
                <option value="USD">USD</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Informations de paiement -->
        <div class="form-section">
          <div class="section-title">💰 Détails du Paiement</div>
          <div class="form-grid">
            <div class="form-group">
              <label for="nom_beneficiaire">Bénéficiaire</label>
              <input type="text" id="nom_beneficiaire" name="nom_beneficiaire" value="MINESUP" required>
            </div>

            <div class="form-group">
              <label for="type_paiement">Type de paiement</label>
              <select id="type_paiement" name="type_paiement" required>
                <option value="virement" selected>Virement bancaire</option>
                <option value="especes">Espèces</option>
                <option value="cheque">Chèque</option>
                <option value="carte">Carte bancaire</option>
              </select>
            </div>

            <div class="form-group">
              <label for="montant">Montant</label>
              <input type="number" id="montant" name="montant" step="0.01" value="25000.00" required>
            </div>

            <div class="form-group">
              <label for="frais_paiement">Frais de paiement</label>
              <input type="number" id="frais_paiement" name="frais_paiement" step="0.01" value="1800.00">
            </div>

            <div class="form-group">
              <label for="date_limite">Date limite</label>
              <input type="date" id="date_limite" name="date_limite" value="2026-02-26">
            </div>

            <div class="form-group">
              <label for="est_actif">Paiement actif</label>
              <div class="checkbox-group">
                <input type="checkbox" id="est_actif" name="est_actif" value="1" checked>
                <label for="est_actif">Oui</label>
              </div>
            </div>
          </div>
        </div>

        <!-- Instructions et commentaires -->
        <div class="form-section">
          <div class="section-title">📝 Instructions et Commentaires</div>
          <div class="form-grid">
            <div class="form-group">
              <label for="instructions">Instructions de paiement</label>
              <textarea id="instructions" name="instructions" placeholder="Instructions pour le payeur...">Effectuer le virement à Ecobank et conserver le reçu original.</textarea>
            </div>

            <div class="form-group">
              <label for="commentaires">Commentaires complémentaires</label>
              <textarea id="commentaires" name="commentaires" placeholder="Informations complémentaires...">Les virements interbancaires sont acceptés.</textarea>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="actions">
          <button type="submit" class="btn btn-primary">
            🎨 Générer le reçu
          </button>
          <a href="{{ route('test.receipt.form') }}" class="btn btn-secondary">
            🔄 Réinitialiser
          </a>
        </div>
      </form>
    </div>
  </div>

  <script>
    function previewLogo(input) {
      const preview = document.getElementById('logo-preview');
      const previewImg = document.getElementById('logo-preview-img');

      if (input.files && input.files[0]) {
        const file = input.files[0];

        // Vérifier la taille du fichier (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
          alert('Le fichier est trop volumineux. Taille maximum : 2MB');
          input.value = '';
          preview.style.display = 'none';
          return;
        }

        // Vérifier le type de fichier
        const allowedTypes = ['image/png', 'image/jpg', 'image/jpeg'];
        if (!allowedTypes.includes(file.type)) {
          alert('Type de fichier non autorisé. Utilisez PNG, JPG ou JPEG.');
          input.value = '';
          preview.style.display = 'none';
          return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
          previewImg.src = e.target.result;
          preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
      } else {
        preview.style.display = 'none';
      }
    }

    function loadSampleData() {
      const sampleData = {
        "banque_nom": "Ecobank Cameroun",
        "numero_compte": "ECO123456789",
        "nom_beneficiaire": "MINESUP",
        "devise": "XAF",
        "code_banque": "ECOCMCMX",
        "agence_banque": "Yaoundé Ngoa-Ekelle",
        "iban": "CM4512345678901234567890123",
        "type_paiement": "virement",
        "frais_paiement": "1800.00",
        "montant": "25000.00",
        "date_limite": "2026-02-26",
        "instructions": "Effectuer le virement à Ecobank et conserver le reçu original.",
        "commentaires": "Les virements interbancaires sont acceptés.",
        "est_actif": true
      };

      // Remplir le formulaire avec les données d'exemple
      Object.keys(sampleData).forEach(key => {
        const element = document.getElementById(key);
        if (element) {
          if (element.type === 'checkbox') {
            element.checked = sampleData[key];
          } else {
            element.value = sampleData[key];
          }
        }
      });
    }

    // Charger automatiquement les données d'exemple au chargement
    document.addEventListener('DOMContentLoaded', loadSampleData);
  </script>
</body>

</html>