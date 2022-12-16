<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birthdate = null;

    #[ORM\ManyToOne(inversedBy: 'contacts')]
    private ?Address $address = null;

    #[ORM\ManyToOne(inversedBy: 'contacts')]
    private ?ContactCategory $category = null;

    #[ORM\OneToMany(mappedBy: 'contact', targetEntity: Phone::class, orphanRemoval: true)]
    private Collection $phone;

    #[ORM\OneToMany(mappedBy: 'contact', targetEntity: Email::class, orphanRemoval: true)]
    private Collection $email;

    public function __construct()
    {
        $this->phone = new ArrayCollection();
        $this->email = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCategory(): ?ContactCategory
    {
        return $this->category;
    }

    public function setCategory(?ContactCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Phone>
     */
    public function getPhone(): Collection
    {
        return $this->phone;
    }

    public function addPhone(Phone $phone): self
    {
        if (!$this->phone->contains($phone)) {
            $this->phone->add($phone);
            $phone->setContact($this);
        }

        return $this;
    }

    public function removePhone(Phone $phone): self
    {
        if ($this->phone->removeElement($phone)) {
            // set the owning side to null (unless already changed)
            if ($phone->getContact() === $this) {
                $phone->setContact(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Email>
     */
    public function getEmail(): Collection
    {
        return $this->email;
    }

    public function addEmail(Email $email): self
    {
        if (!$this->email->contains($email)) {
            $this->email->add($email);
            $email->setContact($this);
        }

        return $this;
    }

    public function removeEmail(Email $email): self
    {
        if ($this->email->removeElement($email)) {
            // set the owning side to null (unless already changed)
            if ($email->getContact() === $this) {
                $email->setContact(null);
            }
        }

        return $this;
    }
}
