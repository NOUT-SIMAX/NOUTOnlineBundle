<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 26/06/2023 18:15
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ActionResult;

class NOUTClientConfiguration extends NOUTClientBase
{
    /**
     * @return mixed
     * @throws \Exception
     */
    public function oGetConfigurationDropdownParams(string $idcontext) : ActionResult
    {
        $clIdentification = $this->_clGetIdentificationREST($idcontext, true);

        $httpresponse = $this->m_clRESTProxy->oGetConfigurationDropdownParams($clIdentification);

        return $this->_oGetJSONActionResultFromHTTPResponse($httpresponse);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function oGetConfigurationDropdownColumns(string $idcontext) : ActionResult
    {
        $clIdentification = $this->_clGetIdentificationREST($idcontext, true);

        $httpresponse = $this->m_clRESTProxy->oGetConfigurationDropdownColumns($clIdentification);

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
