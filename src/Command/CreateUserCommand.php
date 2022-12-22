<?php

namespace App\Command;

use App\Entity\User;
use App\Form\LoginForm;
use App\Service\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand]
class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $em,
        private Validator $validator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'E-mail')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();

        $email = $input->getArgument('email');
        $plaintextPassword = $input->getArgument('password');
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plaintextPassword);

        if (!$this->validateInput($user, $email, $hashedPassword, $output)) {
            return Command::INVALID;
        }

        $user->setEmail($email);
        $user->setPassword($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('Done');

        return Command::SUCCESS;
    }

    private function validateInput(
        User $user, $email, $hashedPassword, OutputInterface $output
    ): bool {
        $form = $this->validator->validateForm(
            LoginForm::class,
            ['email' => $email, 'password' => $hashedPassword],
            $user
        );
        if (!$form->isValid()) {
            foreach ($form->getErrors() as $error) {
                $output->writeln($error->getMessage());
            }
            foreach ($form as $child) {
                foreach ($child->getErrors() as $error) {
                    $output->writeln($child->getName() . ': ' . $error->getMessage());
                }
            }

            return false;
        }

        return true;
    }
}
