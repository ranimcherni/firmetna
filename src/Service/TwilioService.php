<?php

namespace App\Service;

use Twilio\Rest\Client;

class TwilioService
{
    private $sid;
    private $token;
    private $phone;

    // Change les noms ici pour correspondre au YAML ($twilioSid, etc.)
    public function __construct(string $twilioSid, string $twilioToken, string $twilioPhone)
    {
        $this->sid = $twilioSid;
        $this->token = $twilioToken;
        $this->phone = $twilioPhone;
    }

   public function sendSms(string $to, string $message)
{
    // Ajouter +216 si manquant
    if (!str_starts_with($to, '+')) {
        $to = '+216' . $to;
    }

    $client = new Client($this->sid, $this->token);

    return $client->messages->create($to, [
        'from' => $this->phone,
        'body' => $message
    ]);
}
}