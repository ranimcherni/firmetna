<?php

namespace App\Service;

use App\Entity\Publication;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ShareService
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function getShareUrl(Publication $publication): string
    {
        return $this->urlGenerator->generate(
            'app_forum_show',
            ['id' => $publication->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    public function getShareText(Publication $publication): string
    {
        $type = $publication->getTypeLabel();
        $titre = $publication->getTitre();
        $contenu = strip_tags($publication->getContenu());
        $contenu = mb_substr($contenu, 0, 100);
        
        return "$type : $titre - $contenu...";
    }

    public function getFacebookShareUrl(Publication $publication): string
    {
        $url = urlencode($this->getShareUrl($publication));
        $text = urlencode($this->getShareText($publication));
        return "https://www.facebook.com/sharer/sharer.php?u=$url&quote=$text";
    }

    public function getTwitterShareUrl(Publication $publication): string
    {
        $url = urlencode($this->getShareUrl($publication));
        $text = urlencode($publication->getTitre());
        return "https://twitter.com/intent/tweet?url=$url&text=$text";
    }

    public function getWhatsAppShareUrl(Publication $publication): string
    {
        $url = urlencode($this->getShareUrl($publication));
        $text = urlencode($this->getShareText($publication));
        return "https://wa.me/?text=$text%20$url";
    }

    public function getLinkedInShareUrl(Publication $publication): string
    {
        $url = urlencode($this->getShareUrl($publication));
        return "https://www.linkedin.com/sharing/share-offsite/?url=$url";
    }

    public function getEmailShareUrl(Publication $publication): string
    {
        $url = $this->getShareUrl($publication);
        $subject = urlencode($publication->getTitre());
        $body = urlencode($this->getShareText($publication) . "\n\n" . $url);
        return "mailto:?subject=$subject&body=$body";
    }

    public function getInstagramShareUrl(Publication $publication): string
    {
        // Instagram ne permet pas le partage direct via URL
        // On retourne le lien vers Instagram avec le texte pré-rempli
        // L'utilisateur devra copier le lien et le partager manuellement
        $url = $this->getShareUrl($publication);
        $text = urlencode($publication->getTitre() . " - " . $url);
        // Lien vers Instagram (ouvre l'app si installée, sinon le site web)
        return "https://www.instagram.com/?text=$text";
    }
}
