<?php

declare(strict_types=1);

namespace Sptec\GmailApiMailerBundle\Command;

use Sptec\GmailApiMailerBundle\Google\GoogleHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GoogleAuthCommand extends Command
{
    private GoogleHelper $googleHelper;

    public function __construct(GoogleHelper $googleHelper)
    {
        $this->googleHelper = $googleHelper;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('sptec:google:auth')
            ->setDescription('Authenticate with Google API')
            ->setHelp('This command allows you to authenticate with Google API');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $errOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        $io = new SymfonyStyle($input, $errOutput);

        $client = $this->googleHelper->getClient();
        $authUrl = $client->createAuthUrl();

        $io->writeln(\sprintf('Open the following link in your browser: %s', $authUrl));
        $authCode = $io->ask('Enter verification code');

        if (!is_string($authCode)) {
            $io->error('Invalid verification code');
            return Command::FAILURE;
        }

        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        if (array_key_exists('error', $accessToken)) {
            $io->error(\sprintf('Error: %s', $accessToken['error_description']));
            return Command::FAILURE;
        }
        $client->setAccessToken($accessToken);
        $accessToken = json_encode($client->getAccessToken(), JSON_THROW_ON_ERROR);
        $io->block($accessToken, 'Access Token');

        $this->googleHelper->writeAccessToken($client->getAccessToken());

        return Command::SUCCESS;
    }
}
