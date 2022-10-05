<?php

declare(strict_types=1);

namespace Sptec\GmailApiMailerBundle\Mailer\Bridge\GmailApi\Transport;

use Google\Exception;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Sptec\GmailApiMailerBundle\Google\GoogleHelper;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;

class GmailApiTransport extends AbstractTransport
{
    private GoogleHelper $googleHelper;

    public function __construct(GoogleHelper $googleHelper, ?EventDispatcherInterface $dispatcher = null, ?LoggerInterface $logger = null)
    {
        parent::__construct($dispatcher, $logger);
        $this->googleHelper = $googleHelper;
    }

    public function __toString(): string
    {
        return 'gmail+api';
    }

    protected function doSend(SentMessage $message): void
    {
        $rawMessage = \rtrim(\strtr(\base64_encode($message->getMessage()->toString()), '+/', '-_'), '=');

        $service = new Gmail($this->googleHelper->getAuthenticatedClient());
        $gmailMessage = new Message();
        $gmailMessage->setRaw($rawMessage);

        try {
            $service->users_messages->send(GoogleHelper::USER, $gmailMessage);
        } catch (Exception $exception) {
            throw new TransportException(
                'Unable to send an email: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }
}
