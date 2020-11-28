<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class UserCreateOrUpdateController extends AbstractController
{
    private const URL = 'https://core.codepr.ru/api/v2/crm/user_create_or_update';

    public function post(Request $request)
    {
        $post = [
            'app_key' => '5240f691-60b0-4360-ac1f-836419c5408f',
            'phone' => '+79326100011',
            'email' => 'viktorovio@mail.ru',
            'name' => 'testphp',
            'surname' => '',
            'middlename' => '',
            'birthday' => '',
            'discount' => '',
            'bonus' => '',
            'balance' => ''
        ];

        $data_string = json_encode($post);

        $options = [
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => self::URL,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: '.strlen($data_string)
            ]
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        $responseKeys = ['success', 'card', 'card_number', 'card_track', 'card_url', 'form_url', 'user_hash'];

        foreach ($responseKeys as $responseKey) {
            if ( ! stristr($result, $responseKey)) {
                return $this->json('Invalid response error');
            }
        }

        $result .= json_encode([
            'Total time' => curl_getinfo($ch, CURLINFO_TOTAL_TIME)
        ]);

        $fileName = 'curlInfoData.txt';
        if ($result && is_writable($fileName)) {
            $fp = fopen($fileName, 'w');
            fwrite($fp, $result);
            fclose($fp);
        }

        return $this->json($result);
    }

    public function server()
    {
        $cmd = "ab -c 10 -n 10 -m POST " . self::URL;
        $result = shell_exec($cmd);
        $fileName = 'serverTestData.txt';
        if ($result && is_writable($fileName)) {
            $fp = fopen($fileName, 'w');
            fwrite($fp, $result);
            fclose($fp);
        }

        return $this->json($result);
    }
}