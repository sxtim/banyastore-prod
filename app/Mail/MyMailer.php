<?php

namespace App\Mail;


namespace App\Mail;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class MyMailer
{
    public function sendEmail($to, $subject, $body): void
    {
        $transport = Transport::fromDsn(config('mailer.dsn'));
        $mailer = new Mailer($transport);

        $email = (new Email())
            ->from('info@banyastore.ru')
            ->to($to)
            ->subject($subject)
            ->text($body)
            ->html($body);

        $mailer->send($email);
    }
}
