<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


class SendMailErrorHandler extends Mailable
{
    use Queueable, SerializesModels;


    public $mensagem;
    public $email;
    public $assunto;
    public $trace;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mensagem, $nome, $cpf, $unidade, $time, $trace, $email,$assunto)
    {
        $this->mensagem = $mensagem;
        $this->email = $email;
        $this->assunto = $assunto;
        $this->trace = $trace;
        $this->nome = $nome;
        $this->cpf = $cpf;
        $this->unidade = $unidade;
        $this->time = $time;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {;
        $mensagem = $this->mensagem;
        $email = $this->email;
        $assunto = $this->assunto;
        $nome = $this->nome;
        $cpf = $this->cpf;
        $unidade = $this->unidade;
        $time = $this->time;

        $address = 'siapenacre@gmail.com';
        $subject = $assunto;
        $name = config('app.ti').' - '.config('app.secretaria').'/'.config('app.uf');

        return $this->view('email.errorHandler', compact("mensagem","nome","cpf", "unidade", "time", "trace"))
            ->from($address, $name)
            ->replyTo($address, $name)
            ->subject($subject)
            ->with([ 'message' => '' ]);
    }
}
