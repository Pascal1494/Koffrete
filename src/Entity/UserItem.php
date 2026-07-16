<?php

namespace App\Entity;

use App\Repository\UserItemRepository;
use App\Validator\AssertQuota;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserItemRepository::class)]
#[AssertQuota]
class UserItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Media $media = null;

    #[ORM\Column(name: '`condition`', length: 255)]
    private ?string $condition = null; // e.g. "Mint", "Very Good", "Good", "Poor"

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $personalNotes = null; // e.g. "German Edition", "Autographed", "Caisse 3 Étagère C"

    #[ORM\Column]
    private ?\DateTimeImmutable $acquiredAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagePath = null; // Path to upload image (for Vehicles, Retro Gaming, etc.)

    /**
     * @var Collection<int, Loan>
     */
    #[ORM\OneToMany(mappedBy: 'userItem', targetEntity: Loan::class, cascade: ['all'], orphanRemoval: true)]
    private Collection $loans;

    public function __construct()
    {
        $this->acquiredAt = new \DateTimeImmutable();
        $this->loans = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): static
    {
        $this->media = $media;

        return $this;
    }

    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function setCondition(string $condition): static
    {
        $this->condition = $condition;

        return $this;
    }

    public function getPersonalNotes(): ?string
    {
        return $this->personalNotes;
    }

    public function setPersonalNotes(?string $personalNotes): static
    {
        $this->personalNotes = $personalNotes;

        return $this;
    }

    public function getAcquiredAt(): ?\DateTimeImmutable
    {
        return $this->acquiredAt;
    }

    public function setAcquiredAt(\DateTimeImmutable $acquiredAt): static
    {
        $this->acquiredAt = $acquiredAt;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): static
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    /**
     * @return Collection<int, Loan>
     */
    public function getLoans(): Collection
    {
        return $this->loans;
    }

    public function addLoan(Loan $loan): static
    {
        if (!$this->loans->contains($loan)) {
            $this->loans->add($loan);
            $loan->setUserItem($this);
        }

        return $this;
    }

    public function removeLoan(Loan $loan): static
    {
        if ($this->loans->removeElement($loan)) {
            // set the owning side to null (unless already changed)
            if ($loan->getUserItem() === $this) {
                $loan->setUserItem(null);
            }
        }

        return $this;
    }
}