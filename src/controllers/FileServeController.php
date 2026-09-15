<?php

namespace itscoding\servesecret\controllers;

use Craft;
use craft\web\Controller;
use itscoding\servesecret\ServeSecret;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class FileServeController extends Controller
{
    protected int|bool|array $allowAnonymous = ['get-secret-file'];

    public function actionGetSecretFile(): Response
    {
        $request = Craft::$app->request;
        $pathToken = (string)$request->get('file_path', '');
        $hash = (string)$request->get('file_hash', '');
        $inline = filter_var($request->get('file_inline', false), FILTER_VALIDATE_BOOL);

        $expectedHash = ServeSecret::$plugin->security->getHash('file_hash');
        if ($hash === '' || !hash_equals($expectedHash, $hash)) {
            throw new UnauthorizedHttpException(Craft::t('serve-secret', 'Not allowed to get requested data.'));
        }

        $decryptedPath = ServeSecret::$plugin->security->decryptPath($pathToken);
        $file = $decryptedPath !== null
            ? ServeSecret::$plugin->security->resolveAllowedPath($decryptedPath)
            : null;

        if ($file === null) {
            throw new NotFoundHttpException(Craft::t('serve-secret', 'Could not find requested file.'));
        }

        return Craft::$app->getResponse()->sendFile($file, null, ['inline' => $inline]);
    }

    public function actionGetSecretFileForCp(): Response
    {
        $requestPath = rawurldecode(parse_url(Craft::$app->request->getUrl(), PHP_URL_PATH) ?? '');
        $rootPath = Craft::getAlias(ServeSecret::$secretFileAlias);
        $candidate = rtrim($rootPath, '/\\') . DIRECTORY_SEPARATOR . ltrim($requestPath, '/\\');
        $file = ServeSecret::$plugin->security->resolveAllowedPath($candidate);

        if ($file === null) {
            throw new NotFoundHttpException(Craft::t('serve-secret', 'Could not find requested file.'));
        }

        return Craft::$app->getResponse()->sendFile($file, null, ['inline' => true]);
    }
}
