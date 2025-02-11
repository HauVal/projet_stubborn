<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $highlight = false;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductStock::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $stocks;

    
    public function __construct()
    {
        $this->stocks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
    
    public function setImage(?string $image): self
    {
        $this->image = $image;
    
        return $this;
    }

    public function getHighlight(): ?bool
    {
        return $this->highlight;
    }
    
    public function setHighlight(bool $highlight): self
    {
        $this->highlight = $highlight;
    
        return $this;
    }

    public function getStocks(): Collection
    {
        return $this->stocks;
    }
    
    public function addStock(ProductStock $stock): self
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->setProduct($this);
        }
    
        return $this;
    }
    
    public function removeStock(ProductStock $stock): self
    {
        if ($this->stocks->removeElement($stock)) {
            if ($stock->getProduct() === $this) {
                $stock->setProduct(null);
            }
        }
    
        return $this;
    }

}
