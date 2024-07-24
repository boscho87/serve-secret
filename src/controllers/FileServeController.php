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
            throw  new UnauthorizedHttpException('you are not allowed to get the requested data');
        }
        throw new UnsupportedMediaTypeHttpException(
            'the file you looking for could not be found by servesecret plugin'
        );
    }

    public function actionGetSecretFileForCp(): Response
    {
        $path = Craft::$app->request->getUrl();
        if (file_exists($path)) {
            return Craft::$app->getResponse()->sendFile($path,null, ['inline' => true]);
        }
        throw new UnsupportedMediaTypeHttpException(
            'the file you looking for could not be found by servesecret plugin'
        );
    }
}

