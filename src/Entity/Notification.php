<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    public const TYPE_COMMENT = 'comment';
    public const TYPE_REPLY = 'reply';
    public const TYPE_LIKE = 'like';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $destinataire = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $auteur = null;

    #[ORM\ManyToOne(targetEntity: Publication::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Publication $publication = null;

    #[ORM\ManyToOne(targetEntity: Commentaire::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Commentaire $commentaire = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(type: 'boolean')]
    private bool $lu = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateCreation = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDestinataire(): ?User
    {
        return $this->destinataire;
    }

    public function setDestinataire(?User $destinataire): static
    {
        $this->destinataire = $destinataire;
        return $this;
    }

    public function getAuteur(): ?User
    {
        return $this->auteur;
    }

    public function setAuteur(?User $auteur): static
    {
        $this->auteur = $auteur;
        return $this;
    }

    public function getPublication(): ?Publication
    {
        return $this->publication;
    }

    public function setPublication(?Publication $publication): static
    {
        $this->publication = $publication;
        return $this;
    }

    public function getCommentaire(): ?Commentaire
    {
        return $this->commentaire;
    }

    public function setCommentaire(?Commentaire $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function isLu(): bool
    {
        return $this->lu;
    }

    public function setLu(bool $lu): static
    {
        $this->lu = $lu;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeImmutable $dateCreation): static
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getMessage(): string
    {
        $auteurNom = $this->auteur->getNom() ?? $this->auteur->getPrenom() ?? 'Quelqu\'un';
        
        return match($this->type) {
            self::TYPE_COMMENT => "$auteurNom a commenté votre publication",
            self::TYPE_REPLY => "$auteurNom a répondu à votre commentaire",
            self::TYPE_LIKE => "$auteurNom a aimé votre publication",
            default => "Nouvelle notification",
        };
    }
}
