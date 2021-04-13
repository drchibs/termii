<?php

namespace ManeOlawale\Termii\Api;

/*
 * This file is part of the Termii Client.
 *
 * (c) Ilesanmi Olawale Adedoun Twitter: @mane_olawale
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Token extends AbstractApi
{

    public function sendToken( $to, string $text, array $pin, string $from = null, string $channel = null, string $message_type = null )
    {
        if (!$this->client->getSenderId() && !$from) throw new \Exception('Termii client doesn`t have a default Sender ID');
        if (!$this->client->getChannel() && !$channel) throw new \Exception('Termii client doesn`t have a default message channel');

        $response = $this->post('sms/otp/send', [
            'to' => $to,
            'message_text' => $text,
            'message_type' => $message_type ?? 'ALPHANUMERIC',
            'pin_attempts' => $pin['attempts'],
            'pin_time_to_live' => $pin['time_to_live'],
            'pin_length' => $pin['length'],
            'pin_placeholder' => $pin['placeholder'],
            'pin_type' => $pin['type'] ?? 'NUMERIC',
            'from' => $from ?? $this->client->getSenderId(),
            'channel' => $channel ?? $this->client->getChannel(),
        ]);

        return $this->responseArray($response);
    }

    public function verify( string $pin_id, string $pin )
    {
        $response = $this->post('sms/otp/verify', [
            'pin_id' => $pin_id,
            'pin' => $pin,
        ]);

        return $this->responseArray($response);
    }

    public function verified( string $pin_id, string $pin )
    {
        $array = $this->verify($pin_id, $pin);

        return (isset($array['verified']) && $array['verified'] === true)? true : false;
    }

    public function expired( string $pin_id, string $pin )
    {
        $array = $this->verify($pin_id, $pin);

        return (isset($array['verified']) && $array['verified'] === 'Expired')? true : false;
    }

    public function failed( string $pin_id, string $pin )
    {
        $array = $this->verify($pin_id, $pin);

        return (!isset($array['verified']) && isset($array['pinId']))? true : false;
    }

    public function sendInAppToken( $phone_number, array $pin )
    {
        $response = $this->post('sms/otp/generate', [
            'phone_number' => $phone_number,
            'pin_attempts' => $pin['attempts'],
            'pin_time_to_live' => $pin['time_to_live'],
            'pin_length' => $pin['length'],
            'pin_type' => $pin['type'] ?? 'NUMERIC',
        ]);

        return $this->responseArray($response);
    }

}
