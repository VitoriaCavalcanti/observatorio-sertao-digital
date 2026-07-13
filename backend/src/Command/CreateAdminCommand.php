<?php
namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-admin', description: 'Cria um administrador do Observatório.')]
final class CreateAdminCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly UserPasswordHasherInterface $hasher) { parent::__construct(); }
    protected function configure(): void { $this->addArgument('email', InputArgument::REQUIRED)->addArgument('senha', InputArgument::REQUIRED)->addArgument('nome', InputArgument::OPTIONAL, 'Nome completo', 'Administrador'); }
    protected function execute(InputInterface $input, OutputInterface $output): int { $user = (new User())->setEmail((string) $input->getArgument('email'))->setNome((string) $input->getArgument('nome'))->setRoles([User::ROLE_ADMIN]); $user->setPassword($this->hasher->hashPassword($user, (string) $input->getArgument('senha'))); $this->em->persist($user); $this->em->flush(); $output->writeln('<info>Administrador criado com sucesso.</info>'); return Command::SUCCESS; }
}
