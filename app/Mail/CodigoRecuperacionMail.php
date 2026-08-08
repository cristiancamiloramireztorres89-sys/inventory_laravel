<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CodigoRecuperacionMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * El código numérico de 6 dígitos.
     */
    public string $codigo;

    /**
     * Nombre del usuario.
     */
    public string $nombre;

    /**
     * Crear una nueva instancia del mensaje.
     */
    public function __construct(string $codigo, string $nombre)
    {
        $this->codigo = $codigo;
        $this->nombre = $nombre;
    }

    /**
     * Obtener el sobre del mensaje (Envelope).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Tu Código de Seguridad: {$this->codigo} | Inventory System",
        );
    }

    /**
     * Obtener la definición del contenido del mensaje.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.codigo_recuperacion',
        );
    }

    /**
     * Obtener los archivos adjuntos del mensaje.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
