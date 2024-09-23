<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Models\User;
use App\Utility\Flash;
use Core\Controller;
use Core\View;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Contact extends Controller {

    public function contactAction() {
        $id = $this->route_params['id'];

        Articles::addOneView($id);
        $article = Articles::getOne($id);
        $toEmail = User::getEmailbyUserId($article[0]['user_id']);

        $inputs = [];
        $errors = [];
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputs = $_POST;
            $errors = $this->validate($inputs);

            if (empty($errors)) {
                if ($this->sendEmail($inputs['email'], $inputs['subject'], $inputs['message'], $toEmail['email'])) {
                    Flash::addMessage("Votre message a été envoyé avec succès !");
                } else {
                    Flash::danger("Une erreur s'est produite lors de l'envoi de l'email.");
                }
                $message = Flash::getMessage();
            }
        }

        View::renderTemplate('Contact/contact.html', [
        'article' => $article[0],
        'toEmail' => $toEmail['email'],
        'inputs' => $inputs,
        'errors' => $errors,
        'message' => $message
        ]);
    }

    private function validate($data): array {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Le nom est requis.';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Un email valide est requis.';
        }

        if (empty($data['subject'])) {
            $errors['subject'] = 'Le sujet est requis.';
        }

        if (empty($data['message'])) {
            $errors['message'] = 'Le message est requis.';
        }

        return $errors;
    }

    private function sendEmail($fromEmail, $subject, $messageBody, $toEmail) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USER'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['SMTP_PORT'];

            $mail->setFrom($fromEmail, 'Vide Grenier - Le site de Matheo et Yannis !');
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = nl2br($messageBody);

            $mail->send();

            $mail->clearAddresses();
            $mail->addAddress($fromEmail);

            $mail->Subject = "Confirmation de reception de votre message";
            $mail->Body    = nl2br("Merci de nous avoir contacte. Nous avons bien recu votre message et votre destinataire vous repondra des que posssible.");

            return $mail->send();

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }


}
