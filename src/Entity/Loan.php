<?php

namespace App\Entity;

use App\Repository\LoanRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoanRepository::class)]
class Loan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $borrower = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lentAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expectedReturnAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $returnedAt = null;

    #[ORM\ManyToOne(targetEntity: UserItem::class, inversedBy: 'loans')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserItem $userItem = null;

    public function __construct()
    {
        $this->lentAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBorrower(): ?string
    {
        return $this->borrower;
    }

    public function setBorrower(string $borrower): static
    {
        $this->borrower = $borrower;

        return $this;
    }

    public function getLentAt(): ?\DateTimeImmutable
    {
        return $this->lentAt;
    }

    public function setLentAt(\DateTimeImmutable $lentAt): static
    {
        $this->lentAt = $lentAt;

        return $this;
    }

    public function getExpectedReturnAt(): ?\DateTimeImmutable
    {
        return $this->expectedReturnAt;
    }

    public function setExpectedReturnAt(?\DateTimeImmutable $expectedReturnAt): static
    {
        $this->expectedReturnAt = $expectedReturnAt;

        return $this;
    }

    public function getReturnedAt(): ?\DateTimeImmutable
    {
        return $this->returnedAt;
    }

    public function setReturnedAt(?\DateTimeImmutable $returnedAt): static
    {
        $this->returnedAt = $returnedAt;

        return $this;
    }

    public function getUserItem(): ?UserItem
    {
        return $this->userItem;
    }

    public function setUserItem(?UserItem $userItem): static
    {
        $this->userItem = $userItem;

        return $this;
    }
}