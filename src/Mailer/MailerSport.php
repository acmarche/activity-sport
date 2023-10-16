<?php

namespace AcMarche\Sport\Mailer;

use AcMarche\Sport\Entity\Inscription;
use AcMarche\Sport\Entity\Person;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailerSport
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    /**
     * @param Person $person
     * @param Inscription[] $inscriptions
     * @return void
     * @throws TransportExceptionInterface
     */
    public function send(Person $person, array $inscriptions): void
    {
        $email = (new TemplatedEmail())
            ->from('sante@marche.be')
            ->to($person->email)
            ->cc('sante@marche.be')
            ->subject('Après-midi sportive du personnel')
            ->htmlTemplate('@AcMarcheSport/emails/signup.html.twig')
            ->context(['person' => $person, 'inscriptions' => $inscriptions]);

        $this->mailer->send($email);

    }

    /**
     * @param Person $person
     * @param Inscription[] $data
     * @return void
     * @throws TransportExceptionInterface
     */
    public function sendAll(Person $person, string $subject, string $message, array $data)
    {
        $email = (new TemplatedEmail())
            ->from('sante@marche.be')
            ->to($person->email)
            ->cc('sante@marche.be')
            ->subject($subject)
            ->htmlTemplate('@AcMarcheSport/emails/resume.html.twig')
            ->context(['person' => $person, 'inscriptions' => $data, 'message'=>$message]);

        $this->mailer->send($email);

    }
}