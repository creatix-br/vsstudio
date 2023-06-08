<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * 
 * @UniqueEntity("email", message="user.email.already_been_used")
 * @UniqueEntity("phone", message="user.phone.already_been_used")
 * 
 * @author Écio Silva
 */
class User implements UserInterface
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=true)
     * 
     * @Assert\NotBlank(message="_default.not_null")
     * @Assert\Email(
     *      mode = "html5",
     *      normalizer = "trim"
     * )
     * 
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=100)
     * 
     * @Assert\NotBlank(message="_default.not_null")
     * @Assert\Length(
     *      min = 4,
     *      normalizer = "trim"
     * )
     * 
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * 
     * @Assert\NotBlank(message="_default.not_null")
     * @Assert\Regex(
     *      pattern="/^\d{10,11}$/",
     *      normalizer = "trim",
     *      message="_default.invalid"
     * )
     * 
     * @var string
     */
    private $phone;

    /**
     * @ORM\Column(type="date", nullable=true)
     * 
     * @var \DateTime
     */
    private $birthdate;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string")
     * 
     * @var string The hashed password
     */
    private $password;

    /**
     * @Assert\NotBlank(message="_default.not_null", groups={"registration", "updatePassword", "resetPassword"})
     * @Assert\Length(
     *      min = 8,
     *      normalizer = "trim",
     *      groups={"registration", "updatePassword", "resetPassword"}
     * )
     * 
     * @var string The plain password, not encoded yet
     */
    private $plainPassword;

    /**
     * @SecurityAssert\UserPassword(
     *     message = "user.password.old_not_match",
     *      groups={"updatePassword"}
     * )
     */
    private $oldPassword;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     * 
     * @Assert\Length(
     *      allowEmptyString = true,
     *      min = 11,
     *      max = 11,
     *      normalizer = "trim"
     * )
     * 
     * @var string
     */
    private $cpf;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * 
     * @var bool
     */
    private $emailChecked;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * 
     * @var bool
     */
    private $phoneChecked;

    /**
     * 
     */
    public function __construct()
    {
        $this->emailChecked = false;
        $this->phoneChecked = false;
    }

    /**
     * @return string User first name
     */
    public function __toString(): string
    {
        $names = explode(' ', $this->getName());
        return $names[0];
    }

    public function getUserIdentifier()
    {
        return (string) $this->phone;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     * @deprecated
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = preg_replace("#\D+#i", "", $phone);

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

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf(string $cpf): self
    {
        $this->cpf = $cpf;

        return $this;
    }

    public function getEmailChecked(): ?bool
    {
        return $this->emailChecked;
    }

    public function setEmailChecked(bool $emailChecked): self
    {
        $this->emailChecked = $emailChecked;

        return $this;
    }

    public function getPhoneChecked(): ?bool
    {
        return $this->phoneChecked;
    }

    public function setPhoneChecked(bool $phoneChecked): self
    {
        $this->phoneChecked = $phoneChecked;

        return $this;
    }

    public function __toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'phone' => $this->getPhone(),
            'roles' => $this->getRoles(),
            'birthdate' => $this->getBirthdate(),
            'cpf' => $this->getCpf(),
            'emailChecked' => $this->getEmailChecked(),
            'phoneChecked' => $this->getPhoneChecked(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt()
        ];
    }
}
