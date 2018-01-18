<?php
/**
 * ServeSecret plugin for Craft CMS 3.x
 *
 * Serve password protected files
 *
 * @link      https://itscoding.ch
 * @copyright Copyright (c) 2018 Simon Müller
 */

namespace itscoding\servesecret\controllers;

use craft\errors\FileException;
use itscoding\servesecret\ServeSecret;

use Craft;
use craft\web\Controller;

/**
 * FileServeController Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Simon Müller
 * @package   ServeSecret
 * @since     1.0.0
 */
class FileServeController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['get-secret-file'];

    // Public Methods
    // =========================================================================

    /**
     * @return \craft\web\Response|\yii\console\Response
     * @throws FileException
     * @throws \Exception
     */
    public function actionGetSecretFile()
    {
        $path = Craft::$app->request->get('file_path');
        $hash = Craft::$app->request->get('file_hash');
        $file = ServeSecret::$plugin->security->decryptPath($path);
        if (file_exists($file)) {
            if (ServeSecret::$plugin->security->getHash('file_hash') == $hash) {
                return Craft::$app->getResponse()->sendFile($file);
            }
            throw  new \Exception('you are not allowed to get the requested data');
        }
        throw new FileException('the file you looking for could not be found by servesecret plugin');
    }
}
