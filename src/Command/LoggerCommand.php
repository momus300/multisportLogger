<?php
/**
 * Created by PhpStorm.
 * User: momus
 * Date: 7/14/18
 * Time: 2:11 PM
 */

namespace App\Command;

use App\Entity\History;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Utils\Logger as LoggerUtils;

class LoggerCommand extends ContainerAwareCommand
{
    private $em;
    private $monolog;
    private $output;

    public function __construct(?string $name = null, EntityManagerInterface $em, LoggerInterface $monolog)
    {
        parent::__construct($name);

        $this->em      = $em;
        $this->monolog = $monolog;
    }

    protected function configure()
    {
        $this
            ->setName('app:logger')
            ->setDescription('Logging as any user in system to multisport.')
            ->setHelp('This command allows you to log in to multisport for any users');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->output = $output;
        $this->startInfo();

        $users = $this->em->getRepository(Users::class)->findAllWithTriesOk();
        if ( ! $users) {
            $this->noUsersAndExit();
        }

        $loggedCount = 0;
        $failCount   = 0;
        $loggerUtils = new LoggerUtils();
        /** @var Users $user */
        foreach ($users as $user) {
            $output->write('wysylam dane dla usera: ' . $user->getLogin() . '...' . PHP_EOL);

            $response = $loggerUtils->logInAs($user);

            $history = new History();
            $history->setUser($user);
            $history->setSuccess($response);

            if ( ! $response) {
                $user->setTries($user->getTries() + 1);
                $this->em->persist($history);
                $this->em->flush();
                $info = $user->getTries() > 2 ? ' - blocked ' : '';
                $this->log('warning',
                    sprintf('[ERROR] - cannot login for user: %s blocked: %d times%s',
                        $user->getLogin(),
                        $user->getTries(),
                        $info
                    )
                );
                $failCount++;
                continue;
            }

            $user->setTries(0);
            $this->em->persist($history);
            $this->em->flush();
            $this->log('info', '[OK] - I logged for user: ' . $user->getLogin());
            $loggedCount++;
        }

        $this->finishInfo($loggedCount, $failCount);
    }

    private function startInfo(): void
    {
        $this->output->writeln([
            'Multisport Logger',
            '============',
        ]);
    }

    private function noUsersAndExit(): void
    {
        $this->output->writeln([
            '[FINISH]',
            '============',
            'No users'
        ]);
        exit();
    }

    private function log(string $status, string $message)
    {
        $this->monolog->$status($message);
        echo $message . PHP_EOL;
    }

    private function finishInfo(int $loggedCount, int $failCount): void
    {
        $this->output->writeln([
            '[FINISH]',
            '============',
            sprintf('logged: %d, fails %s.', $loggedCount, $failCount),
        ]);
    }
}