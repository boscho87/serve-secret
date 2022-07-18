<?php

namespace itscoding\servesecret;

use itscoding\servesecret\services\Security as SecurityService;
use itscoding\servesecret\twigextensions\ServeSecretTwigExtension;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;


class ServeSecret extends Plugin
{
    public static ServeSecret $plugin;
    public static string $secretFileAlias = '@secretStorage';

    public function init(): void
    {
        parent::init();
        self::$plugin = $this;
        Craft::$app->view->registerTwigExtension(new ServeSecretTwigExtension());


        $rootDir = Craft::getAlias('@root');
        $path = $rootDir . '/secretStorage';
        Craft::setAlias(self::$secretFileAlias, $path);

        $match = substr(Craft::$app->request->getUrl(), 0, strlen($path));

        if ($match === $path) {
            Event::on(
                UrlManager::class,
                UrlManager::EVENT_REGISTER_SITE_URL_RULES,
                function (RegisterUrlRulesEvent $event) {
                    $event->rules[Craft::$app->request->getUrl()] = 'serve-secret/file-serve/get-secret-file-for-cp';
                }
            );
        }


        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['secretFileTrigger'] = 'serve-secret/file-serve/get-secret-file';
            }
        );

        Craft::info(
            Craft::t(
                'serve-secret',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }
}
