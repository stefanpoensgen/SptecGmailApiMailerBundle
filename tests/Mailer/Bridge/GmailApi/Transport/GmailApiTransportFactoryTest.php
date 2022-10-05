<?php

declare(strict_types=1);

namespace Sptec\GmailApiMailerBundle\Tests\Mailer\Bridge\GmailApi\Transport;

use Sptec\GmailApiMailerBundle\Google\GoogleHelper;
use Sptec\GmailApiMailerBundle\Mailer\Bridge\GmailApi\Transport\GmailApiTransport;
use Sptec\GmailApiMailerBundle\Mailer\Bridge\GmailApi\Transport\GmailApiTransportFactory;
use Symfony\Component\Mailer\Test\TransportFactoryTestCase;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\TransportFactoryInterface;

class GmailApiTransportFactoryTest extends TransportFactoryTestCase
{
    public function getFactory(): TransportFactoryInterface
    {
        return new GmailApiTransportFactory(
            $this->getGoogleHelper(),
            $this->getDispatcher(),
            $this->getClient(),
            $this->getLogger()
        );
    }

    public function supportsProvider(): iterable
    {
        yield [
            new Dsn('gmail+api', 'null'),
            true,
        ];
    }

    public function createProvider(): iterable
    {
        $googleHelper = $this->getGoogleHelper();
        $dispatcher = $this->getDispatcher();
        $logger = $this->getLogger();

        yield [
            new Dsn('gmail+api', 'null'),
            new GmailApiTransport($googleHelper, $dispatcher, $logger),
        ];
    }

    protected function getGoogleHelper(): GoogleHelper
    {
        return $this->createMock(GoogleHelper::class);
    }
}
