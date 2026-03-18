<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Sale;
use App\Models\Subscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentService
{
    protected ?string $provider = null;

    public function __construct(?string $provider = null)
    {
        $this->provider = $provider;
    }

    public function setProvider(string $provider): self
    {
        $this->provider = $provider;
        return $this;
    }

    public function initiatePayment(float $amount, string $phoneNumber, ?int $saleId = null, ?int $subscriptionId = null): Payment
    {
        $tenantId = null;
        
        if ($saleId) {
            $sale = Sale::find($saleId);
            $tenantId = $sale?->tenant_id;
        } elseif ($subscriptionId) {
            $subscription = Subscription::find($subscriptionId);
            $tenantId = $subscription?->tenant_id;
        }

        $payment = Payment::create([
            'tenant_id' => $tenantId,
            'sale_id' => $saleId,
            'subscription_id' => $subscriptionId,
            'transaction_id' => $this->generateTransactionId(),
            'provider' => $this->provider,
            'phone_number' => $phoneNumber,
            'amount' => $amount,
            'status' => 'pending',
            'payment_type' => $saleId ? 'sale' : 'subscription',
        ]);

        try {
            if ($this->provider === 'mvola') {
                $this->processMVolaPayment($payment);
            } elseif ($this->provider === 'orange_money') {
                $this->processOrangeMoneyPayment($payment);
            }
        } catch (\Exception $e) {
            $payment->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }

        return $payment;
    }

    protected function processMVolaPayment(Payment $payment): void
    {
        $config = config('mvola');
        
        if (!$config['enabled']) {
            $payment->update(['status' => 'success', 'paid_at' => now()]);
            return;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getMVolaToken(),
            'Content-Type' => 'application/json',
        ])->post($config['api_url'] . '/payment', [
            'amount' => $payment->amount,
            'phone' => $payment->phone_number,
            'merchant_id' => $config['merchant_id'],
            'transaction_id' => $payment->transaction_id,
        ]);

        if ($response->successful()) {
            $payment->update([
                'status' => 'success',
                'paid_at' => now(),
                'response_data' => json_encode($response->json()),
            ]);
        } else {
            $payment->update([
                'status' => 'failed',
                'response_data' => json_encode($response->json()),
            ]);
        }
    }

    protected function processOrangeMoneyPayment(Payment $payment): void
    {
        $config = config('orange_money');
        
        if (!$config['enabled']) {
            $payment->update(['status' => 'success', 'paid_at' => now()]);
            return;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getOrangeMoneyToken(),
            'Content-Type' => 'application/json',
        ])->post($config['api_url'] . '/payment', [
            'amount' => $payment->amount,
            'phone' => $payment->phone_number,
            'merchant_id' => $config['merchant_id'],
            'transaction_id' => $payment->transaction_id,
        ]);

        if ($response->successful()) {
            $payment->update([
                'status' => 'success',
                'paid_at' => now(),
                'response_data' => json_encode($response->json()),
            ]);
        } else {
            $payment->update([
                'status' => 'failed',
                'response_data' => json_encode($response->json()),
            ]);
        }
    }

    protected function getMVolaToken(): string
    {
        $config = config('mvola');
        
        $response = Http::post($config['api_url'] . '/auth/token', [
            'api_key' => $config['api_key'],
            'api_secret' => $config['api_secret'],
        ]);

        return $response->json('access_token');
    }

    protected function getOrangeMoneyToken(): string
    {
        $config = config('orange_money');
        
        $response = Http::post($config['api_url'] . '/auth/token', [
            'api_key' => $config['api_key'],
            'api_secret' => $config['api_secret'],
        ]);

        return $response->json('access_token');
    }

    protected function generateTransactionId(): string
    {
        return 'TXN-' . strtoupper(Str::random(16));
    }

    public function handleWebhook(string $provider, array $data): void
    {
        $transactionId = $data['transaction_id'] ?? null;
        
        if (!$transactionId) {
            return;
        }

        $payment = Payment::where('transaction_id', $transactionId)->first();
        
        if (!$payment) {
            return;
        }

        $status = $data['status'] ?? 'pending';
        
        $payment->update([
            'status' => $status === 'SUCCESS' ? 'success' : 'failed',
            'paid_at' => $status === 'SUCCESS' ? now() : null,
            'response_data' => json_encode($data),
        ]);

        if ($payment->status === 'success' && $payment->sale_id) {
            $sale = Sale::find($payment->sale_id);
            if ($sale) {
                $sale->update([
                    'payment_status' => 'paid',
                    'paid_amount' => $sale->total,
                    'due_amount' => 0,
                ]);
            }
        }

        if ($payment->status === 'success' && $payment->subscription_id) {
            $subscription = Subscription::find($payment->subscription_id);
            if ($subscription) {
                $subscription->update([
                    'status' => 'active',
                    'transaction_id' => $transactionId,
                ]);
            }
        }
    }

    public function checkPaymentStatus(Payment $payment): string
    {
        if ($this->provider === 'mvola') {
            return $this->checkMVolaStatus($payment);
        } elseif ($this->provider === 'orange_money') {
            return $this->checkOrangeMoneyStatus($payment);
        }
        
        return $payment->status;
    }

    protected function checkMVolaStatus(Payment $payment): string
    {
        $config = config('mvola');
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getMVolaToken(),
        ])->get($config['api_url'] . '/payment/status/' . $payment->transaction_id);

        if ($response->successful()) {
            $status = $response->json('status');
            return $status === 'SUCCESS' ? 'success' : 'failed';
        }
        
        return $payment->status;
    }

    protected function checkOrangeMoneyStatus(Payment $payment): string
    {
        $config = config('orange_money');
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getOrangeMoneyToken(),
        ])->get($config['api_url'] . '/payment/status/' . $payment->transaction_id);

        if ($response->successful()) {
            $status = $response->json('status');
            return $status === 'SUCCESS' ? 'success' : 'failed';
        }
        
        return $payment->status;
    }
}
