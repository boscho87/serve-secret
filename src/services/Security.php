<?php

namespace itscoding\servesecret\services;

use craft\elements\Asset;
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
        return $this->actionPath . '?' . http_build_query([
                'file_path' => $this->createEncryptedHash($file),
                'file_hash' => $this->getHash('file_hash'),
                'file_inline' => (int)$inline,
            ]);
    }


    public function createEncryptedHash(Asset $asset): string
    {
        return openssl_encrypt(
            $this->createPath($asset),
            $this->encryptMethod,
            $this->getKey(),
            0,
            $this->getIv()
        );
    }

    public function decryptPath(string $path): string
    {
        return openssl_decrypt(
            $path,
            $this->encryptMethod,
            $this->getKey(),
            0,
            $this->getIv()
        );
    }

    private function getIv(): string
    {
        return substr(hash('sha256', $this->secretIv), 0, 16);
    }

    private function getKey(): string
    {
        return hash('sha256', $this->secretKey);
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

    private function createPath(Asset $file): string
    {
        return $file->getFs()->getRootUrl() . $file->getPath();
    }
}
