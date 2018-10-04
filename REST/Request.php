<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 28/10/2016
 * Time: 11:51
 */

namespace NOUT\Bundle\NOUTOnlineBundle\REST;

class Request extends AbstractRestEntity
{
    private $form;

    private $beginWith;

    public function __construct($form, $beginWith = null, $actionParams = array())
    {
        $this->form = $form;
        $this->beginWith = $beginWith;

        parent::__construct($actionParams);
    }

    public function setAllowedOptions() {
        $this->allowed_options = array(
            'Readable',
            'Ghost',
            'DisplayValue',
            'LanguageCode'
        );
    }

    public function getRouteParams()
    {
        return $this->beginWith === null ?
            array(0 => $this->form) :
            array(0 => $this->form, 1 => $this->beginWith);
    }

    public function getRouteName() {
        return 'Request';
    }
}