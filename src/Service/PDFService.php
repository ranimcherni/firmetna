<?php

namespace App\Service;

use App\Entity\Contract;
use App\Entity\Partner;
use Symfony\Component\HttpFoundation\Response;

class PDFService
{
    public function generateContractPDF(Contract $contract): Response
    {
        $html = $this->generateContractHTML($contract);
        
        // Pour l'instant, retournons le HTML comme PDF (fallback)
        // Dans un vrai projet, vous installeriez wkhtmltopdf ou utiliseriez DomPDF
        return new Response(
            $html,
            200,
            [
                'Content-Type' => 'text/html',
                'Content-Disposition' => 'inline; filename="contract_' . $contract->getId() . '.html"'
            ]
        );
    }

    public function generatePartnerPDF(Partner $partner): Response
    {
        $html = $this->generatePartnerHTML($partner);
        
        return new Response(
            $html,
            200,
            [
                'Content-Type' => 'text/html',
                'Content-Disposition' => 'inline; filename="partner_' . $partner->getId() . '.html"'
            ]
        );
    }

    public function generateContractsListPDF(array $contracts): Response
    {
        $html = $this->generateContractsListHTML($contracts);
        
        return new Response(
            $html,
            200,
            [
                'Content-Type' => 'text/html',
                'Content-Disposition' => 'inline; filename="contracts_list_' . date('Y-m-d') . '.html"'
            ]
        );
    }

