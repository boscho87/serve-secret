<?php
/**
 * ServeSecret plugin for Craft CMS 3.x
 *
 * Serve password protected files
 *
 * @link      https://itscoding.ch
 * @copyright Copyright (c) 2018 Simon Müller
 */

namespace itscoding\servesecret\twigextensions;

use craft\elements\Asset;
use itscoding\servesecret\ServeSecret;

use Craft;

/**
 * Twig can be extended in many ways; you can add extra tags, filters, tests, operators,
 * global variables, and functions. You can even extend the parser itself with
 * node visitors.
 *
 * http://twig.sensiolabs.org/doc/advanced.html
 *
 * @author    Simon Müller
 * @package   ServeSecret
 * @since     1.0.0
 */
class ServeSecretTwigExtension extends \Twig_Extension
{
    // Public Methods
    // =========================================================================

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'ServeSecret';
    }

    /**
     * Returns an array of Twig filters, used in Twig templates via:
     *
     *      {{ 'something' | someFilter }}
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('secretFile', [$this, 'secretFile']),
        ];
    }

    /**
     * Returns an array of Twig functions, used in Twig templates via:
     *
     *      {% set this = someFunction('something') %}
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('secretFile', [$this, 'secretFile']),
        ];
    }

    /**
     * @param Asset $file
     * @return string
     */
    public function secretFile(Asset $file)
    {
        return ServeSecret::$plugin->security->getActionLink($file);
    }
}
