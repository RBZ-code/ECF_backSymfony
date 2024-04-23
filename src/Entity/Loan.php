<?php

namespace App\Entity;

use App\Repository\LoanRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoanRepository::class)]
class Loan
{
#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column]
private ?int $id = null;

#[ORM\Column(type: Types::DATETIME_MUTABLE)]
private ?\DateTimeInterface $start_date = null;

#[ORM\Column(type: Types::DATETIME_MUTABLE)]
private ?\DateTimeInterface $end_date = null;

#[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
private ?\DateTimeInterface $return_date = null;

#[ORM\Column(type: Types::TEXT, nullable: true)]
private ?string $admin_comment = null;

#[ORM\ManyToOne(inversedBy: 'loans')]
#[ORM\JoinColumn(nullable: false)]
private ?Book $book = null;

#[ORM\ManyToOne(inversedBy: 'loanHistory')]
#[ORM\JoinColumn(nullable: false)]
private ?Utilisateur $borrower = null;


#[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
private ?\DateTimeInterface $extension_date = null;

#[ORM\Column(nullable: true)]
private ?bool $extension = null;


public function getId(): ?int
{
return $this->id;
}

public function getStartDate(): ?\DateTimeInterface
{
return $this->start_date;
}

public function setStartDate(\DateTimeInterface $start_date): static
{
$this->start_date = $start_date;

return $this;
}

public function getEndDate(): ?\DateTimeInterface
{
return $this->end_date;
}

public function setEndDate(\DateTimeInterface $end_date): static
{
$this->end_date = $end_date;

return $this;
}

public function getReturnDate(): ?\DateTimeInterface
{
return $this->return_date;
}

public function setReturnDate(?\DateTimeInterface $return_date): static
{
$this->return_date = $return_date;

return $this;
}

public function getAdminComment(): ?string
{
return $this->admin_comment;
}

public function setAdminComment(?string $admin_comment): static
{
$this->admin_comment = $admin_comment;

return $this;
}

public function getBook(): ?Book
{
return $this->book;
}

public function setBook(?Book $book): static
{
$this->book = $book;

return $this;
}

public function getBorrower(): ?Utilisateur
{
return $this->borrower;
}

public function setBorrower(?Utilisateur $borrower): static
{
$this->borrower = $borrower;

return $this;
}

public function getExtensionDate(): ?\DateTimeInterface
{
return $this->extension_date;
}

public function setExtensionDate(?\DateTimeInterface $extension_date): static
{
$this->extension_date = $extension_date;

return $this;
}

public function isExtension(): ?bool
{
    return $this->extension;
}

public function setExtension(?bool $extension): static
{
    $this->extension = $extension;

    return $this;
}
}

