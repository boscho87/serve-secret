<?php

namespace itscoding\servesecret\services;

use craft\elements\Asset;
use itscoding\servesecret\ServeSecret;

use Craft;
use craft\base\Component;

/**
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Simon Müller
 * @package   ServeSecret
 * @since     1.0.0
 */
class Security extends Component
{
    private string $actionPath = '/actions/serve-secret/file-serve/get-secret-file';

    private string $encryptMethod = 'AES-256-CBC';

    private string $secretKey = '';

    private string $secretIv = 'generatedForSecurity';


    public function init(): void
    {
        $this->secretKey = $this->getHash('secret_key');
    }


    public function getActionLink(Asset $file, bool $inline): string
    {
        $path = $this->createPath($file);

        return $this->actionPath . '?' . http_build_query([
                'file_path' => $this->encryptPath($path),
                'file_hash' => $this->getHash('file_hash'),
                'file_inline' => (int)$inline,
            ]);
    }

    private function createPath(Asset $file): string
    {
        $path = trim($file->getVolume()->path, '/')
            . '/' . trim($file->folderPath . '/')
            . '/' . trim($file->filename, '/');
        return str_replace('@webroot', $_SERVER['DOCUMENT_ROOT'], $path);
    }


    public function encryptPath($path): string
    {
        $key = hash('sha256', $this->secretKey);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $hashedIv = substr(hash('sha256', $this->secretIv), 0, 16);
        $output = openssl_encrypt($path, $this->encryptMethod, $key, 0, $hashedIv);
        $output = base64_encode($output);
        return $output;
    }

    public function decryptPath(string $path): string
    {
        $key = hash('sha256', $this->secretKey);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $hashedIv = substr(hash('sha256', $this->secretIv), 0, 16);
        return openssl_decrypt(base64_decode($path), $this->encryptMethod, $key, 0, $hashedIv);
    }

    public function getHash(string $type): string
    {
        if ($hash = Craft::$app->session->get($type)) {
            return $hash;
        }
        $hash = $this->createHash();
        Craft::$app->session->set($type, $hash);
        return $hash;
    }

    private function createHash(): string
    {
        return base64_encode(bin2hex(random_bytes(16)));
    }
}
