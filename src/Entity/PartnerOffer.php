<?php

namespace App\Entity;

use App\Repository\PartnerOfferRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PartnerOfferRepository::class)]
#[ORM\HasLifecycleCallbacks]
class PartnerOffer
{
    public const TYPE_DONATION = 'Donation';
    public const TYPE_SPONSORSHIP = 'Sponsorship';
    public const TYPE_PRODUCT = 'Product';
    public const TYPE_SERVICE = 'Service';
    public const TYPE_OTHER = 'Other';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Partner::class, inversedBy: 'offers')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Partner $partner = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le type d\'offre est obligatoire.')]
    #[Assert\Choice(
        choices: [self::TYPE_DONATION, self::TYPE_SPONSORSHIP, self::TYPE_PRODUCT, self::TYPE_SERVICE, self::TYPE_OTHER],
        message: 'Type d\'offre invalide.'
    )]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(max: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /** Amount in currency (e.g. euros) – null for non-monetary offers (products, services). */
    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $amount = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $offerDate = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Choice(
        choices: ['En cours', 'Réalisé', 'Annulé', 'En attente'],
        message: 'Statut invalide.'
    )]
    private ?string $status = 'En cours';

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner): static
    {
        $this->partner = $partner;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): static
    {
        $this->amount = $amount;
        return $this;
    }

    public function getOfferDate(): ?\DateTimeInterface
    {
        return $this->offerDate;
    }

    public function setOfferDate(?\DateTimeInterface $offerDate): static
    {
        $this->offerDate = $offerDate;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTime();
        }
    }
}
