<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 26/06/2023 18:15
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ActionResult;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy;

class NOUTClientConfiguration extends NOUTClientBase
{
    /**
     * @param string $idcontext
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetConfigurationDropdownAction(string $idcontext) : ActionResult
    {
        $clIdentification = $this->_clGetIdentificationREST($idcontext, true);

        $httpresponse = $this->m_clRESTProxy->oGetConfigurationDropdownAction($clIdentification);

        return $this->_oGetJSONActionResultFromHTTPResponse($httpresponse);
    }

    /**
     * @param string $idcontext
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetConfigurationDropdownForm(string $idcontext) : ActionResult
    {
        $clIdentification = $this->_clGetIdentificationREST($idcontext, true);

        $httpresponse = $this->m_clRESTProxy->oGetConfigurationDropdownForm($clIdentification);

        return $this->_oGetJSONActionResultFromHTTPResponse($httpresponse);
    }

    /**
     * @param string $idconfig
     * @param string $idcontext
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetConfigurationDropdownColumn(string $idconfig, string $idcontext) : ActionResult
    {
        $clIdentification = $this->_clGetIdentificationREST($idcontext, true);

        $httpresponse = $this->m_clRESTProxy->oGetConfigurationDropdownColumn([OnlineServiceProxy::PARAM_IDCol], $clIdentification);

        return $this->_oGetJSONActionResultFromHTTPResponse($httpresponse);
    }

    /**
     * @param string $idcontext
     * @param string $idconfig
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetConfigurationDropdownParameter(string $idconfig, string $idcontext) : ActionResult
    {
        $clIdentification = $this->_clGetIdentificationREST($idcontext, true);

        $httpresponse = $this->m_clRESTProxy->oGetConfigurationDropdownParameter($clIdentification);

        return $this->_oGetJSONActionResultFromHTTPResponse($httpresponse);
    }



    /**
     * @return mixed
     * @throws \Exception
     */
    public function oApplyConfiguration() : ActionResult
    {
        $clIdentification = $this->_clGetIdentificationREST('', true);

        $httpresponse = $this->m_clRESTProxy->oApplyConfiguration($clIdentification);

        return $this->_oGetJSONActionResultFromHTTPResponse($httpresponse);
    }
}
