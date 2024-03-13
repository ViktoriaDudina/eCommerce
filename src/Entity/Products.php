<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductsRepository::class)]
class Products
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $summary = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;


    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column(nullable: true)]
    private ?bool $on_sale = null;

    #[ORM\Column(nullable: true)]
    private ?bool $status = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'products', targetEntity: Reviews::class)]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'products', targetEntity: OrderLine::class)]
    private Collection $orderLines;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: CartItems::class)]
    private Collection $cartItems;

    #[ORM\Column(nullable: true)]
    private ?int $quantity = null;

    #[ORM\Column(nullable: true)]
    private ?int $views = 0;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Wishlists::class)]
    private Collection $wishlists;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->orderLines = new ArrayCollection();
        $this->cartItems = new ArrayCollection();
        $this->updateTimestamps();
        $this->wishlists = new ArrayCollection();

    }

    public function updateTimestamps(): void
     {
         $this->updated_at = new \DateTimeImmutable('now');
         if ($this->created_at === null) {
             $this->created_at = new \DateTimeImmutable('now');
         }
     }

     private function generateSlug(): string
     {
         // Generate a simple slug from the title
         return strtolower(trim(preg_replace('/[^a-zA-Z0-9-]+/', '-', $this->title), '-'));
     }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        $this->slug = $this->generateSlug();


        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): static
    {
        $this->summary = $summary;

        return $this;
    }

    public function getDescription(): ?string
    {
        return strip_tags($this->description);
    }

    public function setDescription(?string $description): static
    {
        $this->description = strip_tags($description);

        return $this;
    }



    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function isOnSale(): ?bool
    {
        return $this->on_sale;
    }

    public function setOnSale(?bool $on_sale): static
    {
        $this->on_sale = $on_sale;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Reviews>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Reviews $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setProducts($this);
        }
    
        return $this;
    }
    
    public function removeReview(Reviews $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getProducts() === $this) {
                $review->setProducts(null);
            }
        }
    
        return $this;
    }

    /**
     * @return Collection<int, OrderLine>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    public function addOrderLine(OrderLine $orderLine): static
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines->add($orderLine);
            $orderLine->setProducts($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLine $orderLine): static
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getProducts() === $this) {
                $orderLine->setProducts(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CartItems>
     */
    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    public function addCartItem(CartItems $cartItem): static
    {
        if (!$this->cartItems->contains($cartItem)) {
            $this->cartItems->add($cartItem);
            $cartItem->setProduct($this);
        }

        return $this;
    }

    public function removeCartItem(CartItems $cartItem): static
    {
        if ($this->cartItems->removeElement($cartItem)) {
            // set the owning side to null (unless already changed)
            if ($cartItem->getProduct() === $this) {
                $cartItem->setProduct(null);
            }
        }

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function incrementViews(): void
{
    $this->views++;
}

    public function setViews(?int $views): static
    {
        $this->views = $views;

        return $this;
 }

    /**
     * @return Collection<int, Wishlists>
     */
    public function getWishlists(): Collection
    {
        return $this->wishlists;
    }

    public function addWishlist(Wishlists $wishlist): static
    {
        if (!$this->wishlists->contains($wishlist)) {
            $this->wishlists->add($wishlist);
            $wishlist->setProduct($this);
        }

        return $this;
    }

    public function removeWishlist(Wishlists $wishlist): static
    {
        if ($this->wishlists->removeElement($wishlist)) {
            // set the owning side to null (unless already changed)
            if ($wishlist->getProduct() === $this) {
                $wishlist->setProduct(null);
            }
        }

        return $this;
    } 
}
