<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PayDunyaService
{
    /**
     * URL de base selon le mode.
     */
    private function baseUrl(): string
    {
        $mode = config('paydunya.mode', 'test');

        if ($mode === 'test') {
            return 'https://app.paydunya.com/sandbox-api/v1';
        }

        return 'https://app.paydunya.com/api/v1';
    }

    /**
     * En-têtes API PayDunya.
     */
    private function headers(): array
    {
        return [
            'Content-Type' => 'application/json',
            'PAYDUNYA-MASTER-KEY' => config('paydunya.master_key'),
            'PAYDUNYA-PRIVATE-KEY' => config('paydunya.private_key'),
            'PAYDUNYA-TOKEN' => config('paydunya.token'),
        ];
    }

    /**
     * Création d'une facture PayDunya.
     */
    public function createInvoice(array $data): array
    {
        $url = $this->baseUrl() . '/checkout-invoice/create';

        $response = Http::withHeaders($this->headers())
            ->post($url, $data);

        if ($response->failed()) {
            throw new RuntimeException(
                'Erreur PayDunya : ' . $response->body()
            );
        }

        $result = $response->json();

        if (($result['response_code'] ?? null) !== '00') {
            throw new RuntimeException(
                'PayDunya a refusé la demande : ' .
                ($result['response_text'] ?? 'Erreur inconnue')
            );
        }

        return $result;
    }

    /**
     * Vérification d'une facture.
     */
    public function confirmInvoice(string $token): array
    {
        $url = $this->baseUrl()
            . '/checkout-invoice/confirm/'
            . $token;

        $response = Http::withHeaders($this->headers())
            ->get($url);

        if ($response->failed()) {
            throw new RuntimeException(
                'Erreur lors de la vérification PayDunya : ' .
                $response->body()
            );
        }

        $result = $response->json();

        if (($result['response_code'] ?? null) !== '00') {
            throw new RuntimeException(
                'PayDunya n\'a pas pu vérifier la facture : ' .
                ($result['response_text'] ?? 'Erreur inconnue')
            );
        }

        return $result;
    }

    /**
     * Paiement Wave.
     */
    public function payWave(
        string $fullName,
        string $email,
        string $phone,
        string $invoiceToken
    ): array {
        if (config('paydunya.mode') === 'test') {
            return $this->sandboxMakePayment($invoiceToken);
        }

        return $this->softPayRequest(
            '/softpay/wave-senegal',
            [
                'wave_senegal_fullName' => $fullName,
                'wave_senegal_email' => $email,
                'wave_senegal_phone' => $phone,
                'wave_senegal_payment_token' => $invoiceToken,
            ]
        );
    }

    /**
     * Paiement Orange Money.
     */
    public function payOrangeMoney(
        string $fullName,
        string $email,
        string $phone,
        string $invoiceToken
    ): array {
        if (config('paydunya.mode') === 'test') {
            return $this->sandboxMakePayment($invoiceToken);
        }

        return $this->softPayRequest(
            '/softpay/new-orange-money-senegal',
            [
                'customer_name' => $fullName,
                'customer_email' => $email,
                'phone_number' => $phone,
                'invoice_token' => $invoiceToken,
            ]
        );
    }

    /**
     * Paiement Wizall.
     */
    public function payWizall(
        string $fullName,
        string $email,
        string $phone,
        string $invoiceToken
    ): array {
        if (config('paydunya.mode') === 'test') {
            return $this->sandboxMakePayment($invoiceToken);
        }

        return $this->softPayRequest(
            '/softpay/wizall-money-senegal',
            [
                'customer_name' => $fullName,
                'customer_email' => $email,
                'phone_number' => $phone,
                'invoice_token' => $invoiceToken,
            ]
        );
    }

    /**
     * Paiement SoftPay en mode sandbox.
     *
     * PayDunya utilise un endpoint sandbox commun
     * pour les tests SoftPay.
     */
    private function sandboxMakePayment(string $invoiceToken): array
    {
        $url = $this->baseUrl() . '/softpay/checkout/make-payment';

        $data = [
            'phone_number' => config('paydunya.test_customer_phone'),
            'customer_email' => config('paydunya.test_customer_email'),
            'password' => config('paydunya.test_customer_password'),
            'invoice_token' => $invoiceToken,
        ];

        $response = Http::withHeaders($this->headers())
            ->post($url, $data);

        if ($response->failed()) {
            throw new RuntimeException(
                'Erreur SoftPay Sandbox PayDunya : ' .
                $response->body()
            );
        }

        $result = $response->json();

        if (($result['success'] ?? false) !== true) {
            throw new RuntimeException(
                'PayDunya a refusé le paiement sandbox : ' .
                ($result['message'] ?? 'Erreur inconnue')
            );
        }

        return $result;
    }

    /**
     * Requête SoftPay en production.
     */
    private function softPayRequest(
        string $endpoint,
        array $data
    ): array {
        $url = $this->baseUrl() . $endpoint;

        $response = Http::withHeaders($this->headers())
            ->post($url, $data);

        if ($response->failed()) {
            throw new RuntimeException(
                'Erreur SoftPay PayDunya : ' .
                $response->body()
            );
        }

        $result = $response->json();

        if (($result['success'] ?? false) !== true) {
            throw new RuntimeException(
                'PayDunya a refusé le paiement : ' .
                ($result['message'] ?? 'Erreur inconnue')
            );
        }

        return $result;
    }
}