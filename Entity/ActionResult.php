<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 11/12/14
 * Time: 14:48
 */

namespace NOUT\Bundle\ContextsBundle\Entity;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\Count;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\CurrentAction;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ValidateError;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

class ActionResult
{
	/**
	 * @var string
	 */
	public $ReturnType;

	/**
	 * @var mixed
	 */
	private $m_Data;

	/**
	 * @var string
	 */
	private $m_sIDContexte;

    /**
     * @var \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\CurrentAction
     */
	private $m_clAction;

	/**
	 * @var \NOUT\Bundle\ContextsBundle\Entity\ActionResultCache
	 */
	private $m_clCache;

	/**
	 * @var ValidateError
	 */
	private $m_clValidateError;

    /**
     * @var Count
     */
    private $m_clCount;

	/**
     * @var Element
     */
    private $m_oElement;





	/**
	 * @param string $sReturnType
	 */
	public function __construct(XMLResponseWS $clReponseXML = null)
	{
		if (isset($clReponseXML))
		{
			$this->ReturnType    = $clReponseXML->sGetReturnType();
			$this->m_sIDContexte = $clReponseXML->sGetActionContext();
			$this->m_clAction    = $clReponseXML->clGetAction();
		}
		else
		{
			$this->ReturnType    = null;
			$this->m_sIDContexte = '';
            $this->m_clAction    = null;
		}

		$this->m_Data               = null;
		$this->m_clCache            = new ActionResultCache();
		$this->m_clValidateError    = null;
        $this->m_sTypeAction        = Langage::eTYPEACTION_Unknown;
        $this->m_oElement           = new \stdClass();

        $this->m_oElement->id = '';
        $this->m_oElement->title = '';

	}

	/**
	 * @param string $sReturnType
	 * @return $this
	 */
	public function setReturnType($sReturnType)
	{
		$this->ReturnType = $sReturnType;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getReturnType()
	{
		return $this->ReturnType;
	}

	/**
	 * @param $data
	 * @return $this
	 */
	public function setData($data)
	{
		$this->m_Data = $data;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->m_Data;
	}

	/**
	 * @param $element
	 * @return $this
	 */
	public function setElement($element)
	{
		$this->m_oElement = $element;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getElement()
	{
		return $this->m_oElement;
	}


	/**
	 * @param ValidateError $clValidateError
	 * @return $this
	 */
	public function setValidateError(ValidateError $clValidateError=null)
	{
		$this->m_clValidateError = $clValidateError;
		return $this;
	}

	/**
	 * @return null|ValidateError
	 */
	public function getValidateError()
	{
		return $this->m_clValidateError;
	}

	/**
	 * @return string
	 */
	public function getIDContexte()
	{
		return $this->m_sIDContexte;
	}

    /**
     * @return \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\CurrentAction|null
     */
	public function getAction()
	{
		return $this->m_clAction;
	}


	/**
	 * @param $eTypeCache
	 * @return $this
	 */
	public function setTypeCache($eTypeCache)
	{
		$this->m_clCache->setTypeCache($eTypeCache);

		return $this;
	}

	/**
	 * @param \DateTime $clExpires
	 * @return $this
	 */
	public function setExpires(\DateTime $clExpires)
	{
		$this->m_clCache->setExpires($clExpires);

		return $this;
	}

	/**
	 * @param int $nMaxAge
	 * @return $this
	 */
	public function setMaxAge($nMaxAge)
	{
		$this->m_clCache->setMaxAge($nMaxAge);

		return $this;
	}

	/**
	 * @param int $nSharedMaxAge
	 * @return $this
	 */
	public function setSharedMaxAge($nSharedMaxAge)
	{
		$this->m_clCache->setSharedMaxAge($nSharedMaxAge);

		return $this;
	}

	/**
	 * @param string $sETAG
	 * @return $this
	 */
	public function setETAG($sETAG)
	{
		$this->m_clCache->setETAG($sETAG);

		return $this;
	}

	/**
	 * @param \DateTime $lastModified
	 * @return $this
	 */
	public function setLastModified(\DateTime $lastModified)
	{
		$this->m_clCache->setLastModified($lastModified);

		return $this;
	}

	/**
	 * @return ActionResultCache
	 */
	public function getCache()
	{
		return $this->m_clCache;
	}

    /**
     * @return Count
     */
    public function getCount()
    {
        return $this->m_clCount;
    }

    /**
     * @param Count $m_clCount
     */
    public function setCount(Count $m_clCount)
    {
        $this->m_clCount = $m_clCount;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeAction()
    {
        return $this->getAction()->getTypeAction();
    }
}
