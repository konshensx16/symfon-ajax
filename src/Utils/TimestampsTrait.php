<?php
namespace App\Utils;

trait TimestampsTrait
{
    /**
     * @var \DateTime $created_at
     */
    private $created_at;

    /**
     * @var \DateTime $updated_at
     */
    private $updated_at;

    public function __construct()
    {
        if (method_exists(get_parent_class($this), '__construct'))
        {
            parent::__construct();
        }
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

}