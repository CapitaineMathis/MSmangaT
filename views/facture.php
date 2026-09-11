<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            color: #333;
        }

        table.layout-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        table.layout-table td {
            vertical-align: top;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
        }
        .company-details {
            text-align: right;
            font-size: 12px;
            line-height: 1.5;
        }
        .client-section {
            margin-bottom: 40px;
        }
        .client-details {
            line-height: 1.5;
        }
        .invoice-meta {
            text-align: right;
            line-height: 1.5;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.items-table th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 10px 5px;
            text-align: left;
            font-size: 13px;
        }
        table.items-table td {
            padding: 10px 5px;
            font-size: 13px;
        }
        table.items-table th.text-right, 
        table.items-table td.text-right {
            text-align: right;
        }

        table.totals-table {
            width: 40%;
            float: right;
            border-collapse: collapse;
        }
        table.totals-table td {
            padding: 5px 0;
            font-size: 13px;
        }
        .total-row td {
            font-weight: bold;
            font-size: 15px;
        }
        .divider {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
        }
        .footer {
            clear: both;
            margin-top: 50px;
            font-size: 11px;
            line-height: 1.4;
        }
    </style>
</head>
<body>

    <table class="layout-table">
        <tr>
            <td class="company-name">
                MILLE SABORDS
            </td>
            <td class="company-details">
                20 Rue du Palais - 17000 LA ROCHELLE<br>
                <strong>n° SIREN / SIRET :</strong> 380 468 413 00022<br>
                <strong>E-mail:</strong> contact@1000-sabords.fr<br>
                <strong>Téléphone:</strong> +33 1 00 00 00 00<br>
                <strong>Site internet:</strong> www.1000-sabords.fr
            </td>
        </tr>
    </table>

    <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 30px;">

    <table class="layout-table client-section">
        <tr>
            <td class="client-details" width="60%">
                <strong>Destinataire:</strong><br>
                <?= htmlspecialchars($user['prenom'] ?? '') ?> <?= htmlspecialchars($user['nom'] ?? '') ?><br>
                <?= htmlspecialchars($user['numero_rue'] ?? '') ?> <?= htmlspecialchars($user['nom_rue'] ?? '') ?><br>
                <?= htmlspecialchars($user['code_postal'] ?? '') ?> <?= htmlspecialchars($user['ville'] ?? '') ?><br>
                France
            </td>
            <td class="invoice-meta" width="40%">
                <strong>Facture:</strong> <?= htmlspecialchars($actionIdCommand) ?><br>
                <strong>Date de facture:</strong> <?= $dateFacture ?><br>
                <strong>Statut:</strong> Payée
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Quantité</th>
                <th class="text-right">Unité</th>
                <th class="text-right">Prix HT</th>
                <th class="text-right">TVA</th>
                <th class="text-right">Montant TTC</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orderItems as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['description']) ?></td>
                <td class="text-right"><?= htmlspecialchars($item['quantite']) ?></td>
                <td class="text-right"><?= htmlspecialchars($item['unite']) ?></td>
                <td class="text-right"><?= number_format($item['prix_unitaire_ht'], 2, ',', ' ') ?> €</td>
                <td class="text-right">5.5%</td>
                <td class="text-right"><?= number_format($item['montant_ttc'], 2, ',', ' ') ?> €</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Sous-total HT</td>
            <td class="text-right"><?= number_format($sousTotalHT, 2, ',', ' ') ?> €</td>
        </tr>
        <tr>
            <td>TVA 5,5%</td>
            <td class="text-right"><?= number_format($tva, 2, ',', ' ') ?> €</td>
        </tr>
        <tr><td colspan="2"><div class="divider"></div></td></tr>
        <tr class="total-row">
            <td>Montant Total EUR</td>
            <td class="text-right"><?= number_format($totalTTC, 2, ',', ' ') ?> €</td>
        </tr>
    </table>

</body>
</html>