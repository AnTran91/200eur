<?php
namespace Tests\UnitTests\Form\FOSUser\Entity;

use FOS\UserBundle\Model\Group;

class TestGroup extends Group
{
    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
