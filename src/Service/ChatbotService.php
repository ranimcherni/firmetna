<?php

namespace App\Service;

use App\Repository\ProduitRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ChatbotService
{
    public function __construct(
        private ProduitRepository $produitRepository,
        private HttpClientInterface $httpClient,
        private string $apiKey = ''
    ) {
    }

    /**
     * Process user message and return chatbot response
     */
    public function processMessage(string $userMessage, string $userRole = 'client'): string
    {
        $lowerMessage = strtolower($userMessage);

        // Detect intent
        if ($this->matches($lowerMessage, ['produit', 'produits', 'quoi', 'vendre', 'acheter', 'list', 'catalogue'])) {
            return $this->handleProductsIntent($userRole);
        }

        if ($this->matches($lowerMessage, ['projet', 'firmetna', 'mission', 'quoi', 'comment', 'qui'])) {
            return $this->handleProjectIntent();
        }

        if ($this->matches($lowerMessage, ['prix', 'coût', 'combien', 'tarif'])) {
            return $this->handlePricingIntent();
        }

        if ($this->matches($lowerMessage, ['livraison', 'délai', 'quand', 'commander'])) {
            return $this->handleDeliveryIntent();
        }

        if ($this->matches($lowerMessage, ['agricult', 'producteur', 'vendre', 'vente', 'partenaire'])) {
            return $this->handleFarmerIntent();
        }

        if ($this->matches($lowerMessage, ['don', 'donation', 'soutenir', 'aide', 'contribuer'])) {
            return $this->handleDonationIntent();
        }

        if ($this->matches($lowerMessage, ['bio', 'écolo', 'environn', 'durable', 'qualité'])) {
            return $this->handleQualityIntent();
        }

        if ($this->matches($lowerMessage, ['contact', 'aide', 'support', 'help', 'question'])) {
            return $this->handleContactIntent();
        }

        // Default response
        return $this->getDefaultResponse();
    }

    private function matches(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (strpos($text, strtolower($keyword)) !== false) {
                return true;
            }
        }
        return false;
    }

    private function handleProductsIntent(string $userRole): string
    {
        $products = $this->produitRepository->findAll();
        
        if (empty($products)) {
            return "🛒 Actuellement, notre catalogue est en cours de mise à jour. Revenez bientôt pour découvrir nos produits locaux!";
        }

        $count = count($products);
        $types = [];
        foreach ($products as $product) {
            $type = $product->getType() === 'vegetale' ? '🥬 Végétal' : '🐄 Produit animal';
            if (!in_array($type, $types)) {
                $types[] = $type;
            }
        }

        $typesStr = implode(', ', $types);
        
        if ($userRole === 'agriculteur' || $userRole === 'producteur') {
            return "🌾 Excellent! Vous pouvez proposer vos produits sur notre plateforme Firmetna.\n\n"
                . "Nos catégories actuelles: $typesStr\n\n"
                . "Pour devenir vendeur, veuillez contacter notre équipe ou cliquer sur 'Devenir Partenaire'.";
        }

        return "🛒 Nous avons $count produits disponibles dans nos catégories: $typesStr\n\n"
            . "Vous pouvez parcourir notre catalogue dans la section 'Produits' ou me demander des recommandations spécifiques!";
    }

    private function handleProjectIntent(): string
    {
        return "🌱 **Bienvenue sur Firmetna!**\n\n"
            . "Firmetna est une plateforme collaborative dédiée à l'agriculture durable et aux produits locaux.\n\n"
            . "✨ Notre mission:\n"
            . "• Connecter producteurs locaux et consommateurs\n"
            . "• Promouvoir l'agriculture biologique et durable\n"
            . "• Soutenir les communautés rurales\n"
            . "• Créer une économie circulaire\n\n"
            . "Vous êtes ici pour acheter, vendre ou soutenir notre projet?";
    }

    private function handlePricingIntent(): string
    {
        return "💰 **Informations tarifaires:**\n\n"
        . "Nos produits sont proposés aux meilleurs prix directs producteur, sans intermédiaire!\n\n"
        . "📦 Exemples (par kilo):\n"
        . "• Légumes frais: 2€ - 6€/kg\n"
        . "• Produits fermiers: 5€ - 12€/unité\n"
        . "• Abonnement panier: à partir de 29,99€/semaine\n\n"
        . "Les tarifs varient selon la saison et la disponibilité. Consultez notre catalogue complet!";
    }

    private function handleDeliveryIntent(): string
    {
        return "🚚 **Livraison et commandes:**\n\n"
        . "• Livraison hebdomadaire disponible\n"
        . "• Délai standard: 2-3 jours\n"
        . "• Retrait en magasin: immédiat\n"
        . "• Frais de livraison: Gratuit dès 50€ d'achat\n\n"
        . "Pour commander, allez dans 'Panier' ou 'Commandes'.";
    }

    private function handleFarmerIntent(): string
    {
        return "🌾 **Devenez partenaire Firmetna!**\n\n"
        . "Vous êtes agriculteur, producteur ou éleveur?\n\n"
        . "📝 Avantages pour les producteurs:\n"
        . "• Accès à une large clientèle\n"
        . "• Vente directe sans intermédiaire\n"
        . "• Visibilité accrue\n"
        . "• Soutien communautaire\n\n"
        . "Pour rejoindre nos partenaires, consultez la page 'Partenaires' ou contactez-nous directement!";
    }

    private function handleDonationIntent(): string
    {
        return "❤️ **Soutenez Firmetna!**\n\n"
        . "Votre soutien nous aide à développer une agriculture durable.\n\n"
        . "💚 Types de contributions:\n"
        . "• Don financier (25€, 50€, 100€...)\n"
        . "• Partenariat\n"
        . "• Bénévolat\n\n"
        . "Chaque euro compte! Consultez notre page 'Donations' pour en savoir plus.";
    }

    private function handleQualityIntent(): string
    {
        return "✅ **Notre engagement qualité:**\n\n"
        . "Tous nos produits sont:\n"
        . "🌿 Cultivés localement\n"
        . "♻️ Méthodes durables et respectueuses\n"
        . "🏠 Provenant de producteurs de confiance\n"
        . "🥗 Frais et naturels, sans additifs\n\n"
        . "Nous certifions la provenance et la qualité de chaque produit!";
    }

    private function handleContactIntent(): string
    {
        return "📞 **Nous sommes là pour vous!**\n\n"
        . "Besoin d'aide?\n"
        . "📧 Email: contact@firmetna.fr\n"
        . "📱 Téléphone: +33 (0)1 234 567 89\n"
        . "🕐 Horaires: Lun-Ven 9h-18h\n\n"
        . "Ou utilisez notre formulaire de contact dans la section 'À Propos'";
    }

    private function getDefaultResponse(): string
    {
        return "👋 Je suis Firmetna Bot!\n\n"
            . "Je peux vous aider avec:\n"
            . "🛒 Nos **produits** et catalogue\n"
            . "🌱 Notre **projet** et mission\n"
            . "💰 **Prix** et tarifs\n"
            . "🚚 **Livraison** et commandes\n"
            . "🌾 Devenir **producteur**\n"
            . "❤️ **Donations** et soutien\n"
            . "✅ **Qualité** et engagements\n\n"
            . "Posez votre question ou choisissez un sujet!";
    }
}
