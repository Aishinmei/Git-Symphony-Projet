<?php

// src/Entity/Notification.php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $email_recipient;

    #[ORM\Column(type: 'string', length: 255)]
    private $sujet;

    #[ORM\Column(type: 'text')]
    private $message;

    // Getter and setter for id (if needed)
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter and setter for email_recipient
    public function getEmailRecipient(): ?string
    {
        return $this->email_recipient;
    }

    public function setEmailRecipient(string $email_recipient): self
    {
        $this->email_recipient = $email_recipient;

        return $this;
    }

    // Getter and setter for sujet
    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }

    // Getter and setter for message
    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}