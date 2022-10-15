<?php

declare(strict_types=1);

namespace Sptec\GmailApiMailerBundle\Google;

use Google\Exception;
use Google\Service\Gmail;
use Google_Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class GoogleHelper
{
    public const TOKEN_CONST = 'GOOGLE_ACCESS_TOKEN';

    public const USER = 'me';

    private Google_Client $client;

    private string $redirectUri;

    private array $access_token;

    private KernelInterface $kernel;

    public function __construct(
        Google_Client $client,
        string $redirectUri,
        array $access_token,
        KernelInterface $kernel
    ) {
        $this->redirectUri = $redirectUri;
        $this->access_token = $access_token;
        $this->kernel = $kernel;
        $this->client = $client;
        $this->setClientDefaults();
    }

    public function getClient(): Google_Client
    {
        return $this->client;
    }

    public function getAuthenticatedClient(): Google_Client
    {
        $this->client->setAccessToken($this->access_token);

        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                $this->writeAccessToken($this->client->getAccessToken());
            } else {
                throw new Exception('New authentication is required. Run bin/console sptec:gmail:auth');
            }
        }

        return $this->client;
    }

    public function writeAccessToken(array $accessToken): void
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput([
            'command' => 'secrets:set',
            'name' => self::TOKEN_CONST,
        ]);

        $stream = fopen('php://memory', 'rb+');
        if ($stream === false) {
            throw new \RuntimeException('Could not open stream');
        }

        fwrite($stream, json_encode($accessToken, JSON_THROW_ON_ERROR));
        rewind($stream);
        $input->setStream($stream);
        $application->run($input, new NullOutput());
    }

    private function setClientDefaults(): void
    {
        $this->client->setApplicationName('Symfony Gmail API Mailer');
        $this->client->setRedirectUri($this->redirectUri);
        $this->client->setScopes(Gmail::GMAIL_SEND);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
    }
}
