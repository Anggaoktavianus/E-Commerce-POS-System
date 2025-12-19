<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $token;
    protected $apiUrl;

    public function __construct()
    {
        $this->token = config('services.whatsapp.token', env('WHATSAPP_TOKEN'));
        $this->apiUrl = config('services.whatsapp.api_url', 'http://nusagateway.com/api/send-message.php');
    }

    /**
     * Send WhatsApp message
     *
     * @param string $phone Phone number (format: 082222205204)
     * @param string $message Message content
     * @return array
     */
    public function sendMessage($phone, $message, $retry = 0)
    {
        try {
            // Remove any non-numeric characters except +
            $phone = preg_replace('/[^0-9+]/', '', $phone);
            
            // Remove + if exists
            $phone = ltrim($phone, '+');
            
            // Convert to international format for WhatsApp API
            // If starts with 0, replace with 62 (Indonesia country code)
            if (strlen($phone) > 0 && $phone[0] === '0') {
                $phone = '62' . substr($phone, 1);
            }
            // If doesn't start with country code and is Indonesian number (10-13 digits), add 62
            elseif (strlen($phone) >= 10 && strlen($phone) <= 13 && !str_starts_with($phone, '62')) {
                $phone = '62' . $phone;
            }

            // Validate token and API URL
            if (empty($this->token)) {
                return [
                    'success' => false,
                    'message' => 'Token WhatsApp tidak dikonfigurasi. Silakan hubungi administrator.'
                ];
            }

            if (empty($this->apiUrl)) {
                return [
                    'success' => false,
                    'message' => 'URL API WhatsApp tidak dikonfigurasi. Silakan hubungi administrator.'
                ];
            }

            // Log phone number format for debugging
            Log::info('WhatsApp API Request', [
                'phone_formatted' => $phone,
                'phone_length' => strlen($phone),
                'api_url' => $this->apiUrl
            ]);

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, $this->apiUrl);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_TIMEOUT, 60); // 60 seconds timeout
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20); // 20 seconds connection timeout
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, [
                'token' => $this->token,
                'phone' => $phone,
                'message' => $message,
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            $curlErrno = curl_errno($curl);
            
            curl_close($curl);

            if ($error || $curlErrno) {
                $errorMessage = $error ?: curl_strerror($curlErrno);
                
                Log::error('WhatsApp API Error', [
                    'error' => $errorMessage,
                    'curl_errno' => $curlErrno,
                    'http_code' => $httpCode,
                    'phone' => $phone,
                    'api_url' => $this->apiUrl
                ]);
                
                // Retry mechanism for timeout errors (max 1 retry)
                if (($curlErrno === CURLE_OPERATION_TIMEOUTED || $curlErrno === CURLE_OPERATION_TIMEDOUT || stripos($errorMessage, 'timeout') !== false) && $retry < 1) {
                    Log::info('WhatsApp API Timeout, retrying...', [
                        'retry' => $retry + 1,
                        'phone' => $phone
                    ]);
                    
                    // Wait 2 seconds before retry
                    sleep(2);
                    return $this->sendMessage($phone, $message, $retry + 1);
                }
                
                // Provide user-friendly error messages
                if ($curlErrno === CURLE_OPERATION_TIMEOUTED || $curlErrno === CURLE_OPERATION_TIMEDOUT || stripos($errorMessage, 'timeout') !== false) {
                    return [
                        'success' => false,
                        'message' => 'Timeout saat mengirim pesan WhatsApp. Server mungkin sedang sibuk atau lambat. Silakan coba lagi dalam beberapa saat atau gunakan metode email sebagai alternatif.'
                    ];
                } elseif ($curlErrno === CURLE_COULDNT_CONNECT || stripos($errorMessage, 'connection') !== false) {
                    return [
                        'success' => false,
                        'message' => 'Tidak dapat terhubung ke server WhatsApp. Silakan cek koneksi internet atau coba lagi nanti. Anda juga bisa menggunakan metode email sebagai alternatif.'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Gagal mengirim pesan WhatsApp: ' . $errorMessage . '. Silakan coba lagi atau gunakan metode email.'
                    ];
                }
            }

            // Check HTTP status code first
            if ($httpCode !== 200 && $httpCode !== 0) {
                Log::warning('WhatsApp API HTTP Error', [
                    'http_code' => $httpCode,
                    'response' => $response,
                    'phone' => $phone
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Server WhatsApp mengembalikan error (HTTP ' . $httpCode . '). Silakan coba lagi.',
                    'response' => $response
                ];
            }

            // Parse response (assuming JSON response)
            $result = json_decode($response, true);
            
            // Check for success in various formats
            if ($httpCode === 200) {
                // Format 1: {"result": "true", "status": "pending"} (NusaGateway format - check first)
                // Check if result exists and is truthy (handles "true", true, 1, "1")
                if (isset($result['result'])) {
                    $resultValue = $result['result'];
                    $isSuccess = ($resultValue === 'true' || 
                                $resultValue === true || 
                                $resultValue === 1 || 
                                $resultValue === '1' ||
                                (is_string($resultValue) && strtolower($resultValue) === 'true'));
                    
                    if ($isSuccess) {
                        Log::info('WhatsApp API Success (NusaGateway format)', [
                            'result' => $result['result'],
                            'message' => $result['message'] ?? 'N/A',
                            'status' => $result['status'] ?? 'N/A',
                            'phone' => $phone
                        ]);
                        
                        return [
                            'success' => true,
                            'message' => isset($result['message']) ? $result['message'] : 'Pesan WhatsApp berhasil dikirim',
                            'response' => $result
                        ];
                    }
                }
                
                // Format 2: {"status": "success"}
                if (isset($result['status']) && $result['status'] === 'success') {
                    return [
                        'success' => true,
                        'message' => 'Pesan WhatsApp berhasil dikirim',
                        'response' => $result
                    ];
                }
                
                // Format 3: {"success": true}
                if (isset($result['success']) && $result['success'] === true) {
                    return [
                        'success' => true,
                        'message' => 'Pesan WhatsApp berhasil dikirim',
                        'response' => $result
                    ];
                }
                
                // Format 4: Plain text success indicators (check for "Sukses" in message)
                if (isset($result['message']) && (stripos($result['message'], 'sukses') !== false || stripos($result['message'], 'success') !== false)) {
                    return [
                        'success' => true,
                        'message' => $result['message'],
                        'response' => $result
                    ];
                }
                
                // Format 5: Plain text success indicators in raw response
                if (stripos($response, 'success') !== false || 
                    stripos($response, 'sent') !== false || 
                    stripos($response, 'sukses') !== false ||
                    stripos($response, 'ok') !== false ||
                    stripos($response, '"true"') !== false ||
                    stripos($response, 'result') !== false) {
                    return [
                        'success' => true,
                        'message' => 'Pesan WhatsApp berhasil dikirim',
                        'response' => $result ?: $response
                    ];
                }
            }

            // If response is empty but HTTP 200, might still be success
            if ($httpCode === 200 && empty($response)) {
                Log::info('WhatsApp API Empty Response', [
                    'http_code' => $httpCode,
                    'phone' => $phone
                ]);
                
                // Assume success if HTTP 200 with empty response
                return [
                    'success' => true,
                    'message' => 'Pesan WhatsApp berhasil dikirim',
                    'response' => null
                ];
            }

            Log::warning('WhatsApp API Unexpected Response', [
                'http_code' => $httpCode,
                'response' => $response,
                'phone' => $phone,
                'response_length' => strlen($response ?? '')
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengirim pesan WhatsApp. Silakan coba lagi atau gunakan metode email.',
                'response' => $response
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp Service Exception', [
                'error' => $e->getMessage(),
                'phone' => $phone ?? null
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim pesan WhatsApp: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send password reset link via WhatsApp
     *
     * @param string $phone Phone number
     * @param string $resetUrl Reset password URL
     * @return array
     */
    public function sendPasswordResetLink($phone, $resetUrl)
    {
        // Format pesan dengan newline yang kompatibel dengan WhatsApp API
        $message = "Halo! Anda telah meminta reset password untuk akun Samsae Store.\n\n";
        $message .= "Klik link berikut untuk mereset password Anda:\n";
        $message .= $resetUrl . "\n\n";
        $message .= "Link ini berlaku selama 60 menit.\n\n";
        $message .= "Jika Anda tidak meminta reset password, abaikan pesan ini.\n\n";
        $message .= "Terima kasih,\nSamsae Store";

        Log::info('Sending WhatsApp Password Reset', [
            'phone' => $phone,
            'reset_url' => $resetUrl,
            'message_length' => strlen($message)
        ]);

        return $this->sendMessage($phone, $message);
    }

    /**
     * Send password reset OTP via WhatsApp
     *
     * @param string $phone Phone number
     * @param string $otp 6-digit OTP code
     * @return array
     */
    public function sendPasswordResetOTP($phone, $otp)
    {
        $message = "Halo! Anda telah meminta reset password untuk akun Samsae Store.\n\n";
        $message .= "Kode OTP Anda adalah:\n";
        $message .= "*{$otp}*\n\n";
        $message .= "Kode ini berlaku selama 10 menit.\n\n";
        $message .= "Jangan bagikan kode ini kepada siapapun.\n\n";
        $message .= "Jika Anda tidak meminta reset password, abaikan pesan ini.\n\n";
        $message .= "Terima kasih,\nSamsae Store";

        Log::info('Sending WhatsApp Password Reset OTP', [
            'phone' => $phone,
            'otp' => $otp,
            'message_length' => strlen($message)
        ]);

        return $this->sendMessage($phone, $message);
    }
}
