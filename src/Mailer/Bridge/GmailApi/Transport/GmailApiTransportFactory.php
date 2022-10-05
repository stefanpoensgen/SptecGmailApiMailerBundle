<?php

declare(strict_types=1);

namespace Sptec\GmailApiMailerBundle\Mailer\Bridge\GmailApi\Transport;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Sptec\GmailApiMailerBundle\Google\GoogleHelper;
use Symfony\Component\Mailer\Exception\UnsupportedSchemeException;
use Symfony\Component\Mailer\Transport\AbstractTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GmailApiTransportFactory extends AbstractTransportFactory
{
    private GoogleHelper $gmailHelper;

    public function __construct(
        GoogleHelper $gmailHelper,
        ?EventDispatcherInterface $dispatcher = null,
        ?HttpClientInterface $client = null,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($dispatcher, $client, $logger);
        $this->gmailHelper = $gmailHelper;
    }

    public function create(Dsn $dsn): TransportInterface
    {
        if (\in_array($dsn->getScheme(), $this->getSupportedSchemes(), true)) {
            return new GmailApiTransport($this->gmailHelper, $this->dispatcher, $this->logger);
        }

        throw new UnsupportedSchemeException($dsn, 'gmail+api', $this->getSupportedSchemes());
    }

    protected function getSupportedSchemes(): array
    {
        return ['gmail+api'];
    }
}
