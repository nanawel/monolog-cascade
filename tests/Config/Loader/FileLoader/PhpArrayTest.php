<?php
/**
 * This file is part of the Monolog Cascade package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cascade\Tests\Config\Loader\FileLoader;

use Symfony\Component\Config\FileLocator;

use Cascade\Config\Loader\FileLoader\PhpArray as ArrayLoader;

/**
 * Class PhpArrayTest
 */
class PhpArrayTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ArrayLoader
     */
    protected $loader;

    protected function setUp(): void
    {
        $this->loader = new ArrayLoader(new FileLocator());
    }

    protected function teardown(): void
    {
        $this->loader = null;
    }

    public function testSupportsPhpFile(): void
    {
        $this->assertTrue($this->loader->supports(__DIR__.'/../../../Fixtures/fixture_config.php'));
    }

    public function testDoesNotSupportNonPhpFiles(): void
    {
        $this->assertFalse($this->loader->supports('foo'));
        $this->assertFalse($this->loader->supports(__DIR__.'/../../../Fixtures/fixture_config.json'));
    }

    public function testThrowsExceptionWhenLoadingFileIfDoesNotReturnValidPhpArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->loader->load(__DIR__.'/../../../Fixtures/fixture_invalid_config.php');
    }

    public function testLoadsPhpArrayConfigFromFile(): void
    {
        $this->assertSame(
            include __DIR__.'/../../../Fixtures/fixture_config.php',
            $this->loader->load(__DIR__.'/../../../Fixtures/fixture_config.php')
        );
    }
}
