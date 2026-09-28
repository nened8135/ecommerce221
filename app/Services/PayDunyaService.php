<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PayDunyaService
{
    /*
    |--------------------------------------------------------------------------
    | URL de base PayDunya
    |--------------------------------------------------------------------------
    |
    | Les endpoints SoftPay Wave, Orange Money et Wizall
    | utilisent actuellement /api/v1.
    |
    */

    private function baseUrl(): string
    {
        return 'https://app.paydunya.com/api/v1';
    }


    /*
    |--------------------------------------------------------------------------
    | Headers PayDunya
    |--------------------------------------------------------------------------
    */

    private function headers(): array
    {
        return [
            'Content-Type' =>
                'application/json',

            'PAYDUNYA-MASTER-KEY' =>
                config('paydunya.master_key'),

            'PAYDUNYA-PRIVATE-KEY' =>
                config('paydunya.private_key'),

            'PAYDUNYA-TOKEN' =>
                config('paydunya.token'),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Création d'une facture
    |--------------------------------------------------------------------------
    */

    public function createInvoice(
        array $data
    ): array {

        $url =
            $this->baseUrl()
            . '/checkout-invoice/create';


        $response =
            Http::withHeaders(
                $this->headers()
            )->post(
                $url,
                $data
            );


        if ($response->failed()) {

            throw new RuntimeException(
                'Erreur PayDunya : '
                . $response->body()
            );
        }


        $result =
            $response->json();


        if (
            ($result['response_code'] ?? null)
            !== '00'
        ) {

            throw new RuntimeException(
                'PayDunya a refusé la demande : '
                . (
                    $result['response_text']
                    ?? 'Erreur inconnue'
                )
            );
        }


        return $result;
    }


    /*
    |--------------------------------------------------------------------------
    | Confirmation d'une facture
    |--------------------------------------------------------------------------
    */

    public function confirmInvoice(
        string $token
    ): array {

        $url =
            $this->baseUrl()
            . '/checkout-invoice/confirm/'
            . $token;


        $response =
            Http::withHeaders(
                $this->headers()
            )->get($url);


        if ($response->failed()) {

            throw new RuntimeException(
                'Erreur lors de la vérification PayDunya : '
                . $response->body()
            );
        }


        $result =
            $response->json();


        if (
            ($result['response_code'] ?? null)
            !== '00'
        ) {

            throw new RuntimeException(
                'PayDunya n\'a pas pu vérifier la facture : '
                . (
                    $result['response_text']
                    ?? 'Erreur inconnue'
                )
            );
        }


        return $result;
    }


    /*
    |--------------------------------------------------------------------------
    | Paiement Wave Sénégal
    |--------------------------------------------------------------------------
    */

    public function payWave(
        string $fullName,
        string $email,
        string $phone,
        string $invoiceToken
    ): array {

        return $this->softPayRequest(
            '/softpay/wave-senegal',
            [
                'wave_senegal_fullName' =>
                    $fullName,

                'wave_senegal_email' =>
                    $email,

                'wave_senegal_phone' =>
                    $phone,

                'wave_senegal_payment_token' =>
                    $invoiceToken,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Paiement Orange Money Sénégal
    |--------------------------------------------------------------------------
    */

    public function payOrangeMoney(
        string $fullName,
        string $email,
        string $phone,
        string $invoiceToken
    ): array {

        return $this->softPayRequest(
            '/softpay/new-orange-money-senegal',
            [
                'customer_name' =>
                    $fullName,

                'customer_email' =>
                    $email,

                'phone_number' =>
                    $phone,

                'invoice_token' =>
                    $invoiceToken,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Paiement Wizall Sénégal
    |--------------------------------------------------------------------------
    */

    public function payWizall(
        string $fullName,
        string $email,
        string $phone,
        string $invoiceToken
    ): array {

        return $this->softPayRequest(
            '/softpay/wizall-money-senegal',
            [
                'customer_name' =>
                    $fullName,

                'customer_email' =>
                    $email,

                'phone_number' =>
                    $phone,

                'invoice_token' =>
                    $invoiceToken,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Requête SoftPay générique
    |--------------------------------------------------------------------------
    */

    private function softPayRequest(
        string $endpoint,
        array $data
    ): array {

        $url =
            $this->baseUrl()
            . $endpoint;


        $response =
            Http::withHeaders(
                $this->headers()
            )->post(
                $url,
                $data
            );


        if ($response->failed()) {

            throw new RuntimeException(
                'Erreur SoftPay PayDunya : '
                . $response->body()
            );
        }


        $result =
            $response->json();


        if (
            ($result['success'] ?? false)
            !== true
        ) {

            throw new RuntimeException(
                'PayDunya a refusé le paiement : '
                . (
                    $result['message']
                    ?? 'Erreur inconnue'
                )
            );
        }


        return $result;
    }
}