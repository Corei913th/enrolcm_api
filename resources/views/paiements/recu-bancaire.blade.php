{{-- Reçu de paiement bancaire - Format Ecobank Cameroun --}}
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reçu de Paiement - {{ $data['banque_nom'] ?? 'Banque' }}</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Roboto', Arial, sans-serif;
      font-size: 12px;
      line-height: 1.4;
      color: #333;
      background-color: #f8f9fa;
      padding: 20px;
    }

    .recu-container {
      max-width: 800px;
      margin: 0 auto;
      background: white;
      border: 1px solid #ddd;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* En-tête */
    .header {
      background: linear-gradient(135deg, #0066cc 0%, #004080 100%);
      color: white;
      padding: 20px;
      text-align: center;
      position: relative;
    }

    .header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><text fill="rgba(255,255,255,0.1)" font-size="16" font-weight="bold">ECOBANK</text></svg>') repeat;
      opacity: 0.3;
    }

    .header-content {
      position: relative;
      z-index: 1;
    }

    .bank-logo {
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 5px;
      letter-spacing: 2px;
    }

    .bank-details {
      font-size: 11px;
      opacity: 0.9;
    }

    .transaction-title {
      font-size: 16px;
      font-weight: bold;
      margin-top: 15px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    /* Corps du reçu */
    .body {
      padding: 30px;
    }

    .transaction-info {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
      margin-bottom: 30px;
    }

    .info-section {
      background: #f8f9fa;
      border: 1px solid #e9ecef;
      border-radius: 6px;
      padding: 20px;
    }

    .section-title {
      font-weight: bold;
      font-size: 13px;
      color: #495057;
      margin-bottom: 15px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-bottom: 2px solid #0066cc;
      padding-bottom: 5px;
    }

    .info-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
      padding: 8px 0;
      border-bottom: 1px solid #e9ecef;
    }

    .info-row:last-child {
      border-bottom: none;
      margin-bottom: 0;
    }

    .label {
      font-weight: 500;
      color: #6c757d;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .value {
      font-weight: 600;
      color: #212529;
      font-size: 12px;
    }

    .amount-highlight {
      font-size: 18px;
      color: #28a745;
      font-weight: bold;
    }

    /* Montant principal */
    .amount-section {
      text-align: center;
      margin: 30px 0;
      padding: 25px;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border: 2px solid #0066cc;
      border-radius: 10px;
    }

    .amount-label {
      font-size: 14px;
      color: #6c757d;
      margin-bottom: 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .amount-value {
      font-size: 32px;
      font-weight: bold;
      color: #28a745;
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }

    .currency {
      font-size: 16px;
      color: #6c757d;
      margin-left: 5px;
    }

    /* Statut */
    .status-section {
      text-align: center;
      margin: 25px 0;
      padding: 15px;
      background: #d4edda;
      border: 1px solid #c3e6cb;
      border-radius: 6px;
      color: #155724;
    }

    .status-success {
      font-weight: bold;
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    /* Instructions */
    .instructions {
      margin-top: 30px;
      padding: 20px;
      background: #fff3cd;
      border: 1px solid #ffeaa7;
      border-radius: 6px;
      border-left: 4px solid #ffc107;
    }

    .instructions-title {
      font-weight: bold;
      color: #856404;
      margin-bottom: 10px;
      font-size: 13px;
      text-transform: uppercase;
    }

    .instructions-content {
      color: #856404;
      font-size: 12px;
      line-height: 1.5;
    }

    /* Pied de page */
    .footer {
      background: #f8f9fa;
      border-top: 1px solid #dee2e6;
      padding: 20px 30px;
      font-size: 10px;
      color: #6c757d;
      text-align: center;
    }

    .footer-content {
      max-width: 600px;
      margin: 0 auto;
    }

    .footer-title {
      font-weight: bold;
      margin-bottom: 10px;
      color: #495057;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .footer-text {
      line-height: 1.4;
      margin-bottom: 15px;
    }

    .transaction-ref {
      background: #0066cc;
      color: white;
      padding: 10px 20px;
      border-radius: 6px;
      font-weight: bold;
      font-size: 13px;
      letter-spacing: 1px;
      display: inline-block;
      margin: 15px 0;
    }

    /* Responsive */
    @media (max-width: 600px) {
      .transaction-info {
        grid-template-columns: 1fr;
        gap: 20px;
      }

      .body {
        padding: 20px;
      }

      .amount-value {
        font-size: 28px;
      }
    }

    /* Styles d'impression */
    @media print {
      body {
        background: white;
        padding: 0;
      }

      .recu-container {
        box-shadow: none;
        border: none;
      }

      .no-print {
        display: none !important;
      }
    }
  </style>
</head>

<body>
  <div class="recu-container">
    <!-- En-tête -->
    <div class="header">
      <div class="header-content">
        <div class="bank-logo">{{ $data['banque_nom'] ?? 'ECOBANK CAMEROUN' }}</div>
        <div class="bank-details">
          {{ $data['agence_banque'] ?? 'Agence Centrale' }} • {{ $data['code_banque'] ?? 'ECOCMCMX' }}<br>
          Reçu de paiement électronique sécurisé
        </div>
        <div class="transaction-title">Reçu de Transaction</div>
      </div>
    </div>

    <!-- Corps -->
    <div class="body">
      <!-- Informations de transaction -->
      <div class="transaction-info">
        <!-- Informations bancaires -->
        <div class="info-section">
          <div class="section-title">Informations Bancaires</div>

          <div class="info-row">
            <span class="label">Banque</span>
            <span class="value">{{ $data['banque_nom'] ?? 'Ecobank Cameroun' }}</span>
          </div>

          <div class="info-row">
            <span class="label">Agence</span>
            <span class="value">{{ $data['agence_banque'] ?? 'Yaoundé Ngoa-Ekelle' }}</span>
          </div>

          <div class="info-row">
            <span class="label">Code Banque</span>
            <span class="value">{{ $data['code_banque'] ?? 'ECOCMCMX' }}</span>
          </div>

          <div class="info-row">
            <span class="label">Numéro Compte</span>
            <span class="value">{{ $data['numero_compte'] ?? 'ECO123456789' }}</span>
          </div>

          <div class="info-row">
            <span class="label">IBAN</span>
            <span class="value">{{ $data['iban'] ?? 'CM4512345678901234567890123' }}</span>
          </div>

          <div class="info-row">
            <span class="label">Devise</span>
            <span class="value">{{ $data['devise'] ?? 'XAF' }}</span>
          </div>
        </div>

        <!-- Informations du paiement -->
        <div class="info-section">
          <div class="section-title">Détails du Paiement</div>

          <div class="info-row">
            <span class="label">Type de paiement</span>
            <span class="value">{{ $data['type_paiement'] ?? 'Virement bancaire' }}</span>
          </div>

          <div class="info-row">
            <span class="label">Bénéficiaire</span>
            <span class="value">{{ $data['nom_beneficiaire'] ?? 'MINESUP' }}</span>
          </div>

          <div class="info-row">
            <span class="label">Date limite</span>
            <span class="value">{{ $data['date_limite'] ? \Carbon\Carbon::parse($data['date_limite'])->format('d/m/Y') : 'N/A' }}</span>
          </div>

          <div class="info-row">
            <span class="label">Frais de paiement</span>
            <span class="value">{{ number_format($data['frais_paiement'] ?? 0, 2, ',', ' ') }} {{ $data['devise'] ?? 'XAF' }}</span>
          </div>

          <div class="info-row">
            <span class="label">Date transaction</span>
            <span class="value">{{ now()->format('d/m/Y H:i:s') }}</span>
          </div>

          <div class="info-row">
            <span class="label">Statut</span>
            <span class="value">{{ $data['est_actif'] ? 'ACTIF' : 'INACTIF' }}</span>
          </div>
        </div>
      </div>

      <!-- Montant principal -->
      <div class="amount-section">
        <div class="amount-label">Montant à payer</div>
        <div class="amount-value">
          {{ number_format($data['montant'] ?? 0, 2, ',', ' ') }}
          <span class="currency">{{ $data['devise'] ?? 'XAF' }}</span>
        </div>
      </div>

      <!-- Référence de transaction -->
      <div style="text-align: center;">
        <div class="transaction-ref">
          Référence: {{ $data['id'] ?? 'REF-' . now()->format('YmdHis') }}
        </div>
      </div>

      <!-- Statut -->
      <div class="status-section">
        <div class="status-success">
          ✓ Transaction enregistrée avec succès
        </div>
      </div>

      <!-- Instructions -->
      @if($data['instructions'] ?? null)
      <div class="instructions">
        <div class="instructions-title">Instructions importantes</div>
        <div class="instructions-content">
          {{ $data['instructions'] }}
        </div>
      </div>
      @endif

      <!-- Commentaires -->
      @if($data['commentaires'] ?? null)
      <div class="instructions" style="background: #e7f3ff; border-color: #b3d7ff; border-left-color: #0066cc;">
        <div class="instructions-title" style="color: #0066cc;">Informations complémentaires</div>
        <div class="instructions-content" style="color: #0066cc;">
          {{ $data['commentaires'] }}
        </div>
      </div>
      @endif
    </div>

    <!-- Pied de page -->
    <div class="footer">
      <div class="footer-content">
        <div class="footer-title">Conditions générales</div>
        <div class="footer-text">
          Ce reçu est généré électroniquement et fait office de justificatif officiel de paiement.
          Conservez-le précieusement pour vos archives. Les paiements sont traités selon les délais bancaires standards.
          Pour toute contestation, contactez votre agence bancaire dans les 30 jours suivant la transaction.
        </div>
        <div class="footer-text">
          <strong>Ecobank Cameroun</strong> • Service Clients: 222 22 22 22 • www.ecobank.com<br>
          Généré le {{ now()->format('d/m/Y à H:i:s') }} • Référence système: {{ $data['id'] ?? 'N/A' }}
        </div>
      </div>
    </div>
  </div>

  <!-- Script pour l'impression automatique (optionnel) -->
  <script>
    // Impression automatique au chargement (commenter si non souhaité)
    // window.onload = function() {
    //     window.print();
    // };
  </script>
</body>

</html>