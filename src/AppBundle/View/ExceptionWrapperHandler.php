<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 20/05/16
 * Time: 12:31 PM
 */

namespace AppBundle\View;

use FOS\RestBundle\View\ExceptionWrapperHandlerInterface;

class ExceptionWrapperHandler implements ExceptionWrapperHandlerInterface
{

    public function wrap($data)
    {
        return array(
            'code' => $data['status_code'],
            'msg' => $data['message']
        );
    }
}
