<?php
/**
 * This file is part of the Monolog Cascade package.
 *
 * (c) Raphael Antonmattei <rantonmattei@theorchard.com>
 * (c) The Orchard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cascade\Tests\Config\Loader\FileLoader;

use Cascade\Tests\Fixtures;

/**
 * Class JsonTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class JsonTest extends \PHPUnit\Framework\TestCase
{
    /**
     * JSON loader mock builder
     * @var \PHPUnit_Framework_MockObject_MockBuilder
     */
    protected ?\PHPUnit\Framework\MockObject\MockObject $jsonLoader = null;

    protected function setUp(): void
    {
        parent::setUp();

        $fileLocatorMock = $this->createMock(
            \Symfony\Component\Config\FileLocatorInterface::class
        );

        $this->jsonLoader = $this->getMockBuilder(
            'Cascade\Config\Loader\FileLoader\Json'
        )
            ->setConstructorArgs(array($fileLocatorMock))
            ->onlyMethods(array('readFrom', 'isFile', 'validateExtension'))
            ->getMock();
    }

    protected function teardown(): void
    {
        $this->jsonLoader = null;
        parent::tearDown();
    }

    /**
     * Test loading a JSON string
     */
    public function testLoad(): void
    {
        $json = Fixtures::getSampleJsonString();

        $this->jsonLoader->expects($this->once())
            ->method('readFrom')
            ->willReturn($json);

        $this->assertEquals(
            json_decode($json, true),
            $this->jsonLoader->load($json)
        );
    }

    /**
     * Data provider for testSupportsWithInvalidResource
     *
     * @return array array non-string values
     */
    public function notStringDataProvider(): array
    {
        return array(
            array(array()),
            array(true),
            array(123),
            array(123.456),
            array(null),
            array(new \stdClass),
            array(function (): void {
            })
        );
    }

    /**
     * Test loading resources supported by the JsonLoader
     *
     * @param mixed $invalidResource Invalid resource value
     * @dataProvider notStringDataProvider
     */
    public function testSupportsWithInvalidResource($invalidResource): void
    {
        $this->assertFalse($this->jsonLoader->supports($invalidResource));
    }

    /**
     * Test loading a JSON string
     */
    public function testSupportsWithJsonString(): void
    {
        $this->jsonLoader->expects($this->once())
            ->method('isFile')
            ->willReturn(false);

        $json = Fixtures::getSampleJsonString();

        $this->assertTrue($this->jsonLoader->supports($json));
    }

    /**
     * Test loading a JSON file
     * Note that this function tests isJson with a valid Json string
     */
    public function testSupportsWithJsonFile(): void
    {
        $this->jsonLoader->expects($this->once())
            ->method('isFile')
            ->willReturn(true);

        $this->jsonLoader->expects($this->once())
            ->method('validateExtension')
            ->willReturn(true);

        $jsonFile = Fixtures::getSampleJsonFile();

        $this->assertTrue($this->jsonLoader->supports($jsonFile));
    }

    /**
     * Test isJson method with invalid JSON string.
     * Valid scenario is tested by the method above
     */
    public function testSupportsWithNonJsonString(): void
    {
        $this->jsonLoader->expects($this->once())
            ->method('isFile')
            ->willReturn(false);

        $someString = Fixtures::getSampleString();

        $this->assertFalse($this->jsonLoader->supports($someString));
    }
}
