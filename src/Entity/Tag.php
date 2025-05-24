<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\RestoreTag;
use App\Repository\TagRepository;
use App\State\TagsDeletedListProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new GetCollection(
            name: 'tags_deleted',
            uriTemplate: '/tags/deleted',
            provider: TagsDeletedListProvider::class
        ),
        new Post(
            validationContext: ['groups' => ['createOrUpdate']]
        ),
        new Get(),
        new Delete(),
        new Patch(
            validationContext: ['groups' => ['createOrUpdate']]
        ),
        new Patch(
            name: 'restore',
            uriTemplate: '/tags/{id}/restore',
            controller: RestoreTag::class,
            provider: TagsDeletedListProvider::class,
            denormalizationContext: ['groups' => []]
        ),
    ],
    normalizationContext: ['groups' => ['tag:read']],
    denormalizationContext: ['groups' => ['tag:write']],
)]
#[ApiFilter(
    OrderFilter::class,
    properties: ['id', 'name'],
    arguments: ['orderParameterName' => 'order'],
)]
#[Gedmo\SoftDeleteable(
    fieldName: 'deletedAt',
    timeAware: false,
    hardDelete: true
)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['createOrUpdate'])]
    #[Groups(['tag:read', 'tag:write'])]
    private string $name;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'childrens')]
    #[Groups(['tag:read', 'tag:write'])]
    #[MaxDepth(1)]
    private ?self $parent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    #[Groups(['tag:read'])]
    #[MaxDepth(1)]
    private Collection $childrens;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'tags')]
    private Collection $products;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['tag:read'])]
    private ?\DateTime $deletedAt;

    public function __construct()
    {
        $this->childrens = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    #[Assert\IsTrue(message: 'tag.one_level', groups: ['createOrUpdate'])]
    public function isParentHasNotParent(): bool
    {
        if (null === $this->parent) {
            return true;
        }

        return null === $this->parent->getParent();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildrens(): Collection
    {
        return $this->childrens;
    }

    public function addChild(self $child): static
    {
        if (!$this->childrens->contains($child)) {
            $this->childrens->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): static
    {
        if ($this->childrens->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->addTag($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            $product->removeTag($this);
        }

        return $this;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
