<?php

namespace KnpU\LoremIpsumBundle;

use KnpU\LoremIpsumBundle\DependencyInjection\Compiler\WordProviderCompilerPass;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KnpULoremIpsumBundle extends AbstractBundle
{
//    public function build(ContainerBuilder $container)
//    {
//        $container->addCompilerPass(new WordProviderCompilerPass());
//    }

    protected string $extensionAlias = 'knpu_lorem_ipsum';

    // $config is the bundle Configuration that you usually process in ExtensionInterface::load() but already merged and processed
    /**
     * @param array<mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
//        $loader = new XmlFileLoader($builder, new FileLocator(__DIR__.'/Resources/config'));
//        $loader->load('services.xml');

/*        <service id="knpu_lorem_ipsum.knpu_ipsum" class="KnpU\LoremIpsumBundle\KnpUIpsum" public="true">
            <argument type="collection" />
        </service>

        <service id="knpu_lorem_ipsum.ipsum_api_controller" class="KnpU\LoremIpsumBundle\Controller\IpsumApiController" public="true">
            <argument type="service" id="knpu_lorem_ipsum.knpu_ipsum" />
            <argument type="service" id="event_dispatcher" on-invalid="null" />
        </service>

        <service id="knpu_lorem_ipsum.knpu_word_provider" class="KnpU\LoremIpsumBundle\KnpUWordProvider">
            <tag name="knpu_ipsum_word_provider" />
        </service>

        <service id="knpu_lorem_ipsum.word_provider" alias="knpu_lorem_ipsum.knpu_word_provider" public="false" />
        <service id="KnpU\LoremIpsumBundle\KnpUIpsum" alias="knpu_lorem_ipsum.knpu_ipsum" public="false" />*/


        $builder->autowire('knpu_lorem_ipsum.knpu_ipsum', KnpUIpsum::class)
            ->addArgument('collection');

        $serviceIdentifier='knpu_lorem_ipsum.knpu_ipsum';
        $builder->register(KnpUIpsum::class, $serviceIdentifier);
        $container->services()->alias(KnpUIpsum::class, $serviceIdentifier );

        $wordProviderId = 'knpu_lorem_ipsum.knpu_word_provider';
        $wordProviderTag = "knpu_ipsum_word_provider";
        $builder->register($wordProviderId, KnpUWordProvider::class)->addTag($wordProviderTag);



        $definition = $builder->getDefinition('knpu_lorem_ipsum.knpu_ipsum');
        $definition->setArgument(1, $config['unicorns_are_real']);
        $definition->setArgument(2, $config['min_sunshine']);

        $builder->registerForAutoconfiguration(WordProviderInterface::class)
            ->addTag('knpu_ipsum_word_provider');


        $references = [];
        foreach ($builder->findTaggedServiceIds('knpu_ipsum_word_provider') as $id => $tags) {
            $references[] = new Reference($id);
        }

        $definition->setArgument(0, $references);


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
