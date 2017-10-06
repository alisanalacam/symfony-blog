<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

/**
 * Class AddUserCommand
 * @package AppBundle\Command
 */
class AddUserCommand extends ContainerAwareCommand
{
    const MAX_ATTEMPTS = 5;
    /**
     * @var ObjectManager
     */
    private $entityManager;
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:add-user')
            ->setDescription('Kullanıcı oluşturur')
            ->addArgument('username', InputArgument::OPTIONAL, 'Yeni kullanıcı adı')
            ->addArgument('password', InputArgument::OPTIONAL, 'Kullanıcının şifresi')
            ->addArgument('email', InputArgument::OPTIONAL, 'Kullanıcının eposta adresi')
            ->addOption('is-admin', null, InputOption::VALUE_NONE, 'yönetici mi?')
        ;
    }
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
    }
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null !== $input->getArgument('username') && null !== $input->getArgument('password') && null !== $input->getArgument('email')) {
            return;
        }
        $output->writeln('');
        $output->writeln('Kullanıcı ekleme sihirbazı');
        $output->writeln('-----------------------------------');
        $output->writeln(array(
            '',
            '',
            ' $ php app/console app:add-user username password email@example.com',
            '',
        ));
        $output->writeln(array(
            '',
            'Kullanıcı bilgileri sırasıyla sorulacaktır',
            '',
        ));
        $console = $this->getHelper('question');
        $username = $input->getArgument('username');
        if (null === $username) {
            $question = new Question(' > <info>Kullanıcı adı</info>: ');
            $question->setValidator(function ($answer) {
                if (empty($answer)) {
                    throw new \RuntimeException('Kullanıcı adı boş olamaz');
                }
                return $answer;
            });
            $question->setMaxAttempts(self::MAX_ATTEMPTS);
            $username = $console->ask($input, $output, $question);
            $input->setArgument('username', $username);
        } else {
            $output->writeln(' > <info>Kullanıcı adı</info>: '.$username);
        }
        $password = $input->getArgument('password');
        if (null === $password) {
            $question = new Question(' > <info>Şifre</info> (yazılan şifre ekranda görünmeyecektir): ');
            $question->setValidator(array($this, 'passwordValidator'));
            $question->setHidden(true);
            $question->setMaxAttempts(self::MAX_ATTEMPTS);
            $password = $console->ask($input, $output, $question);
            $input->setArgument('password', $password);
        } else {
            $output->writeln(' > <info>Şifre</info>: '.str_repeat('*', strlen($password)));
        }
        $email = $input->getArgument('email');
        if (null === $email) {
            $question = new Question(' > <info>Eposta</info>: ');
            $question->setValidator(array($this, 'emailValidator'));
            $question->setMaxAttempts(self::MAX_ATTEMPTS);
            $email = $console->ask($input, $output, $question);
            $input->setArgument('email', $email);
        } else {
            $output->writeln(' > <info>Eposta</info>: '.$email);
        }
    }
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = microtime(true);
        $username = $input->getArgument('username');
        $plainPassword = $input->getArgument('password');
        $email = $input->getArgument('email');
        $isAdmin = $input->getOption('is-admin');
        $existingUser = $this->entityManager->getRepository('AppBundle:User')->findOneBy(array('username' => $username));
        if (null !== $existingUser) {
            throw new \RuntimeException(sprintf('"%s" isimli kullanıcı sistemde mevcut.', $username));
        }
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles(array($isAdmin ? 'ROLE_ADMIN' : 'ROLE_USER'));
        $encoder = $this->getContainer()->get('security.password_encoder');
        $encodedPassword = $encoder->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $output->writeln('');
        $output->writeln(sprintf('[OK] %s başarıyla oluşturuldu: %s (%s)', $isAdmin ? 'Yönetici Kullanıcı' : 'Kullanıcı', $user->getUsername(), $user->getEmail()));
        if ($output->isVerbose()) {
            $finishTime = microtime(true);
            $elapsedTime = $finishTime - $startTime;
            $output->writeln(sprintf('[INFO] Yeni kullanıcı id: %d / Geçen zaman: %.2f ms', $user->getId(), $elapsedTime*1000));
        }
    }
    /**
     * @param $plainPassword
     * @return mixed
     * @throws \Exception
     */
    public function passwordValidator($plainPassword)
    {
        if (empty($plainPassword)) {
            throw new \Exception('Şifre bol olamaz');
        }
        if (strlen(trim($plainPassword)) < 6) {
            throw new \Exception('Şifre 6 karakterden fazla olmalıdır');
        }
        return $plainPassword;
    }
    /**
     * @param $email
     * @return mixed
     * @throws \Exception
     */
    public function emailValidator($email)
    {
        if (empty($email)) {
            throw new \Exception('Eposta boş olamaz');
        }
        if (false === strpos($email, '@')) {
            throw new \Exception('Geçerli bir eposta adresi giriniz');
        }
        return $email;
    }
}