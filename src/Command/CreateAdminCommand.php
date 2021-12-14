<?php

namespace App\Command;

use App\Enum\RolesEnum;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'app:create-admin';
    protected static $defaultDescription = 'Create application admin user';

    private UserService $userService;
    private EntityManagerInterface $em;

    public function __construct(UserService $userService, EntityManagerInterface $em, string $name = null)
    {
        parent::__construct($name);
        $this->userService = $userService;
        $this->em = $em;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $question1 = new Question('Please enter admin user name: ');
        $question2 = new Question('Please enter admin password: ');
        $userName = $helper->ask($input, $output, $question1);
        $password = $helper->ask($input, $output, $question2);

        $io = new SymfonyStyle($input, $output);

        try {
            $user = $this->userService->create($password, $userName);
            $user->addRole(RolesEnum::ADMIN);
            $this->em->persist($user);
            $this->em->flush();
            $io->success('Admin user successfully created');
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }

        return Command::SUCCESS;
    }
}
