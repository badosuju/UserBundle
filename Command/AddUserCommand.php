<?php

namespace Ampisoft\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class AddUserCommand
 * @package Ampisoft\UserBundle\Command
 */
class AddUserCommand extends ContainerAwareCommand
{

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('amp:user:create')
            ->setDescription('add a new user to the database')
            ->addArgument( 'username', InputArgument::OPTIONAL, 'Username?' )
            ->addArgument('plainPassword1', InputArgument::OPTIONAL, 'Password?')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email?')
            ->addArgument('group', InputArgument::OPTIONAL, 'Group?');
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $plainPassword = $input->getArgument('plainPassword1');
        $email = $input->getArgument('email');


        $userManager = $this->getContainer()->get('amp_user.manager');
        $user = $userManager->createUser($username, $plainPassword, $email, 'ROLE_ADMIN');

        $output->writeln(sprintf('User: %s created', $user->getUsername()));

    }
}
