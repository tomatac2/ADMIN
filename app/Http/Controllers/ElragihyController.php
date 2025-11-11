<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;
use App\Model\Order;

class ElragihyController extends Controller
{
    private $tranportalId = 'BF6oc5l4NM40Gly';  
    private $password = 'K5$o3WwA3#!7rSd';  
    private $secretKey = '52898608747552898608747552898608'; 
    private $paymentTokenGenerationUrl = 'https://securepayments.alrajhibank.com.sa/pg/payment/tranportal.htm'; 
    private $iv = "PGKEYENCDECIVSPC" ; 
 
    //iframe
    public function arbIframe()
    {
        return view('arb-payments.iframe');
    }


    ////////END Flutter App
    function responsePayment(Request $request)
    {
    
      
       $res = $this->handleRes(['data' => $request->all()]);
        
    return view('arb-payments.response', ['data' => ["success" => $res["success"] , "msg" => $res["msg"] ] ]);
    }
  
    ///////////////
    function handleRes($data){
        try {
            $decryptedData = $this->aes_decrypt_hex(
                $data["data"]['trandata'],
                $this->secretKey,
                $this->iv
            );

            if (is_array($decryptedData) && isset($decryptedData[0])) {
                $response = $decryptedData[0];

                // حالة النجاح
                if (isset($response['result']) && strtoupper($response['result']) === 'CAPTURED') {
                    $success = true ;
                     $msg =
                     '
            <div style="
                max-width: 600px;
                margin: 50px auto;
                padding: 30px;
                background: #e6ffed;
                border-left: 6px solid #22c55e;
                border-radius: 12px;
                font-family: Arial, sans-serif;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                text-align: center;
            ">
                <h2 style="color: #15803d; margin-bottom: 10px;">✅ Payment Successful!</h2>
                <p style="font-size: 16px; color: #065f46;">Your transaction has been <strong>CAPTURED</strong>.</p>
                <p style="margin-top: 15px; color: #333;">
                    <strong>Amount:</strong> ' . htmlspecialchars($response['amt']) . '<br>
                    <strong>Card:</strong> ' . htmlspecialchars($response['card']) . '<br>
                    <strong>Transaction ID:</strong> ' . htmlspecialchars($response['transId'] ?? $response['trackId']) . '
                </p>
            </div>
            ';

                    // حالة الفشل
                } elseif (!empty($response['errorText'])) {
                    $success = false ;
                   $msg = '
            <div style="
                max-width: 600px;
                margin: 50px auto;
                padding: 30px;
                background: #fee2e2;
                border-left: 6px solid #dc2626;
                border-radius: 12px;
                font-family: Arial, sans-serif;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                text-align: center;
            ">
                <h2 style="color: #b91c1c; margin-bottom: 10px;">❌ Payment Failed</h2>
                <p style="font-size: 16px; color: #7f1d1d;">' . htmlspecialchars($response['errorText']) . '</p>
                <p style="margin-top: 15px; color: #333;">
                    <strong>Track ID:</strong> ' . htmlspecialchars($response['trackId']) . '<br>
                    <strong>Card:</strong> ' . htmlspecialchars($response['cardType'] ?? 'N/A') . '
                </p>
            </div>
            ';
                } else {
                  $msg = '
            <div style="
                max-width: 600px;
                margin: 50px auto;
                padding: 30px;
                background: #fef9c3;
                border-left: 6px solid #eab308;
                border-radius: 12px;
                font-family: Arial, sans-serif;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                text-align: center;
            ">
                <h2 style="color: #854d0e;">⚠️ Unknown Response</h2>
                <p style="color:#444;">The payment status could not be determined.</p>
            </div>
            ';
             $success = false ; 
                }
            } else {
                 $msg = "<h2 style='color:orange;text-align:center;'>⚠️ Invalid or empty response.</h2>";
                  $success = false;
            }
        } catch (Exception $e) {
            $success = false;
            $msg =  "<h2 style='color:red;text-align:center;'>Error during decryption:</h2> " .
                htmlspecialchars($e->getMessage());
        }
        return ["success" => $success , "msg" => $msg , "data"=> $response['udf1'] ?? null ];
    }
    ///////////////
        
