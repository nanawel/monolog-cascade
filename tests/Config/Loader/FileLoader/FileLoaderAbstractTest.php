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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\FileLocatorInterface;
use org\bovigo\vfs\vfsStream;

use Cascade\Tests\Fixtures;

/**
 * Class FileLoaderAbstractTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class FileLoaderAbstractTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Mock of extending Cascade\Config\Loader\FileLoader\FileLoaderAbstract
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected ?\PHPUnit\Framework\MockObject\MockObject $mock = null;

    protected function setUp(): void
    {
        parent::setUp();

        $fileLocatorMock = $this->createMock(
            \Symfony\Component\Config\FileLocatorInterface::class
        );

        $this->mock = $this->getMockBuilder(\Cascade\Config\Loader\FileLoader\FileLoaderAbstract::class)
            ->setConstructorArgs(array($fileLocatorMock))
            ->onlyMethods(array('load', 'supports'))
            ->getMock();

        // Setting valid extensions for tests
        $this->mock::$validExtensions = array('test', 'php');
    }

    protected function teardown(): void
    {
        $this->mock = null;
        parent::tearDown();
    }

    /**
     * Test loading config from a valid file
     */
    public function testReadFrom(): void
    {
        $this->assertEquals(
            Fixtures::getSampleYamlString(),
            $this->mock->readFrom(Fixtures::getSampleYamlFile())
        );
    }

    /**
     * Test loading config from a valid file
     */
    public function testLoadFileFromString(): void
    {
        $this->assertEquals(
            trim(Fixtures::getSampleString()),
            $this->mock->readFrom(Fixtures::getSampleString())
        );
    }

    /**
     * Data provider for testGetSectionOf
     *
     * @return array array with original value, section and expected value
     */
    public function extensionsDataProvider(): array
    {
        return array(
            array(true, 'hello/world.test'),
            array(true, 'hello/world.php'),
            array(false, 'hello/world.jpeg'),
            array(false, 'hello/world'),
            array(false, '')
        );
    }

    /**
     * Test validating the extension
     *
     * @param boolean $expected Expected boolean value
     * @param string filepath Filepath to validate
     * @dataProvider extensionsDataProvider
     */
    public function testValidateExtension(bool $expected, string $filepath): void
    {
        if ($expected) {
            $this->assertTrue($this->mock->validateExtension($filepath));
        } else {
            $this->assertFalse($this->mock->validateExtension($filepath));
        }
    }

    /**
     * Data provider for testGetSectionOf
     *
     * @return array array wit original value, section and expected value
     */
    public function arrayDataProvider(): array
    {
        return array(
            array(
                array(
                    'a' => array('aa' => 'AA', 'ab' => 'AB'),
                    'b' => array('ba' => 'BA', 'bb' => 'BB')
                ),
                'b',
                array('ba' => 'BA', 'bb' => 'BB')
            ),
            array(
                array('a' => 'A', 'b' => 'B'),
                'c',
                array('a' => 'A', 'b' => 'B'),
            ),
            array(
                array('a' => 'A', 'b' => 'B'),
                '',
                array('a' => 'A', 'b' => 'B'),
            )
        );
    }

    /**
     * Test the getSectionOf function
     *
     * @param array $array Array of options
     * @param string $section Section key
     * @param array $expected Expected array for the given section
     * @dataProvider arrayDataProvider
     */
    public function testGetSectionOf(array $array, string $section, array $expected): void
    {
        $this->assertSame($expected, $this->mock->getSectionOf($array, $section));
    }

    /**
     * Test loading an invalid file
     */
    public function testloadFileFromInvalidFile(): void
    {
        $this->expectException(\RuntimeException::class);
        // mocking the file system from a 'config_dir' base dir
        $root = vfsStream::setup('config_dir');

        // Adding an unreadable file (chmod 0000)
        vfsStream::newFile('config.yml', 0000)
            ->withContent(
                "---\n".
                "hidden_config: true"
            )->at($root);

        // This will throw an exception because the file is not readable
        $this->mock->readFrom(vfsStream::url('config_dir/config.yml'));

        stream_wrapper_unregister(vfsStream::SCHEME);
    }
}
