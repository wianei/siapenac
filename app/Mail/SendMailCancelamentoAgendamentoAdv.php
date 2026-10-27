<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailCancelamentoAgendamentoAdv extends Mailable
{
    use Queueable, SerializesModels;

    public $mensagem;
    public $email;
    public $assunto;
    public $interno;
    public $data;
    public $horario;
    public $unidade;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $interno, $data, $horario, $unidade, $mensagem,$assunto)
    {
        $this->mensagem = $mensagem;
        $this->assunto = $assunto;
        $this->email = $email;
        $this->interno = $interno;
        $this->data = $data;
        $this->horario = $horario;
        $this->unidade = $unidade;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mensagem = $this->mensagem;
        $assunto = $this->assunto;
        $interno = $this->interno;
        $data = $this->data;
        $horario = $this->horario;
        $unidade = $this->unidade;

        $address = 'siapenacre@gmail.com';
        $subject = $assunto;
        $name = config('app.ti').' - '.config('app.secretaria').'/'.config('app.uf');

        return $this->view('email.cancelamentoAgendamentoAdvUnidade', compact("mensagem",'interno','data','horario','unidade'))
            ->from($address, $name)
            ->replyTo($address, $name)
            ->subject($subject)
            ->with([ 'message' => '' ]);
    }
}
