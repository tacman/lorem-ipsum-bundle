<?php

namespace KnpU\LoremIpsumBundle;

use KnpU\LoremIpsumBundle\DependencyInjection\Compiler\WordProviderCompilerPass;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KnpULoremIpsumBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new WordProviderCompilerPass());
    }

    protected string $extensionAlias = 'knpu_lorem_ipsum';

    // $config is the bundle Configuration that you usually process in ExtensionInterface::load() but already merged and processed
    /**
     * @param array<mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $loader = new XmlFileLoader($builder, new FileLocator(__DIR__.'/Resources/config'));
        $loader->load('services.xml');

        $definition = $builder->getDefinition('knpu_lorem_ipsum.knpu_ipsum');
        $definition->setArgument(1, $config['unicorns_are_real']);
        $definition->setArgument(2, $config['min_sunshine']);

        $builder->registerForAutoconfiguration(WordProviderInterface::class)
            ->addTag('knpu_ipsum_word_provider');

    }

    public function configure(DefinitionConfigurator $definition): void
    {
        // since the configuration is short, we can add it here
        $definition->rootNode()
            ->children()
            ->booleanNode('unicorns_are_real')->defaultTrue()->info('Whether or not you believe in unicorns')->end()
            ->integerNode('min_sunshine')->defaultValue(3)->info('How much do you like sunshine?')->end()
            ->end()
        ;
    }


}
