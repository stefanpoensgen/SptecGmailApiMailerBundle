<?php

declare(strict_types=1);

namespace Sptec\GmailApiMailerBundle\DependencyInjection;

use Sptec\GmailApiMailerBundle\Google\GoogleHelper;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sptec_gmail_api_mailer');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('client_id')
                    ->info('Your Google Api Client Id')
                    ->defaultValue('')
                ->end()
                ->scalarNode('client_secret')
                    ->info('Your Google Api Client Secret')
                    ->defaultValue('')
                ->end()
                ->scalarNode('redirect_uri')
                    ->info('Your Google Api redirect Uri')
                    ->defaultValue('http://localhost')
                ->end()
                ->scalarNode('access_token')
                    ->info('Your Google Api token')
                    ->defaultValue('%env(json:' . GoogleHelper::TOKEN_CONST . ')%')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
