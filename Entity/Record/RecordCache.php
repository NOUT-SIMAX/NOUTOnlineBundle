<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 06/08/2015
 * Time: 17:53
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


class RecordCache
{

	/**
	 * @var array
	 * map qui contient l'association (IDTableau,IDEnreg) => Objet Record
	 */
	protected $m_MapIDTableauIDEnreg2Record;


	/**
	 * @var array
	 * map qui contient l'association (IDTableau,IDEnreg,Niveau) => Objet Record
	 */

	protected $m_MapXMLKey2Record;

	public function __construct()
	{
		$this->m_MapIDTableauIDEnreg2Record	= array();
		$this->m_MapXMLKey2Record			= array();
	}

    /**
     * @return array
     */
	public function getMapIDTableauIDEnreg2Record(): array
    {
	    return $this->m_MapIDTableauIDEnreg2Record;
    }

	/**
	 * @param Record $clRecord
     * @param $nNiv
	 */
	public function SetRecord($nNiv, Record $clRecord)
	{
		$sIDForm=$clRecord->getIDTableau();
		$sIDEnreg = $clRecord->getIDEnreg();

		$sKey2Record = $sIDForm.'/'.$sIDEnreg;

		if (!isset($this->m_MapIDTableauIDEnreg2Record[$sKey2Record])
			|| $clRecord->isBetterLevel($this->m_MapIDTableauIDEnreg2Record[$sKey2Record])
		)
		{
			$this->m_MapIDTableauIDEnreg2Record[$sKey2Record]=$clRecord;
		}

		$sKey2Record.='/'.$nNiv;
		if (!isset($this->m_MapXMLKey2Record[$sKey2Record]))
		{
			$this->m_MapXMLKey2Record[$sKey2Record]=$clRecord;
		}
	}



	/**
	 * @param $sIDForm
	 * @param $sIDEnreg
	 * @return null|Record
	 */
	public function getRecord($sIDForm, $sIDEnreg): ?Record
    {
		$sKey2Record = $sIDForm.'/'.$sIDEnreg;

		if (!isset($this->m_MapIDTableauIDEnreg2Record[$sKey2Record]))
		{
			return null;
		}

		return $this->m_MapIDTableauIDEnreg2Record[$sKey2Record];
	}

	/**
	 * @param $sIDForm
	 * @param $sIDEnreg
	 * @param $nNiv
	 * @return null|Record
	 */
	public function getRecordFromIdLevel($sIDForm, $sIDEnreg, $nNiv): ?Record
    {
		$sKey2Record = $sIDForm.'/'.$sIDEnreg.'/'.$nNiv;
		if (!isset($this->m_MapXMLKey2Record[$sKey2Record]))
		{
			return null;
		}
		return $this->m_MapXMLKey2Record[$sKey2Record];
	}

    /**
     * met à jour le cache
     * @param RecordCache $cacheSrc
     * @return $this
     */
    public function update(RecordCache $cacheSrc): RecordCache
    {
        foreach($cacheSrc->m_MapIDTableauIDEnreg2Record as $key=>$clRecord)
        {
            $this->m_MapIDTableauIDEnreg2Record[$key]=$clRecord;
        }

        foreach($cacheSrc->m_MapXMLKey2Record as $key=>$clRecord)
        {
            $this->m_MapXMLKey2Record[$key]=$clRecord;
        }
        return $this;
    }



}