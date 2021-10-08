<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity;


use NOUT\Bundle\NOUTOnlineBundle\Service\NOUTClient;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SpecialParamListType;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\WSDLEntityDefinition;

class ParametersManagement
{
    /**
     * @param array $TabParamRequest
     * @return array
     */
    public static function s_setDefaultParamsValues(array $TabParamRequest) : array
    {
        if(!isset($TabParamRequest[NOUTClient::PARAM_SPECIALPARAMLIST])) {
            $TabParamRequest[NOUTClient::PARAM_SPECIALPARAMLIST] = new SpecialParamListType();
            $TabParamRequest[NOUTClient::PARAM_SPECIALPARAMLIST]->initFirstLength();
        }
        else {
            if(!isset($TabParamRequest[NOUTClient::PARAM_SPECIALPARAMLIST]['First'])) {
                $TabParamRequest[NOUTClient::PARAM_SPECIALPARAMLIST]['First'] = 0;
            }
            if(!isset($TabParamRequest[NOUTClient::PARAM_SPECIALPARAMLIST]['Length'])) {
                $TabParamRequest[NOUTClient::PARAM_SPECIALPARAMLIST]['Length'] = 50;
            }
        }
        return $TabParamRequest;
    }

    /**
     * @param array $paramXML
     * @return string
     */
    public static function s_sStringifyParamXML(array $paramXML) : string
    {
        $stringValue = '';
        foreach($paramXML as $paramKey => $paramValue) {
            if($paramKey === '_') {
                return self::s_sStringifyParamXML($paramValue);
            }
            if(is_numeric($paramKey)) {
                $paramKey = "id_$paramKey";
            }
            else {
                $paramKey = preg_replace('/\s+/', '_', $paramKey);
            }
            if(!is_array($paramValue)) {
                $stringValue .= "<$paramKey>".htmlspecialchars($paramValue)."</$paramKey>";
            }
            else {
                $stringValue .= "<$paramKey";
                foreach($paramValue as $paramAttrKey => $paramAttrValue) {
                    if($paramAttrKey !== '_') {
                        $stringValue .= " $paramAttrKey=$paramValue";
                    }
                }
                $stringValue .=">";
                if(isset($paramValue['_'])) {
                    $stringValue .= $paramValue["_"];
                }
                $stringValue .= "</$paramKey>";
            }
        }
        return $stringValue;
    }

    /**
     * @param array                $params
     * @param WSDLEntityDefinition $entityDefinition
     * @return array
     */
    public static function s_normalizeParameters(array $params, WSDLEntityDefinition $entityDefinition) : array
    {
        $normalizedParameters = array();
        foreach($params as $key => $value) {
            if($key === '_') {
                if(is_array($value)) {
                    $normalizedParameters = self::s_normalizeParameters($value, $entityDefinition);
                }
                else {
                    if(count($entityDefinition->attributes) > 0) {
                        $normalizedParameters['_'] = $value;
                    }
                }
            }
            else {
                $attrName = self::_s_GetExecuteFromParams_getAttribute($key, $entityDefinition);
                if($attrName === null)
                    continue;
                $type = self::_s_GetExecuteFromParams_getType($attrName, $entityDefinition);
                switch($type) {
                    case 'attribute':
                        $normalizedParameters[$attrName] = $value;
                        break;
                    case 'rawValue':
                        if(count($entityDefinition->attributes) > 0) {
                            if(is_string($value)) {
                                $normalizedParameters['_'][$attrName] = $value;
                            }
                            else {
                                $normalizedParameters['_'][$attrName] = $value['_'];
                            }
                        }
                        else {
                            if(is_string($value)) {
                                $normalizedParameters[$attrName] = $value;
                            }
                            else {
                                $normalizedParameters[$attrName] = $value['_'];
                            }
                        }
                        break;
                    default:
                        if($type instanceof WSDLEntityDefinition) {
                            if(count($entityDefinition->attributes) > 0) {
                                $normalizedParameters['_'][$attrName] = self::s_normalizeParameters($value, $type);
                            }
                            else {
                                $normalizedParameters[$attrName] = self::s_normalizeParameters($value, $type);
                            }
                        }
                        else {
                            throw new \RuntimeException('Unable to handle type');
                        }
                }
            }
        }
        return $normalizedParameters;
    }

    /**
     * @param                      $key
     * @param WSDLEntityDefinition $entityDefinition
     * @return int|mixed|string|null
     */
    protected static function _s_GetExecuteFromParams_getAttribute($key, WSDLEntityDefinition $entityDefinition)
    {
        foreach($entityDefinition->attributes as $attribute) {
            if(strtolower($key) === strtolower($attribute)) {
                return $attribute;
            }
        }
        foreach($entityDefinition->valueType as $typeKey => $typeValue) {
            if(strtolower($key) === strtolower($typeKey)) {
                return $typeKey;
            }
        }
        return null;
    }

