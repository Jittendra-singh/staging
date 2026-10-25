<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $subject;
    public $view;
    public $replyToAddress;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $view
     * @param array $data
     * @param string|null $replyToAddress
     * @return void
     */
    public function __construct($subject, $view, $data, $replyToAddress = null)
    {
        $this->subject = $subject;
        $this->view = $view;
        $this->data = $data;
        $this->replyToAddress = $replyToAddress;
    }
    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->subject,
            replyTo: $this->replyToAddress ? [$this->replyToAddress] : [],
        );
    }
    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: $this->view,
            with: $this->data,
        );
    }
    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
