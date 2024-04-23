<?php

namespace Vanguard\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VirginFarmsSystemMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $content,$subject;

    public function __construct($subject, $content)
    {
        $this->content = $content;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.system.index')->with([
            'content' => $this->content
        ]);
    }

    public function setAttach(string $attachType = 'attach', string|array $filePath = null, string $name = null, string $disk = null, array $option = [])
    {
        switch ($attachType) {
            case 'attach':
                if (is_array($filePath) && !empty($filePath)) {
                    foreach ($filePath as $path) {
                        $this->attach($path, $option);
                    }
                } else {
                    if ($filePath) {
                        $this->attach($filePath, $option);
                    }
                }
                break;
            case 'attachFromStorage':
                if (is_array($filePath) && !empty($filePath)) {
                    foreach ($filePath as $path) {
                        $this->attachFromStorage($path, $name, $option);
                    }
                } else {
                    if ($filePath) {
                        $this->attachFromStorage($filePath, $name, $option);
                    }
                }
                break;
            case 'attachFromStorageDisk':
                if (is_array($filePath) && !empty($filePath)) {
                    foreach ($filePath as $path) {
                        $this->attachFromStorageDisk($disk, $path, $name, $option);
                    }
                } else {
                    if ($filePath) {
                        $this->attachFromStorageDisk($disk, $filePath, $name, $option);
                    }
                }
                break;
            default:
        }

        return $this;
    }
}