    /**
     * @param                      $attr
     * @param WSDLEntityDefinition $entityDefinition
     * @return WSDLEntityDefinition|string
     */
    protected static function _s_GetExecuteFromParams_getType($attr, WSDLEntityDefinition $entityDefinition)
    {
        if(in_array($attr, $entityDefinition->attributes)) {
            return 'attribute';
        }
        elseif(isset($entityDefinition->valueType[$attr])) {
            if(is_string($entityDefinition->valueType[$attr])) {
                return 'rawValue';
            }
            elseif($entityDefinition->valueType[$attr] instanceof WSDLEntityDefinition) {
                return $entityDefinition->valueType[$attr];
            }
        }
        throw new \RuntimeException('Unable to handle param');
    }

    /**
     * @param string     $sIDForm
     * @param array      $aTabColumnsValues
     * @param array|null $aFilesToSend
     * @return string
     */
    public static function s_sStringifyUpdateData(string $sIDForm, array $aTabColumnsValues, array $aFilesToSend = null) : string
    {
        $sUpdateData = "<xml><id_$sIDForm>\n";
        $sUpdateData.= self::s_sStringifyXMLColonne($aTabColumnsValues, $aFilesToSend);
        $sUpdateData.= "\n</id_$sIDForm></xml>";

        return $sUpdateData;
    }

    /**
     * @param null|array $aFilesToSend
     * @param array      $aTabColumnsValues
     * @return string
     */
    public static function s_sStringifyXMLColonne(array $aTabColumnsValues, array $aFilesToSend = null): string
    {
        $sXML = '';

        foreach($aTabColumnsValues as $sIDColonne=>$sValue)
        {
            if(!is_null($aFilesToSend) && array_key_exists($sIDColonne, $aFilesToSend)) // La colonne est un fichier et a été modifiée
            {
                $sXML.= self::_s_sGetFileXML($sIDColonne, $aFilesToSend[$sIDColonne])."\n";
            }
            else
            {
                if(is_array($sValue))
                {
                    $sValue=implode('|', array_values($sValue));
                }
                //on preprend par id_ si nécessaire
                if (strncmp('id_', $sIDColonne, 3) !== 0){
                    $sIDColonne='id_'.$sIDColonne;
                }

                $sXML.="<$sIDColonne>".htmlspecialchars($sValue)."</$sIDColonne>\n";
            }
        }

        return $sXML;
    }


    /**
     * @param $sIDColonne
     * @param NOUTFileInfo|null $oFile
     * @return string
     */
    protected static function _s_sGetFileXML($sIDColonne, NOUTFileInfo $oFile=null): string
    {
        // Structure attendue des données XML d'un fichier
        /*
            <id_47723350017105 simax:ref="14673000757953052016">    // Identifiant 1 = idColonne et 2 = id unique au choix, retrouvé dans ref
                lst_oper_L33-1 (1) (1) (1).csv
            </id_47723350017105>

            <simax:Data
            simax:ref = "14673000757953052016"
            simax:title = "lst_oper_L33-1 (1) (1) (1).csv"
            simax:encoding = "base64"
            simax:size = "215465"
            simax:filename = "lst_oper_L33-1 (1) (1) (1).csv"
            simax:typemime = "text/plain" >
                fileContentHere
            </simax:Data>
        */

        if($oFile instanceof NOUTFileInfo)
        {
            $fileUniqueId  = uniqid();

            // Headers
            $sFileXml = "<id_$sIDColonne simax:ref=\"$fileUniqueId\">";
            $sFileXml .= $oFile->filename;
            $sFileXml .= "</id_$sIDColonne>\n";

            // Paramètres
            $sFileXml .= '<simax:Data ';
            $sFileXml .= 'simax:ref="' . $fileUniqueId . '" ';
            $sFileXml .= 'simax:title="' . $oFile->filename . '" ';
            $sFileXml .= 'simax:encoding="' . 'base64' . '" ';
            $sFileXml .= 'simax:size="' . $oFile->size . '" ';
            $sFileXml .= 'simax:filename="' . $oFile->filename . '" ';
            $sFileXml .= 'simax:typemime="' . $oFile->mimetype . '">';

            // Content
            $sFileXml .= base64_encode($oFile->content);

            // Fin Paramètres
            $sFileXml .= "</simax:Data>\n";
        }
        else // Champ vide
        {
            $sFileXml = "<id_$sIDColonne></id_$sIDColonne>\n";
        }

        return $sFileXml;
    }

}