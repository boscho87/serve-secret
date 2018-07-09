<?php
/**
 * ServeSecret plugin for Craft CMS 3.x
 *
 * Serve password protected files
 *
 * @link      https://itscoding.ch
 * @copyright Copyright (c) 2018 Simon Müller
 */

namespace itscoding\servesecret\services;

use craft\elements\Asset;
use itscoding\servesecret\ServeSecret;

use Craft;
use craft\base\Component;

/**
 * Security Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Simon Müller
 * @package   ServeSecret
 * @since     1.0.0
 */
class Security extends Component
{

    /**
     * @var string
     */
    private $actionPath = '/actions/serve-secret/file-serve/get-secret-file';
    /**
     * @var string
     */
    private $encryptMethod = 'AES-256-CBC';
    /**
     * @var string
     */
    private $secretKey = '';
    /**
     * @var string
     */
    private $secretIv = 'generatedForSecurity';


    /**
     *
     */
    public function init()
    {
        $this->secretKey = $this->getHash('secret_key');
    }

    /**
     * @param $path
     * @return string
     */
    public function getActionLink(Asset $file, bool $inline)
    {
        $path = $this->createPath($file);

        return $this->actionPath . '?' . http_build_query([
                'file_path' => $this->encryptPath($path),
                'file_hash' => $this->getHash('file_hash'),
                'file_inline' => (int)$inline,
            ]);
    }

    /**
     * @param Asset $file
     * @return mixed
     */
    private function createPath(Asset $file)
    {
        $path = trim($file->getVolume()->path, '/')
            . '/' . trim($file->folderPath . '/')
            . '/' . trim($file->filename, '/');
        $path = str_replace('@webroot', $_SERVER['DOCUMENT_ROOT'], $path);
        return $path;
    }

    /**
     * @param $path
     * @return string
     */
    public function encryptPath($path)
    {
        $key = hash('sha256', $this->secretKey);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $hashedIv = substr(hash('sha256', $this->secretIv), 0, 16);
        $output = openssl_encrypt($path, $this->encryptMethod, $key, 0, $hashedIv);
        $output = base64_encode($output);
        return $output;
    }

    /**
     * @param $path
     * @return string
     */
    public function decryptPath($path)
    {
        $key = hash('sha256', $this->secretKey);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $hashedIv = substr(hash('sha256', $this->secretIv), 0, 16);
        $output = openssl_decrypt(base64_decode($path), $this->encryptMethod, $key, 0, $hashedIv);
        return $output;
    }

    /**
     * @param string $type
     * @return mixed|string
     */
    public function getHash(string $type)
    {
        if ($hash = Craft::$app->session->get($type)) {
            return $hash;
        }
        $hash = $this->createHash();
        Craft::$app->session->set($type, $hash);
        return $hash;
    }

    /**
     * @return string
     */
    private function createHash()
    {
        return base64_encode(bin2hex(random_bytes(16)));
    }
}