  function aes_decrypt_hex(string $encryptedHex, string $key, string $iv) {
    try {
        // 1) hex -> binary
        $ciphertext = hex2bin($encryptedHex);
        if ($ciphertext === false) {
            throw new \Exception("Invalid HEX string.");
        }

        // 2) ensure key 32 bytes
        if (strlen($key) !== 32) {
            $key = hash('sha256', $key, true); // raw 32 bytes
        }

        // 3) iv must be 16 bytes
        if (strlen($iv) !== 16) {
            throw new \Exception("IV length must be 16 bytes.");
        }

        // 4) decrypt
        $plaintext = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($plaintext === false) {
            throw new \Exception("Decryption failed. Check key/iv/ciphertext.");
        }

        // 5) If looks URL-encoded, urldecode it
        //    Heuristic: contains %xx sequences or plus signs
        if (preg_match('/%[0-9A-Fa-f]{2}/', $plaintext) || strpos($plaintext, '+') !== false) {
            $plaintext_decoded = urldecode($plaintext);
        } else {
            $plaintext_decoded = $plaintext;
        }

        // 6) Try to json_decode — if valid JSON return array, else return raw string
        $json = json_decode($plaintext_decoded, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json;
        }

        // if not JSON, try rawurldecode as fallback (in case + was meaningful)
        $plaintext_alt = rawurldecode($plaintext);
        $jsonAlt = json_decode($plaintext_alt, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $jsonAlt;
        }

        // else return decoded string
        return $plaintext_decoded;

    } catch (\Exception $e) {
        // التعامل مع الخطأ — ارجع false أو ارمي الاستثناء حسب الاستخدام
        // هنا نرميه لإظهار السبب
        throw $e;
    }
}

    ///////////////
    function errorPayment(Request $request)
    {
        return view('arb-payments.err', ['data' => $request->all()]);
    }

    public function initiatePayment(Request $request)
    {


        if ($_GET) {

            // 1. تجيز بيانات الطب
            $orderId = 'ORDER_' . time(); // مال: رقم طلب فيد
            $amount = "1.00"; // مثا: مبلغ الدفع
            $responseUrl  = env('APP_URL') . '/arb-response'; // رابط العودة بعد الدفع
            $errorUrl =      env('APP_URL') . '/arb-error'; // ابط العودة في الة الخطأ
            $plainTxt = [
                [
                    "id" => $this->tranportalId,
                    "password" => $this->password,
                    "action" => "1",
                    "currencyCode" => "682",
                    'errorURL' => $errorUrl,
                    'responseURL' => $responseUrl,
                    "trackId" =>  "126290" . rand(10000, 99999) . "0963514" . rand(10000, 99999),  //62311134233463359187736  rand 23 numbers
                    'amt' => $amount,
                   // "udf1" => "123123", //order_id
                    //"udf2"=> "1", //order_id
                ]
            ];

         
          

            $plainTxt2json =    json_encode($plainTxt, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);


            // 2. تشفر البيانات (trandata)
            $encryptedData = $this->aes_encrypt_hex($plainTxt2json, $this->secretKey, $this->iv);

            // 3. إرال الطلب إلى API لإنشاء رمز الدف
            $response = $this->sendCurlRequest(
                $this->paymentTokenGenerationUrl,
                [
                    [
                        'id' => $this->tranportalId,
                        'trandata' => $encryptedData,
                        'errorURL' => $errorUrl,
                        'responseURL' => $responseUrl
                    ]
                ]
            );


            try {
                if (!isset($response[0]["result"])) {
                    echo ("Response structure invalid or empty.");
                }

                $res = $response[0]["result"];
                if (preg_match('/^(\d+):https/', $res, $matches)) {
                    $paymentID =  $matches[1]; // 600202529905904248
                }
                $paymentPageUrl = "https://securepayments.neoleap.com.sa/pg/paymentpage.htm?PaymentID=" . $paymentID;
                return redirect()->away($paymentPageUrl);
                // استخدم $res هنا عادي
            } catch (\Exception $e) {
                // سجل الخطأ أو تعامل معاه
                error_log("Error getting result: " . $e->getMessage());
                $res = null; // أو أي قيمة افتراضية تحب تستخدمها
            }
        } else {
            return view('arb-payments.initiate');
        }
    }
    private function sendCurlRequest($url, $data)
    {
      
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo new \Exception('cURL Error: ' . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    private function aes_encrypt_hex($plaintext, $key, $iv)
    {
        // اتشفير باستخدام AES-256-CBC
        $ciphertext = \openssl_encrypt($plaintext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        // تحويل لناتج لى HEX
        return strtoupper(bin2hex($ciphertext));
    }
}
