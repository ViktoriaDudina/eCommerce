<?php

namespace App\Entity;

use App\Repository\CartsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartsRepository::class)]
class Carts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $cteated_At = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'carts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $created_by = null;

    #[ORM\OneToMany(mappedBy: 'carts', targetEntity: CartItems::class, cascade: ['remove'])]
    private Collection $cartItems;

    public function __construct()
    {
        $this->cartItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCteatedAt(): ?\DateTimeImmutable
    {
        return $this->cteated_At;
    }

    public function setCteatedAt(?\DateTimeImmutable $cteated_At): static
    {
        $this->cteated_At = $cteated_At;

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

    public function getCreatedBy(): ?User
    {
        return $this->created_by;
    }

    public function setCreatedBy(?User $created_by): static
    {
        $this->created_by = $created_by;

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
            $cartItem->setCarts($this);
        }

        return $this;
    }

    public function removeCartItem(CartItems $cartItem): static
    {
        if ($this->cartItems->removeElement($cartItem)) {
            // set the owning side to null (unless already changed)
            if ($cartItem->getCarts() === $this) {
                $cartItem->setCarts(null);
            }
        }

        return $this;
    }

    /**
 * Calculates the order total.
 *
 * @return float
 */
public function getTotal(): float
{
    $total = 0;

    foreach ($this->getCartItems() as $item) {
        $total += $item->getTotal();
    }

    return $total;
}

public function clearItems(): void
{
    // If your cart items are stored in a Doctrine Collection
    $this->cartItems->clear();

    // If you need to manually remove each item (alternative method)
    // foreach ($this->cartItems as $item) {
    //     $this->removeCartItem($item); // Assuming removeCartItem is a method in your Carts entity
    // }
}

}
