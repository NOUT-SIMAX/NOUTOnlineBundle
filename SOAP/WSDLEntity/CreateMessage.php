<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity;

/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 22/07/14
 * Time: 17:34
 */

//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------
class CreateMessage
{
	public $CreateType; // CreateTypeEnum
	public $IDMessage; // string
	public $IDAnswerType; // string

    const CREATE_TYPE_EMPTY = 'Empty';
    const CREATE_TYPE_FORWARD = 'Forward';
    const CREATE_TYPE_ANSWER = 'Answer';
    const CREATE_TYPE_ANSWER_ALL = 'Answer All';
    const CREATE_TYPE_ANSWER_TYPE = 'Answer Type';
}
//***
