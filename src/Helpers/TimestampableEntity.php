<?php declare(strict_types=1);
/**
 * Created 2021-09-26
 * Author Dmitry Kushneriov
 */

namespace App\Helpers;

use DateTime;

/**
 * Trait TimestampableHasLifecycleCallbacksEntity
 * @package App\Helpers
 *
 * @property DateTime $createdAt
 * @property DateTime $updatedAt
 */
trait TimestampableEntity
{
    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->created_at = $createdAt;
        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updated_at = $updatedAt;
        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }

    /**
     * @ORM\PrePersist
     */
    public function beforeCreate(): void
    {
        if ($this->created_at === null) {
            $this->setCreatedAt(new DateTime());
        }
        if ($this->updated_at === null) {
            $this->setUpdatedAt(new DateTime());
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function beforeUpdate(): void
    {
        $this->setUpdatedAt(new DateTime());
    }
}