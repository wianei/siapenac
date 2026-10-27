<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailCadastroUser extends Mailable
{
    use Queueable, SerializesModels;

    public $nome;
    public $cpf;
    public $mensagem;
    public $email;
    public $orgao;
    public $senha;
    public $assunto;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nome,$cpf,$mensagem,$email,$orgao,$senha,$assunto)
    {
        $this->nome = $nome;
        $this->cpf = $cpf;
        $this->mensagem = $mensagem;
        $this->email = $email;
        $this->orgao = $orgao;
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
        $mensagem = $this->mensagem;
        $email = $this->email;
        $orgao = $this->orgao;
        $assunto = $this->assunto;

        $address = 'siapenacre@gmail.com';
        $subject = $assunto;
        $name = config('app.ti').' - '.config('app.secretaria').'/'.config('app.uf');

        return $this->view('email.cadastroUsuario', compact("mensagem","nome","orgao","cpf"))
            ->from($address, $name)
            ->replyTo($address, $name)
            ->subject($subject)
            ->with([ 'message' => '' ]);
    }
}
