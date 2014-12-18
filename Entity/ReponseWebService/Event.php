<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/09/14
 * Time: 17:43
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

class Event
{
	/*
<xs:attribute xs:name="simax:uid" xs:use="required" simax:typeElement="xs:string"/>
<xs:attribute xs:name="simax:startTime" xs:use="required" simax:typeElement="xs:datetime"/>
<xs:attribute xs:name="simax:endTime" simax:typeElement="xs:datetime"/>
<xs:attribute xs:name="simax:summary" xs:use="required" simax:typeElement="xs:string"/>
<xs:attribute xs:name="simax:description" simax:typeElement="xs:string"/>
<xs:attribute xs:name="simax:resource" xs:use="required" simax:typeElement="xs:string"/>
<xs:attribute xs:name="simax:typeOfEvent" xs:use="required" simax:typeElement="xs:string"/>
<xs:attribute xs:name="simax:rrules" simax:typeElement="xs:string"/>
	 */
	public $m_sUID;
	public $m_sStartTime;
	public $m_sEndTime;
	public $m_sSummary;
	public $m_sDescription;
	public $m_nIDResource;
	public $m_nTypeOfEvent;
	public $m_sColorRGB;
	public $m_sRrules;

	public function __construct()
	{
		$this->m_sUID         = '';
		$this->m_sStartTime   = '';
		$this->m_sEndTime     = '';
		$this->m_sSummary     = '';
		$this->m_sDescription = '';
		$this->m_nIDResource  = '';
		$this->m_nTypeOfEvent = '';
		$this->m_sColorRGB    = '';
		$this->m_sRrules      = '';
	}
}
