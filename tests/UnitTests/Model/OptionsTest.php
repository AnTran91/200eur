<?php
/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace tests\UnitTests\Model;

use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase
{
    public function testImageFile()
    {
        $option = $this->getOption();
        $this->assertNull($option->getImageFile());
        $uploadedFile = $this->getUploadedFile();
        $option->setImageFile($uploadedFile);
        $this->assertSame($uploadedFile, $option->getImageFile());
    }

    public function testImageName()
    {
        $option = $this->getOption();
        $this->assertNull($option->getImageName());
        $option->setImageName('test');
        $this->assertSame('test', $option->getImageName());
    }

    public function testUpdatedAt()
    {
        $option = $this->getOption();
        $this->assertNull($option->getUpdatedAt());
        $date = $this->getDate();
        $option->setUpdatedAt($date);
        $this->assertSame($date, $option->getUpdatedAt());
    }

    public function testGroupOption()
    {
        $option = $this->getOption();
        $this->assertNull($option->getGroupOptions());

        $groupOption = $this->getGroupOptionsClass();
        $option->setGroupOptions($groupOption);

        $this->assertSame($groupOption, $option->getGroupOptions());
    }

    /**
     * @return Option
     */
    protected function getOption()
    {
        return $this->getMockForAbstractClass('App\Entity\Options');
    }

    /**
     * @return UploadedFile
     */
    protected function getUploadedFile()
    {
        return $this->getMockBuilder('\Symfony\Component\HttpFoundation\File\UploadedFile')
              ->disableOriginalConstructor()
              ->getMock();
    }



    /**
     * @return \DateTime
     */
    protected function getDate()
    {
        return $this->getMockForAbstractClass('\DateTime');
    }

    /**
     * Options::class
     * @return string
     */
    protected function getGroupOptionsClass()
    {
        return $this->getMockForAbstractClass('App\Entity\GroupOptions');
    }
}
