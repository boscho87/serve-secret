<?php

namespace itscoding\servesecret\controllers;

use craft\web\Controller;
use itscoding\servesecret\ServeSecret;
use Craft;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\UnsupportedMediaTypeHttpException;

class FileServeController extends Controller
{
    protected int|bool|array $allowAnonymous = ['get-secret-file'];


    public function actionGetSecretFile(): Response
    {
        $path = Craft::$app->request->get('file_path');
        $hash = Craft::$app->request->get('file_hash');
        $inline = Craft::$app->request->get('file_inline');

        $file = ServeSecret::$plugin->security->decryptPath($path);

        if (file_exists($file)) {
            if (ServeSecret::$plugin->security->getHash('file_hash') == $hash) {
                return Craft::$app->getResponse()->sendFile($file, null, ['inline' => $inline]);
            }
            $message = Craft::t('not.allowed.to.get.requested.data');
            throw  new UnauthorizedHttpException($message);
        }
        $message = Craft::t('could.not.find.file.by.servesecret.plugin');
        throw new UnsupportedMediaTypeHttpException($message);
    }

    public function actionGetSecretFileForCp(): Response
    {
        $path = Craft::$app->request->getUrl();
        if (file_exists($path)) {
            return Craft::$app->getResponse()->sendFile($path, null, ['inline' => true]);
        }
        $message = Craft::t('could.not.find.file.by.servesecret.plugin');
        throw new UnsupportedMediaTypeHttpException($message);
    }
}
