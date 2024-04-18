<?php

namespace MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use FOS\MessageBundle\Entity\Message as BaseMessage;

/**
 * @ORM\Entity
 * @ORM\Table(name="messagerie_message")
 */
class Message extends BaseMessage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(
     *   targetEntity="MessageBundle\Entity\Thread",
     *   inversedBy="messages"
     * )
     * @var \FOS\MessageBundle\Model\ThreadInterface
     */
    protected $thread;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @var \FOS\MessageBundle\Model\ParticipantInterface
     */
    protected $sender;

    /**
     * @ORM\OneToMany(
     *   targetEntity="MessageBundle\Entity\MessageMetadata",
     *   mappedBy="message",
     *   cascade={"all"}
     * )
     * @var MessageMetadata[]|Collection
     */
    protected $metadata;

    /**
     * Add metadatum
     *
     * @param \MessageBundle\Entity\MessageMetadata $metadatum
     *
     * @return Message
     */
    public function addMetadatum(\MessageBundle\Entity\MessageMetadata $metadatum)
    {
        $this->metadata[] = $metadatum;

        return $this;
    }

    /**
     * Remove metadatum
     *
     * @param \MessageBundle\Entity\MessageMetadata $metadatum
     */
    public function removeMetadatum(\MessageBundle\Entity\MessageMetadata $metadatum)
    {
        $this->metadata->removeElement($metadatum);
    }

    /**
     * Get metadata
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMetadata()
    {
        return $this->metadata;
    }


    public function toArray(){
        return array(
            'Id'=>$this->getId(),
            'Body'=>$this->getBody(),
            'CreatedAt'=>$this->getCreatedAt(),
            'Thread'=>$this->getThread()->toArray(),
            'Sender'=>$this->getSender()->toArray()
            // 'Metadata'=>$this->getMetadata()->toArray()
        );
    }
}
