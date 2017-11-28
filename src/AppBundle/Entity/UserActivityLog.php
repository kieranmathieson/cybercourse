<?php
/**
 * Entity for logging user activity.
 *
 * See https://nehalist.io/logging-events-to-database-in-symfony/
 *
 * User: kieran
 * Date: 11/16/2017
 * Time: 10:41 AM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LogRepository")
 * @ORM\Table(name="user_activity_log")
 * @ORM\HasLifecycleCallbacks
 */
class UserActivityLog
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Many activity log entries have one user.
     * @ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="activityLogEntries")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $user;

    /**
     * What happened. See AppBundle\Helper\ActivityLogHelper.
     *
     * @ORM\Column(type="string", length=16)
     */
    protected $eventType;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $url;

    /**
     * IP address. Length allows for IPv6
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $ip;

    /**
     * Extra data in JSON.
     *
     * @ORM\Column(type="json", nullable=true)
     */
    protected $extra;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $whenEvent;

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->whenEvent = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }



    /**
     * Set extra, pass array.
     *
     * @param array $extra
     *
     * @return UserActivityLog
     */
    public function setExtra($extra)
    {
        $this->extra = json_encode($extra);
        return $this;
    }

    /**
     * Set extra, pass string, assume JSON encoded.
     *
     * @param string $extra
     *
     * @return UserActivityLog
     */
    public function setExtraJson($extra)
    {
        $this->extra = $extra;
        return $this;
    }

    /**
     * Get extra, return array.
     *
     * @return array
     */
    public function getExtra()
    {
        return json_decode($this->extra);
    }

    /**
     * Set extra, return string, assume JSON.
     *
     * @return string
     */
    public function getExtraJson()
    {
        return $this->extra;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $whenEvent
     *
     * @return UserActivityLog
     */
    public function setWhenEvent($whenEvent)
    {
        $this->whenEvent = $whenEvent;

        return $this;
    }

    /**
     * Get when.
     *
     * @return \DateTime
     */
    public function getWhenEvent()
    {
        return $this->whenEvent;
    }

    /**
     * @return mixed
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @param string $eventType
     * @return UserActivityLog
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return UserActivityLog
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return UserActivityLog
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

}
