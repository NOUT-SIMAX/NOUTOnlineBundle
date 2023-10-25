<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 24/10/2023 15:30
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

class JSONResponseWS implements IResponseWS
{
    protected \stdClass $oJSON;

    protected ?array $aTabError=null;

    public function __construct($jsonEncoded)
    {
        $this->oJSON = json_decode($jsonEncoded, false, 512, JSON_BIGINT_AS_STRING);


        $result = $this->_getResult();
        if (property_exists($result, 'Fault')) {

            $this->aTabError = [];
            foreach($result->Fault->Detail as $obj)
            {
                $clError = new OnlineError($obj->Code->Name,
                                           $obj->Code->Numero,
                                           $obj->Code->Category,
                                           $obj->Message
                );

                if (property_exists($obj, 'Parameter')){

                    foreach ($obj->Parameter as $ndParam)
                    {
                        $clParam = new OnlineErrorParameter($ndParam->IDParam, $ndParam->TitleParam, $ndParam->TitleElem);
                        $clError->AddParameter($clParam);
                    }
                }
                $this->aTabError[]=$clError;
            }
        }
    }


    /**
     * retourne vrai si le retour est une erreur
     */
    public function bIsFault(): bool
    {
        return $this->sGetReturnType() == self::RETURNTYPE_ERROR;
    }
    /**
     * @return array|null, false si pas une erreur, un tableau d'erreur SIMAX si c'est une erreur
     */
    public function getTabError(): ?array
    {
        return $this->aTabError;
    }
    /**
     * @return int
     */
    public function getNumError(): int
    {
        if (!isset($this->aTabError))
        {
            return 0;
        }

        return $this->aTabError[0]->getErreur();
    }

    /**
     * @return int
     */
    public function getCatError(): int
    {
        if (!isset($this->aTabError))
        {
            return 0;
        }

        return $this->aTabError[0]->getCategorie();
    }
    /**
     * @return string
     */
    public function getMessError(): string
    {
        if (!isset($this->aTabError))
        {
            return '';
        }

        return $this->aTabError[0]->getMessage();
    }


    /**
     * @inheritDoc
     */
    public function sGetReturnType() : string
    {
        $header = $this->_getHeader();
        if (is_null($header) || !property_exists($header, 'ReturnType')){
            return '';
        }

        return $header->ReturnType;
    }

    /**
     * @inheritDoc
     */
    public function sGetActionContext() : string
    {
        $header = $this->_getHeader();
        if (is_null($header) || !property_exists($header, 'ActionContext')){
            return '';
        }

        return $header->ReturnType;
    }

    /**
     * @inheritDoc
     */
//    public function sGetContextToValidateOnClose() : string
//    {
//        $header = $this->_getHeader();
//        if (is_null($header) || !property_exists($header, 'ActionContext')){
//            return '';
//        }
//
//        return $header->ReturnType;
//    }

    /**
     * @inheritDoc
     */
    public function aGetActionContextToClose() : array
    {
        $header = $this->_getHeader();
        if (is_null($header) || !property_exists($header, 'ActionContext')){
            return [];
        }

        return $header->ActionContextToClose;
    }

    /**
     * @inheritDoc
     */
//    public function clGetMessageBox() : MessageBox
//    {
//        // TODO: Implement clGetMessageBox() method.
//
//    }

    /**
     * @inheritDoc
     */
//    public function getData() : string
//    {
//        // TODO: Implement getData() method.
//    }

    /**
     * @inheritDoc
     */
//    public function getFile() : ?NOUTFileInfo
//    {
//        // TODO: Implement getFile() method.
//    }

    /**
     * @inheritDoc
     */
    public function clGetAction() : CurrentAction
    {
        $header = $this->_getHeader();
        if (is_null($header) || !property_exists($header, 'Action')){
            return new CurrentAction();
        }

        return (new CurrentAction())->initFromJSON($header->Action);
    }

    /**
     * @inheritDoc
     */
//    public function clGetRequest() : ?CurrentRequest
//    {
//        // TODO: Implement clGetRequest() method.
//    }

    /**
     * @inheritDoc
     */
//    public function clGetTitle() : string
//    {
//        // TODO: Implement clGetTitle() method.
//    }

    /**
     * @inheritDoc
     */
//    public function sGetIDIHM() : ?string
//    {
//        // TODO: Implement sGetIDIHM() method.
//    }

    /**
     * @inheritDoc
     */
//    public function aGetIDIHMToClose() : ?array
//    {
//        // TODO: Implement aGetIDIHMToClose() method.
//    }

    /**
     * @inheritDoc
     */
    public function clGetConnectedUser() : ConnectedUser
    {
        // TODO: Implement clGetConnectedUser() method.
    }

    /**
     * @inheritDoc
     */
//    public function clGetForm() : Form
//    {
//        // TODO: Implement clGetForm() method.
//    }

    /**
     * @inheritDoc
     */
//    public function clGetElement() : ?Element
//    {
//        // TODO: Implement clGetElement() method.
//    }

    /**
     * @inheritDoc
     */
//    public function clGetCount() : Count
//    {
//        // TODO: Implement clGetCount() method.
//    }

    /**
     * @inheritDoc
     */
//    public function clGetFolderCount() : FolderCount
//    {
//        // TODO: Implement clGetFolderCount() method.
//    }

    /**
     * @inheritDoc
     */
//    public function GetTabPossibleDisplayMode() : ?array
//    {
//        // TODO: Implement GetTabPossibleDisplayMode() method.
//    }

    /**
     * @inheritDoc
     */
//    public function sGetDefaultGraphType() : ?string
//    {
//        // TODO: Implement sGetDefaultGraphType() method.
//    }

    /**
     * @inheritDoc
     */
//    public function sGetDefaultDisplayMode() : ?string
//    {
//        // TODO: Implement sGetDefaultDisplayMode() method.
//    }

    /**
     * @inheritDoc
     */
//    public function getValue() : ?string
//    {
//        // TODO: Implement getValue() method.
//    }

    /**
     * @inheritDoc
     */
//    public function getValidateError() : ?ValidateError
//    {
//        // TODO: Implement getValidateError() method.
//    }

    /**
     * @inheritDoc
     */
//    public function sGetTokenSession() : string
//    {
//        // TODO: Implement sGetTokenSession() method.
//    }

    /**
     * @inheritDoc
     */
//    public function nGetSessionLanguageCode() : int
//    {
//        // TODO: Implement nGetSessionLanguageCode() method.
//    }

    /**
     * @inheritDoc
     */
//    public function nGetNumberOfChart() : int
//    {
//        // TODO: Implement nGetNumberOfChart() method.
//    }

    /**
     * @inheritDoc
     */
    public function sGetReport() : string
    {
        $result = $this->_getResult();
        if (is_null($result) || !property_exists($result, 'Report')){
            return '';
        }

        if (is_string($result->Report))
        {
            return $result->Report;
        }

        if (is_array($result->Report)){
            if (empty($result->Report)){
                return '';
            }
            return $result->Report[0];
        }
        return '';
    }

    /**
     * @inheritDoc
     */
//    public function GetTabLanguages() : array
//    {
//        // TODO: Implement GetTabLanguages() method.
//    }

    protected function _getHeader() : ?\stdClass
    {
        if (property_exists($this->oJSON, 'header')){
            return $this->oJSON->header;
        }

        return null;
    }

    protected function _getResult() : ?\stdClass
    {

        if (property_exists($this->oJSON, 'result')){
            return $this->oJSON->result;
        }
        return null;
    }
}
