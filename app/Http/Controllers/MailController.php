<?php
namespace App\Http\Controllers;

use App\Mail\MyMailer;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class MailController extends Controller
{

    public function send(MyMailer $myMailer)
    {
        $myMailer->sendEmail(
            'stanislavkorobkin@mail.ru',
            'Test Subject',
            '<p>This is the email body in <strong>HTML</strong>.</p>'
        );


        return 'Email sent successfully!';
    }
}
