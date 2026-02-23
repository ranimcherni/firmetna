<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    public const STATUT_EN_ATTENTE = 'En attente';
    public const STATUT_CONFIRMEE = 'Confirmée';
    public const STATUT_EXPEDIEE = 'Expédiée';
    public const STATUT_LIVREE = 'Livrée';
    public const STATUT_ANNULEE = 'Annulée';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?User $client = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateCommande = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [
        self::STATUT_EN_ATTENTE,
        self::STATUT_CONFIRMEE,
        self::STATUT_EXPEDIEE,
        self::STATUT_LIVREE,
        self::STATUT_ANNULEE,
    ])]
    private ?string $statut = self::STATUT_EN_ATTENTE;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 500)]
    private ?string $adresseLivraison = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    #[Assert\Length(max: 500)]
    private ?string $commentaire = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, options: ['default' => 0])]
    #[Assert\GreaterThanOrEqual(0)]
    private ?string $total = '0';

    /** @var Collection<int, LigneCommande> */
    #[ORM\OneToMany(targetEntity: LigneCommande::class, mappedBy: 'commande', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Assert\Valid]
    private Collection $lignes;

    public function __construct()
    {
        $this->dateCommande = new \DateTimeImmutable();
        $this->lignes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;
        return $this;
    }

    public function getDateCommande(): ?\DateTimeImmutable
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeImmutable $dateCommande): static
    {
        $this->dateCommande = $dateCommande;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(?string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;
        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return Collection<int, LigneCommande>
     */
    public function getLignes(): Collection
    {
        return $this->lignes;
    }

    public function addLigne(LigneCommande $ligne): static
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes->add($ligne);
            $ligne->setCommande($this);
        }
        return $this;
    }

    public function removeLigne(LigneCommande $ligne): static
    {
        if ($this->lignes->removeElement($ligne)) {
            if ($ligne->getCommande() === $this) {
                $ligne->setCommande(null);
            }
        }
        return $this;
    }

    public function recalculerTotal(): void
    {
        $t = '0';
        foreach ($this->lignes as $ligne) {
            $t = (string) (bcadd($t, $ligne->getSousTotal(), 2));
        }
        $this->total = $t;
    }

    public function __toString(): string
    {
        return sprintf('Commande #%d - %s', $this->id ?? 0, $this->dateCommande?->format('d/m/Y') ?? '');
    }
}
