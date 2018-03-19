<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 30/06/16
 * Time: 03:40 PM
 */

namespace AppBundle\Twig;

use AppBundle\EventListener\UrlListener;

class UrlExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{

    private $listener;
    
    public function __construct(UrlListener $listener)
    {
        $this->listener = $listener;
    }

    public function getGlobals()
    {
        return $this->listener->getVars();
    }
    
    public function getName()
    {
        return 'app_url_extension';
    }
}
