<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailCadastroAdv extends Mailable
{
    use Queueable, SerializesModels;

    public $nome;
    public $cpf;
    public $mensagem;
    public $email;
    public $senha;
    public $assunto;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nome,$cpf,$email,$senha,$assunto)
    {
        $this->nome = $nome;
        $this->cpf = $cpf;
        $this->email = $email;
        $this->senha = $senha;
        $this->assunto = $assunto;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $nome= $this->nome;
        $cpf = $this->cpf;
        $email = $this->email;
        $assunto = $this->assunto;
        $senha = $this->senha;

        $address = 'siapenacre@gmail.com';
        $subject = $assunto;
        $name = config('app.ti').' - '.config('app.secretaria').'/'.config('app.uf');

        return $this->view('email.sendMailCadastroAdv', compact("nome","cpf","senha"))
            ->from($address, $name)
            ->replyTo($address, $name)
            ->subject($subject)
            ->with([ 'message' => '' ]);
    }
}
