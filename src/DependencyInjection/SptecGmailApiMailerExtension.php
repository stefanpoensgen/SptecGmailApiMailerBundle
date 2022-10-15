<?php

declare(strict_types=1);

namespace Sptec\GmailApiMailerBundle\DependencyInjection;

use Google_Client;
use Sptec\GmailApiMailerBundle\Command\GoogleAuthCommand;
use Sptec\GmailApiMailerBundle\Google\GoogleHelper;
use Sptec\GmailApiMailerBundle\Mailer\Bridge\GmailApi\Transport\GmailApiTransportFactory;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SptecGmailApiMailerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        if (!$configuration instanceof ConfigurationInterface) {
            return;
        }

        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('env(' . GoogleHelper::TOKEN_CONST . ')', '{}');

        $container->register(GoogleHelper::class)
            ->addArgument(new Reference(Google_Client::class))
            ->addArgument($config['redirect_uri'])
            ->addArgument($config['access_token'])
            ->addArgument(new Reference('kernel'))
        ;

        $container->register(GoogleAuthCommand::class)
            ->addArgument(new Reference(GoogleHelper::class))
            ->addTag('console.command')
        ;

        $container->register(GmailApiTransportFactory::class)
            ->addArgument(new Reference(GoogleHelper::class))
            ->addArgument(new Reference('event_dispatcher'))
            ->addArgument(new Reference('http_client', ContainerInterface::IGNORE_ON_INVALID_REFERENCE))
            ->addArgument(new Reference('logger', ContainerInterface::IGNORE_ON_INVALID_REFERENCE))
            ->addTag('mailer.transport_factory')
        ;
    }
}
