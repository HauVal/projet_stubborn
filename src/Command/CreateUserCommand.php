<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Créer un utilisateur.'
)]
class CreateUserCommand extends Command
{

    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var \Symfony\Component\Console\Helper\QuestionHelper $helper */
        $helper = $this->getHelper('question');
    
        $output->writeln('<info>Création d\'un utilisateur :</info>');
    
        // Question pour le nom
        $nameQuestion = new Question('Nom : ');
        $name = $helper->ask($input, $output, $nameQuestion);
    
        // Question pour l'email
        $emailQuestion = new Question('Email : ');
        $email = $helper->ask($input, $output, $emailQuestion);
    
        // Question pour le mot de passe
        $passwordQuestion = new Question('Mot de passe : ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $passwordQuestion);
    
        // Question pour le rôle
        $roleQuestion = new Question('Role (ROLE_USER ou ROLE_ADMIN) : ', 'ROLE_USER');
        $role = $helper->ask($input, $output, $roleQuestion);
    
        // Création de l'utilisateur
        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRoles([$role]);
    
        // Sauvegarde en base de données
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    
        $output->writeln('<info>Utilisateur créé avec succès !</info>');
    
        return Command::SUCCESS;
    }
}
