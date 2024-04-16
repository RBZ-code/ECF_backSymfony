<?php

namespace App\Entity;

use App\Repository\ConditionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConditionRepository::class)]
#[ORM\Table(name: '`condition`')]
class Condition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Book>
     */
    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'book_condition')]
    private Collection $book_condition;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function __construct()
    {
        $this->book_condition = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBookCondition(): Collection
    {
        return $this->book_condition;
    }

    public function addBookCondition(Book $bookCondition): static
    {
        if (!$this->book_condition->contains($bookCondition)) {
            $this->book_condition->add($bookCondition);
            $bookCondition->setBookCondition($this);
        }

        return $this;
    }

    public function removeBookCondition(Book $bookCondition): static
    {
        if ($this->book_condition->removeElement($bookCondition)) {
            // set the owning side to null (unless already changed)
            if ($bookCondition->getBookCondition() === $this) {
                $bookCondition->setBookCondition(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
