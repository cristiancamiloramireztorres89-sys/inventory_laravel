<?php

namespace App\Mail;

use App\Models\Venta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FacturaVentaMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Instancia de la venta registrada.
     */
    public Venta $venta;

    /**
     * Contenido binario del PDF generado.
     */
    public ?string $pdfContent;

    /**
     * Crear una nueva instancia de mensaje.
     */
    public function __construct(Venta $venta, ?string $pdfContent = null)
    {
        $this->venta = $venta;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Obtener el sobre del mensaje (Envelope).
     */
    public function envelope(): Envelope
    {
        $numeroFactura = str_pad($this->venta->id_venta, 5, '0', STR_PAD_LEFT);

        return new Envelope(
            subject: "Tu Factura de Compra #VEN-{$numeroFactura} | Inventory System",
        );
    }

    /**
     * Obtener la definición del contenido del mensaje.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.factura_venta',
        );
    }

    /**
     * Obtener los archivos adjuntos del mensaje.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->pdfContent) {
            $numeroFactura = str_pad($this->venta->id_venta, 5, '0', STR_PAD_LEFT);

            return [
                Attachment::fromData(fn () => $this->pdfContent, "Factura_POS_VEN-{$numeroFactura}.pdf")
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
