<?php
/**
 * PHP-DI
 *
 * @link      http://php-di.org/
 * @copyright Matthieu Napoli (http://mnapoli.fr/)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace FunctionalTest\DI\Bridge\Symfony;

use DI\Bridge\Symfony\SymfonyContainerBridge;
use DI\Container;
use FunctionalTest\DI\Bridge\Symfony\Fixtures\Class1;
use FunctionalTest\DI\Bridge\Symfony\Fixtures\Class2;

/**
 * Tests interactions between containers, i.e. entries that reference other entries in
 * other containers.
 *
 * @coversNothing
 */
class ContainerInteractionTest extends AbstractFunctionalTest
{
    /**
     * @test Get a Symfony entry from PHP-DI's container
     */
    public function phpdi_should_get_entries_from_symfony()
    {
        $kernel = $this->createKernel('class2.yml');

        /** @var SymfonyContainerBridge $container */
        $container = $kernel->getContainer();
        /** @var Container $phpdiContainer */
        $phpdiContainer = $container->getFallbackContainer();

        $phpdiContainer->set(
            'foo',
            \DI\object('FunctionalTest\DI\Bridge\Symfony\Fixtures\Class1')
                ->constructor(\DI\link('class2'))
        );

        $class1 = $container->get('foo');

        $this->assertTrue($class1 instanceof Class1);
    }

    /**
     * @test Get a PHP-DI entry from Symfony's container
     */
    public function symfonyGetInPHPDI()
    {
        $kernel = $this->createKernel('class1.yml');

        $class1 = $kernel->getContainer()->get('class1');

        $this->assertTrue($class1 instanceof Class1);
    }

    /**
     * @test Alias a Symfony entry from PHP-DI's container
     */
    public function phpdi_aliases_can_reference_symfony_entries()
    {
        $kernel = $this->createKernel('class2.yml');

        /** @var SymfonyContainerBridge $container */
        $container = $kernel->getContainer();
        /** @var Container $phpdiContainer */
        $phpdiContainer = $container->getFallbackContainer();

        $phpdiContainer->set('foo', \DI\link('class2'));

        $class2 = $container->get('foo');

        $this->assertTrue($class2 instanceof Class2);
    }
}