    private function generateContractHTML(Contract $contract): string
    {
        $partner = $contract->getPartner();
        
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contract - ' . htmlspecialchars($contract->getTitle()) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #1a4d2e; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #1a4d2e; margin: 0; }
        .header p { color: #666; margin: 5px 0; }
        .section { margin-bottom: 30px; }
        .section h2 { color: #1a4d2e; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .info-item { padding: 10px; background: #f9f9f9; border-radius: 5px; }
        .info-item strong { color: #1a4d2e; }
        .footer { text-align: center; margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; }
        .status { padding: 5px 10px; border-radius: 3px; color: white; font-weight: bold; }
        .status.active { background: #28a745; }
        .status.completed { background: #17a2b8; }
        .status.cancelled { background: #dc3545; }
        .status.pending { background: #ffc107; color: #000; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CONTRAT</h1>
        <p>FIRMETNA - Gestion des Partenariats</p>
        <p>Généré le: ' . date('d/m/Y H:i') . '</p>
    </div>

    <div class="section">
        <h2>Informations du Contrat</h2>
        <div class="info-grid">
            <div class="info-item">
                <strong>Titre:</strong> ' . htmlspecialchars($contract->getTitle()) . '
            </div>
            <div class="info-item">
                <strong>Type:</strong> ' . htmlspecialchars($contract->getType()) . '
            </div>
            <div class="info-item">
                <strong>Montant:</strong> ' . ($contract->getAmount() ? htmlspecialchars($contract->getAmount()) . ' €' : 'Non spécifié') . '
            </div>
            <div class="info-item">
                <strong>Date début:</strong> ' . ($contract->getDateDebutContract() ? $contract->getDateDebutContract()->format('d/m/Y') : 'Non spécifiée') . '
            </div>
            <div class="info-item">
                <strong>Date fin:</strong> ' . ($contract->getDateFinContract() ? $contract->getDateFinContract()->format('d/m/Y') : 'Non spécifiée') . '
            </div>
            <div class="info-item">
                <strong>Date contrat:</strong> ' . ($contract->getOfferDate() ? $contract->getOfferDate()->format('d/m/Y') : 'Non spécifiée') . '
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <strong>Statut:</strong> 
            <span class="status ' . $this->getStatusClass($contract->getStatus()) . '">
                ' . htmlspecialchars($contract->getStatus()) . '
            </span>
        </div>
    </div>';

        if ($partner) {
            $html .= '
    <div class="section">
        <h2>Informations du Partenaire</h2>
        <div class="info-grid">
            <div class="info-item">
                <strong>Nom:</strong> ' . htmlspecialchars($partner->getName()) . '
            </div>
            <div class="info-item">
                <strong>Type:</strong> ' . htmlspecialchars($partner->getType()) . '
            </div>
            <div class="info-item">
                <strong>Email:</strong> ' . ($partner->getEmail() ? htmlspecialchars($partner->getEmail()) : 'Non spécifié') . '
            </div>
            <div class="info-item">
                <strong>Téléphone:</strong> ' . ($partner->getPhone() ? htmlspecialchars($partner->getPhone()) : 'Non spécifié') . '
            </div>
        </div>';
            
            if ($partner->getAddress()) {
                $html .= '
        <div style="margin-top: 20px;">
            <strong>Adresse:</strong><br>
            ' . htmlspecialchars($partner->getAddress()) . '
        </div>';
            }
            
            $html .= '
    </div>';
        }

        if ($contract->getDescription()) {
            $html .= '
    <div class="section">
        <h2>Description</h2>
        <p>' . nl2br(htmlspecialchars($contract->getDescription())) . '</p>
    </div>';
        }

        $html .= '
    <div class="footer">
        <p>Ce document a été généré automatiquement par le système FIRMETNA</p>
        <p>Pour toute question, contactez l\'administrateur du système</p>
        <div class="no-print" style="margin-top: 20px;">
            <button onclick="window.print()" style="background: #1a4d2e; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                🖨️ Imprimer
            </button>
        </div>
    </div>
</body>
</html>';

        return $html;
    }

    private function generatePartnerHTML(Partner $partner): string
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fiche Partenaire - ' . htmlspecialchars($partner->getName()) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #1a4d2e; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #1a4d2e; margin: 0; }
        .section { margin-bottom: 30px; }
        .section h2 { color: #1a4d2e; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .info-item { padding: 10px; background: #f9f9f9; border-radius: 5px; }
        .info-item strong { color: #1a4d2e; }
        .contracts-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .contracts-table th, .contracts-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .contracts-table th { background-color: #1a4d2e; color: white; }
        .footer { text-align: center; margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FICHE PARTENAIRE</h1>
        <p>FIRMETNA - Gestion des Partenariats</p>
        <p>Généré le: ' . date('d/m/Y H:i') . '</p>
    </div>

    <div class="section">
        <h2>Informations Générales</h2>
        <div class="info-grid">
            <div class="info-item">
                <strong>Nom:</strong> ' . htmlspecialchars($partner->getName()) . '
            </div>
            <div class="info-item">
                <strong>Type:</strong> ' . htmlspecialchars($partner->getType()) . '
            </div>
            <div class="info-item">
                <strong>Email:</strong> ' . ($partner->getEmail() ? htmlspecialchars($partner->getEmail()) : 'Non spécifié') . '
            </div>
            <div class="info-item">
                <strong>Téléphone:</strong> ' . ($partner->getPhone() ? htmlspecialchars($partner->getPhone()) : 'Non spécifié') . '
            </div>
        </div>';
        
        if ($partner->getAddress()) {
            $html .= '
        <div style="margin-top: 20px;">
            <strong>Adresse:</strong><br>
            ' . htmlspecialchars($partner->getAddress()) . '
        </div>';
        }
        
        if ($partner->getWebsite()) {
            $html .= '
        <div style="margin-top: 20px;">
            <strong>Site Web:</strong> ' . htmlspecialchars($partner->getWebsite()) . '
        </div>';
        }
        
        $html .= '
    </div>';

        if ($partner->getDescription()) {
            $html .= '
    <div class="section">
        <h2>Description</h2>
        <p>' . nl2br(htmlspecialchars($partner->getDescription())) . '</p>
    </div>';
        }

        $contracts = $partner->getOffers();
        if ($contracts->count() > 0) {
            $html .= '
    <div class="section">
        <h2>Contracts (' . $contracts->count() . ')</h2>
        <table class="contracts-table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>';
            
            foreach ($contracts as $contract) {
                $html .= '
                <tr>
                    <td>' . htmlspecialchars($contract->getTitle()) . '</td>
                    <td>' . htmlspecialchars($contract->getType()) . '</td>
                    <td>' . ($contract->getAmount() ? htmlspecialchars($contract->getAmount()) . ' €' : '-') . '</td>
                    <td>' . ($contract->getOfferDate() ? $contract->getOfferDate()->format('d/m/Y') : '-') . '</td>
                    <td><span class="status ' . $this->getStatusClass($contract->getStatus()) . '">' . htmlspecialchars($contract->getStatus()) . '</span></td>
                </tr>';
            }
            
            $html .= '
            </tbody>
        </table>
    </div>';
        }

        $html .= '
    <div class="footer">
        <p>Ce document a été généré automatiquement par le système FIRMETNA</p>
        <p>Pour toute question, contactez l\'administrateur du système</p>
        <div class="no-print" style="margin-top: 20px;">
            <button onclick="window.print()" style="background: #1a4d2e; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                🖨️ Imprimer
            </button>
        </div>
    </div>
</body>
</html>';

        return $html;
    }

    private function generateContractsListHTML(array $contracts): string
    {
        $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des Contracts - FIRMETNA</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #1a4d2e; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #1a4d2e; margin: 0; }
        .contracts-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .contracts-table th, .contracts-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .contracts-table th { background-color: #1a4d2e; color: white; }
        .contracts-table tr:nth-child(even) { background-color: #f9f9f9; }
        .status { padding: 3px 8px; border-radius: 3px; color: white; font-size: 12px; font-weight: bold; }
        .status.active { background: #28a745; }
        .status.completed { background: #17a2b8; }
        .status.cancelled { background: #dc3545; }
        .status.pending { background: #ffc107; color: #000; }
        .footer { text-align: center; margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LISTE DES CONTRACTS</h1>
        <p>FIRMETNA - Gestion des Partenariats</p>
        <p>Généré le: ' . date('d/m/Y H:i') . '</p>
        <p>Total: ' . count($contracts) . ' contracts</p>
    </div>

    <table class="contracts-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Partenaire</th>
                <th>Type</th>
                <th>Montant</th>
                <th>Date</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>';
        
        foreach ($contracts as $contract) {
            $partner = $contract->getPartner();
            $html .= '
            <tr>
                <td>#' . $contract->getId() . '</td>
                <td>' . htmlspecialchars($contract->getTitle()) . '</td>
                <td>' . ($partner ? htmlspecialchars($partner->getName()) : '-') . '</td>
                <td>' . htmlspecialchars($contract->getType()) . '</td>
                <td>' . ($contract->getAmount() ? htmlspecialchars($contract->getAmount()) . ' €' : '-') . '</td>
                <td>' . ($contract->getOfferDate() ? $contract->getOfferDate()->format('d/m/Y') : '-') . '</td>
                <td><span class="status ' . $this->getStatusClass($contract->getStatus()) . '">' . htmlspecialchars($contract->getStatus()) . '</span></td>
            </tr>';
        }
        
        $html .= '
        </tbody>
    </table>

    <div class="footer">
        <p>Ce document a été généré automatiquement par le système FIRMETNA</p>
        <p>Pour toute question, contactez l\'administrateur du système</p>
        <div class="no-print" style="margin-top: 20px;">
            <button onclick="window.print()" style="background: #1a4d2e; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                🖨️ Imprimer
            </button>
        </div>
    </div>
</body>
</html>';

        return $html;
    }

    private function getStatusClass(string $status): string
    {
        return match(strtolower($status)) {
            'actif' => 'active',
            'suspendu' => 'pending',
            'terminé' => 'completed',
            'termine' => 'completed',
            'annulé' => 'cancelled',
            'annule' => 'cancelled',
            default => 'pending'
        };
    }
}
