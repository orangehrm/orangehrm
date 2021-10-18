<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("ohrm_email_subscriber")
 * @ORM\Entity
 */
class EmailSubscriber
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var EmailNotification
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\EmailNotification")
     * @ORM\JoinColumn(name="notification_id", referencedColumnName="id")
     */
    private EmailNotification $emailNotification;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private string $email;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return EmailNotification
     */
    public function getEmailNotification(): EmailNotification
    {
        return $this->emailNotification;
    }

    /**
     * @param EmailNotification $emailNotification
     */
    public function setEmailNotification(EmailNotification $emailNotification): void
    {
        $this->emailNotification = $emailNotification;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
