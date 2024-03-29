<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 11/12/14
 * Time: 14:48
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ConnectedUser;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\Count;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\CurrentAction;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\CurrentRequest;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\Element;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\FolderCount;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ValidateError;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

class ActionResult
{
	/**
	 * @var string|null
	 */
	public $ReturnType=null;

    /**
     * @var string|null
     */
    public $IDIHM=null;

    /** @var array  */
    public $IDIHMToClose=[];

	/**
	 * @var mixed
	 */
	private $m_Data=null;

	/**
     * Les données en plus (par exemple liste utilisateurs)
	 * @var mixed
	 */
	private $m_ExtraData=null;

	/**
	 * @var string
	 */
	private $m_sIDContexte='';


    /**
     * @var string
     */
    private $m_sIDContexteToValidateOnClose='';

    /**
     * @var array
     */
    private $m_aContexteToClose=array();

    /** @var CurrentAction */
	private $m_clAction=null;

    /** @var CurrentRequest */
    private $m_clRequest=null;

	/**
	 * @var ActionResultCache
	 */
	private $m_clCache;

	/**
	 * @var ValidateError
	 */
	private $m_clValidateError=null;

    /**
     * @var Count
     */
    private $m_clCount = null;

    /**
     * @var FolderCount
     */
    private $m_clFolderCount = null;

	/**
     * @var Element
     */
    private $m_oElement;

    /**
     * @var ConnectedUser
     */
    private $m_clConnectedUser=null;

    /**
     * ActionResult constructor.
     * @param XMLResponseWS|null $clReponseXML
     */
	public function __construct(XMLResponseWS $clReponseXML = null)
	{
		if (isset($clReponseXML))
		{
			$this->ReturnType    = $clReponseXML->sGetReturnType();
            $this->IDIHM         = $clReponseXML->sGetIDIHM();
			$this->m_sIDContexte = $clReponseXML->sGetActionContext();
			$this->m_clAction    = $clReponseXML->clGetAction();
			$this->m_clRequest   = $clReponseXML->clGetRequest();
            $this->m_sIDContexteToValidateOnClose = $clReponseXML->sGetContextToValidateOnClose();
            $this->m_aContexteToClose = $clReponseXML->aGetActionContextToClose();
            $this->m_clConnectedUser = $clReponseXML->clGetConnectedUser();
            $this->IDIHMToClose = $clReponseXML->aGetIDIHMToClose();
		}

		$this->m_clCache  = new ActionResultCache();
        $this->m_oElement = new Element('', '');
	}

	/**
	 * @param string $sReturnType
	 * @return $this
	 */
	public function setReturnType(string $sReturnType): ActionResult
    {
		$this->ReturnType = $sReturnType;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getReturnType(): ?string
    {
		return $this->ReturnType;
	}

	/**
	 * @param $data
	 * @return $this
	 */
	public function setData($data): ActionResult
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
     * @param $extraData
     * @return $this
     */
    public function setExtraData($extraData): ActionResult
    {
        $this->m_ExtraData = $extraData;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtraData()
    {
        return $this->m_ExtraData;
    }


	/**
	 * @param $element
	 * @return $this
	 */
	public function setElement(Element $element): ActionResult
    {
		$this->m_oElement = $element;
		return $this;
	}

	/**
	 * @return Element
	 */
	public function getElement() : Element
	{
		return $this->m_oElement;
	}


	/**
	 * @param ValidateError|null $clValidateError
	 * @return $this
	 */
	public function setValidateError(ValidateError $clValidateError=null): ActionResult
    {
		$this->m_clValidateError = $clValidateError;
		return $this;
	}

	/**
	 * @return null|ValidateError
	 */
	public function getValidateError(): ?ValidateError
    {
		return $this->m_clValidateError;
	}

	/**
	 * @return string
	 */
	public function getIDContexte(): string
    {
		return $this->m_sIDContexte;
	}

    /**
     * @return string
     */
    public function getIDContexteToValidateOnClose(): string
    {
        return $this->m_sIDContexteToValidateOnClose;
    }

    /**
     * @return array
     */
    public function getContexteToClose(): array
    {
        return $this->m_aContexteToClose;
    }


    /**
     * @return CurrentAction|null
     */
	public function getAction(): ?CurrentAction
    {
		return $this->m_clAction;
	}

    /**
     * @return CurrentRequest|null
     */
    public function getRequest(): ?CurrentRequest
    {
        return $this->m_clRequest;
    }


	/**
	 * @param $eTypeCache
	 * @return $this
	 */
	public function setTypeCache($eTypeCache): ActionResult
    {
		$this->m_clCache->setTypeCache($eTypeCache);

		return $this;
	}

	/**
	 * @param \DateTime $clExpires
	 * @return $this
	 */
	public function setExpires(\DateTime $clExpires): ActionResult
    {
		$this->m_clCache->setExpires($clExpires);

		return $this;
	}

	/**
	 * @param int $nMaxAge
	 * @return $this
	 */
	public function setMaxAge(int $nMaxAge): ActionResult
    {
		$this->m_clCache->setMaxAge($nMaxAge);

		return $this;
	}

	/**
	 * @param int $nSharedMaxAge
	 * @return $this
	 */
	public function setSharedMaxAge(int $nSharedMaxAge): ActionResult
    {
		$this->m_clCache->setSharedMaxAge($nSharedMaxAge);

		return $this;
	}

	/**
	 * @param string $sETAG
	 * @return $this
	 */
	public function setETAG(string $sETAG): ActionResult
    {
		$this->m_clCache->setETAG($sETAG);

		return $this;
	}

	/**
	 * @param \DateTime $lastModified
	 * @return $this
	 */
	public function setLastModified(\DateTime $lastModified): ActionResult
    {
		$this->m_clCache->setLastModified($lastModified);

		return $this;
	}

	/**
	 * @return ActionResultCache
	 */
	public function getCache() : ActionResultCache
	{
		return $this->m_clCache;
	}

    /**
     * @return Count
     */
    public function getCount(): ?Count
    {
        return $this->m_clCount;
    }

    /**
     * @param Count $clCount
     * @return $this
     */
    public function setCount(Count $clCount): ActionResult
    {
        $this->m_clCount = $clCount;
        return $this;
    }

    /**
     * @return FolderCount
     */
    public function getFolderCount(): ?FolderCount
    {
        return $this->m_clFolderCount;
    }

    /**
     * @param FolderCount $clCount
     * @return $this
     */
    public function setFolderCount(FolderCount $clCount): ActionResult
    {
        $this->m_clFolderCount = $clCount;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeAction(): string
    {
        return $this->getAction()->getIDTypeAction();
    }


    /**
     * @return string
     */
    public function getIDAction(): string
    {
        return $this->getAction()->getID();
    }


    /**
     * @return ConnectedUser|null
     */
    public function getConnectedUser(): ?ConnectedUser
    {
        return $this->m_clConnectedUser;
    }

}
