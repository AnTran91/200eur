<?php
namespace Tests\UnitTests\Form\FOSUser;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase as BaseTypeTestCase;

/**
 * Class TypeTestCase.
 */
abstract class TypeTestCase extends BaseTypeTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->factory = Forms::createFormFactoryBuilder()
            ->addTypes($this->getTypes())
            ->addExtensions($this->getExtensions())
            ->addTypeExtensions($this->getTypeExtensions())
            ->getFormFactory();
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }
    /**
     * @return array
     */
    protected function getTypeExtensions()
    {
        return array();
    }
    /**
     * @return array
     */
    protected function getTypes()
    {
        return array();
    }
}
