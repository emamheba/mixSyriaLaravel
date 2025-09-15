<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;

class SmsService
{
    protected $apiUrl;
    protected $appKey;
    protected $authKey;
    protected $templateId;

    public function __construct()
    {
        // الكود الصحيح الذي يقرأ من ملفات الإعدادات
          $this->apiUrl = "https://whatsender.site/api/create-message";
    $this->appKey = "ff0b9fa2-bdbe-4ff3-aca6-accf23130a90";
    $this->authKey = "042UOma52FnGmq6cxAYhb9kDOjPlhCnMESgW3k9LlMGjwcuEBr";
    $this->templateId = "f8501413-d6b2-41e6-a840-06b2d8bcbd6d";
    Log::info('SmsService is using HARDCODED credentials for testing.');
    }

    public function sendOtp(string $phoneNumber, string $otp): bool
    {
        if (!$this->apiUrl || !$this->appKey || !$this->authKey || !$this->templateId) {
            Log::error('WhatSender API credentials or Template ID are not configured correctly.');
            return false;
        }

        try {
            $response = Http::asForm()->timeout(15)->post($this->apiUrl, [
                'appkey'      => $this->appKey,
                'authkey'     => $this->authKey,
                'to'          => $phoneNumber,
                'template_id' => $this->templateId,
                
                // إرسال المتغيرات كمصفوفة ترابطية (Key-Value)
                // هذا هو التنسيق الأكثر وضوحًا للـ API
                'variables'   => [
                    '{1}' => $otp,
                ],
            ]);

            if ($response->successful() && $response->json('message_status') === 'Success') {
                Log::info("Successfully sent OTP to {$phoneNumber}. Response: " . $response->body());
                return true;
            } else {
                Log::error("Failed to send OTP to {$phoneNumber}. Status: {$response->status()}. Response: " . $response->body());
                return false;
            }

        } catch (ConnectionException $e) {
            Log::error("Connection Exception while sending OTP to {$phoneNumber}: " . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Log::error("Generic Exception while sending OTP to {$phoneNumber}: " . $e->getMessage());
            return false;
        }
    }
}