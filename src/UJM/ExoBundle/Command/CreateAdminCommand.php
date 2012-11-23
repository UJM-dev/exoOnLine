<?php

namespace UJM\ExoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UJM\ExoBundle\Entity\User;

/**
 * Creates a default admin user.
 */
class CreateAdminCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('exoline:create-default-admin')
            ->setDescription('Creates a default admin user, with "admin/admin" credentials.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Creating default admin user...');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $adminRole = $em->getRepository('UJM\ExoBundle\Entity\Role')
            ->findOneByName('ROLE_ADMIN');
        $user = new User();
        $user->setFirstName('John');
        $user->setName('Doe');
        $user->setUsername('admin');
        $user->setPassword('admin');
        $user->addRole($adminRole);

        $em->persist($user);
        $em->flush();

        $output->writeln('Done');
    }
}