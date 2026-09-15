<?php

namespace itscoding\servesecret\services;

use Craft;
use craft\base\Component;
use craft\elements\Asset;
use craft\fs\Local;
use itscoding\servesecret\ServeSecret;
use RuntimeException;

class Security extends Component
{
    private const ENCRYPT_METHOD = 'aes-256-gcm';
    private const TOKEN_PREFIX = 'v2.';
    private const IV_LENGTH = 12;
    private const TAG_LENGTH = 16;

    private string $actionPath = '/actions/serve-secret/file-serve/get-secret-file';
    private string $secretKey = '';

    public function init(): void
    {
        parent::init();
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
        $iv = random_bytes(self::IV_LENGTH);
        $tag = '';
        $ciphertext = openssl_encrypt(
            $this->createPath($asset),
            self::ENCRYPT_METHOD,
            $this->getKey(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($ciphertext === false || strlen($tag) !== self::TAG_LENGTH) {
            throw new RuntimeException('Could not encrypt secret file path.');
        }

        return self::TOKEN_PREFIX . $this->base64UrlEncode($iv . $tag . $ciphertext);
    }

    public function decryptPath(string $token): ?string
    {
        if (!str_starts_with($token, self::TOKEN_PREFIX)) {
            return null;
        }

        $payload = $this->base64UrlDecode(substr($token, strlen(self::TOKEN_PREFIX)));
        if ($payload === null || strlen($payload) <= self::IV_LENGTH + self::TAG_LENGTH) {
            return null;
        }

        $iv = substr($payload, 0, self::IV_LENGTH);
        $tag = substr($payload, self::IV_LENGTH, self::TAG_LENGTH);
        $ciphertext = substr($payload, self::IV_LENGTH + self::TAG_LENGTH);

        $decrypted = openssl_decrypt(
            $ciphertext,
            self::ENCRYPT_METHOD,
            $this->getKey(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        return is_string($decrypted) ? $decrypted : null;
    }

    public function resolveAllowedPath(string $path): ?string
    {
        $root = realpath(Craft::getAlias(ServeSecret::$secretFileAlias));
        $file = realpath($path);

        if ($root === false || $file === false || !is_file($file)) {
            return null;
        }

        $rootPrefix = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!str_starts_with($file, $rootPrefix)) {
            return null;
        }

        return $file;
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

    private function getKey(): string
    {
        return hash('sha256', $this->secretKey, true);
    }

    private function createHash(): string
    {
        return $this->base64UrlEncode(random_bytes(32));
    }

    private function createPath(Asset $file): string
    {
        /** @var Local $fileSystem */
        $fileSystem = $file->getVolume()->getFs();
        $rootPath = $fileSystem->getRootPath();

        if ($rootPath === null) {
            throw new RuntimeException('ServeSecret requires a local filesystem volume.');
        }

        $path = $rootPath . DIRECTORY_SEPARATOR . ltrim($file->getPath(), '/\\');
        $resolvedPath = $this->resolveAllowedPath($path);

        if ($resolvedPath === null) {
            throw new RuntimeException('Asset is outside the configured secret storage directory.');
        }

        return $resolvedPath;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): ?string
    {
        $padding = strlen($value) % 4;
        if ($padding !== 0) {
            $value .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode(strtr($value, '-_', '+/'), true);

        return $decoded === false ? null : $decoded;
    }
}
