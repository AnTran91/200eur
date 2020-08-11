<?php
namespace Tests\UnitTests\Form\FOSUser\Entity;

use FOS\UserBundle\Model\User;

class TestUser extends User
{
    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
