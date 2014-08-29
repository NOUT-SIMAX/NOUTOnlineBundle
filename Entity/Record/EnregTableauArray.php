<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 29/08/14
 * Time: 11:14
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


class EnregTableauArray implements \Iterator
{
	protected $m_Tab;

	private $_key;

	public function __construct()
	{
		$this->_key=0;
		$this->m_Tab = array();
	}

	public function rewind()
	{
		$this->_key = 0;
	}

	public function valid()
	{
		return array_key_exists($this->_key, $this->m_Tab);
	}

	public function key()
	{
		return $this->_key;
	}

	public function current()
	{
		return $this->m_Tab[$this->_key];
	}

	public function next()
	{
		++$this->_key;
	}

	public function GetTabIDEnreg($nIDTableau=null, $bMemeTaille=false)
	{
		$aRet = array();
		foreach($this->m_Tab as $clEnreg)
		{
			if ($clEnreg->m_nIDEnreg == $nIDTableau)
				$aRet[]=$clEnreg->m_nIDEnreg;
			else if ($bMemeTaille)
				$aRet[]=null;
		}

		return $aRet;
	}

	public function GetTabIDTableau()
	{
		$aRet = array();
		foreach($this->m_Tab as $clEnregTab)
			$aRet[]=$clEnregTab->m_nIDTableau;

		return $aRet;
	}

	public function bEstIDTableauUnique()
	{
		if (count($this->m_Tab)==0)
			return false;

		$nIDPrec=$this->m_Tab[0]->m_nIDTableau;
		foreach($this->m_Tab as $clEnregTab)
			if ($clEnregTab->m_nIDTableau != $nIDPrec)
				return false;

		return true;
	}

	public function nGetIDTableauEnreg($nIDEnreg)
	{
		foreach($this->m_Tab as $clEnregTab)
			if ($clEnregTab->m_nIDEnreg == $nIDEnreg)
				return $clEnregTab->m_nIDTableau;

		return null;
	}

	public function nGetIDTableau($nIndice)
	{
		if (isset($this->m_Tab[$nIndice]))
			return $this->m_Tab[$nIndice]->m_nIDTableau;

		return null;
	}

	public function nGetIDEnreg($nIndice)
	{
		if (isset($this->m_Tab[$nIndice]))
			return $this->m_Tab[$nIndice]->m_nIDEnreg;

		return null;
	}

	public function GetSize()
	{
		return count($this->m_Tab);
	}

	public function IsEmpty()
	{
		return count($this->m_Tab)==0;
	}

	public function RemoveAll()
	{
		$this->m_Tab=array();
		$this->rewind();
		return $this;
	}

	public function Add($nIDTableau, $nIDEnreg)
	{
		$this->m_Tab[]=new EnregTableau($nIDTableau, $nIDEnreg);
		return $this;
	}

	/**
	 * Ajoute si n'existe pas
	 * @param $nIDTableau
	 * @param $nIDEnreg
	 * @return $this
	 */
	public function AddNouveau($nIDTableau, $nIDEnreg)
	{
		foreach($this->m_Tab as $clEnregTab)
			if ($clEnregTab->m_nIDEnreg == $nIDEnreg)
				return $this;

		return $this->Add($nIDTableau, $nIDEnreg);
	}

	public function AddFromListeStr($nIDTableau, $sStr)
	{
		$TabIDEnreg = explode('|', $sStr);

		foreach($TabIDEnreg as $nIDEnreg)
			$this->Add($nIDTableau, $nIDEnreg);
	}

	public function Append($TabSrc, $nIDTableau=null)
	{
		if ($TabSrc instanceof EnregTableauArray)
			$this->m_Tab = array_merge($this->m_Tab, $TabSrc->m_Tab);
		else if (is_array($TabSrc))
		{
			foreach($TabSrc as $nIDEnreg)
				$this->Add($nIDTableau, $nIDEnreg);
		}

		return $this;
	}

	public function AppendNouveauIDAuto($TabSrc, $nIDTableau=null)
	{
		if ($TabSrc instanceof EnregTableauArray)
		{
			foreach($TabSrc as $clEnreg)
				$this->AddNouveau($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);
		}
		else if (is_array($TabSrc))
		{
			foreach($TabSrc as $nIDEnreg)
				$this->AddNouveau($nIDTableau, $nIDEnreg);
		}

		return $this;
	}

	/**
	 * Supprime les doublons
	 * @return $this
	 */
	public function RemoveDoublon()
	{
		$Temp = $this->m_Tab;
		$this->RemoveAll();

		foreach($Temp as $clEnreg)
			$this->AddNouveau($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);

		return $this;
	}

	public function nGetIndice($nIDEnreg)
	{
		foreach($this->m_Tab as $key=>$clEnreg)
		{
			if ($clEnreg->m_nIDEnreg == $nIDEnreg)
				return $key;
		}

		return null;
	}

	// recherche si l'IDAuto est dans la liste
	public  function bEstDansTableau($nIDEnreg)
	{
		return $this->nGetIndice($nIDEnreg) != null;
	}

	public function Intersection($TabIDauto)
	{
		$Temp = $this->m_Tab;
		$this->RemoveAll();

		foreach($Temp as $clEnreg)
		{
			if (    (($TabIDauto instanceof EnregTableauArray) && $TabIDauto->bEstDansTableau($clEnreg->m_nIDEnreg))
				||  (is_array($TabIDauto) && array_search($clEnreg->m_nIDEnreg, $this->m_Tab)))
				$this->Add($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);
		}

		return $this;
	}

	public function Exclusion($TabIDauto)
	{
		$Temp = $this->m_Tab;
		$this->RemoveAll();

		foreach($Temp as $clEnreg)
		{
			if (    (($TabIDauto instanceof EnregTableauArray) && $TabIDauto->bEstDansTableau($clEnreg->m_nIDEnreg))
				||  (is_array($TabIDauto) && array_search($clEnreg->m_nIDEnreg, $this->m_Tab)))
				continue;

			$this->Add($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);
		}

		return $this;
	}


	public function nGetOccurrence($nIDEnreg)
	{
		$nNb = 0;
		foreach($this->m_Tab as $clEnreg)
		{
			if ($clEnreg->m_nIDEnreg == $nIDEnreg)
				$nNb++;
		}
		return $nNb;
	}

	public function RemoveTableau($nIDTableau)
	{
		$Temp = $this->m_Tab;
		$this->RemoveAll();

		foreach($Temp as $clEnreg)
		{
			if ($clEnreg->m_nIDTableau != $nIDTableau)
				$this->Add($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);
		}

		return $this;
	}

	public function ReplaceTableau($nIDTableauARemplacer, $nIDTableau)
	{

		foreach($this->m_Tab as $clEnreg)
		{
			if ($clEnreg->m_nIDTableau == $nIDTableauARemplacer)
				$clEnreg->m_nIDTableau = $nIDTableau;
		}

		return $this;
	}

	public function RemoveIDAuto($nIDEnreg)
	{
		$Temp = $this->m_Tab;
		$this->RemoveAll();

		foreach($Temp as $clEnreg)
		{
			if ($clEnreg->m_nIDEnreg != $nIDEnreg)
				$this->Add($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);
		}

		return $this;
	}

	public function RemoveAt($nIndice)
	{
		$Temp = $this->m_Tab;
		$this->RemoveAll();

		foreach($Temp as $i=>$clEnreg)
		{
			if ($i != $nIndice)
				$this->Add($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);
		}

		return $this;
	}

	public function InsertAt($nIndice, $nIDTableau, $nIDEnreg)
	{
		$Temp = $this->m_Tab;
		$this->RemoveAll();

		foreach($Temp as $i=>$clEnreg)
		{
			if ($i != $nIndice)
				$this->Add($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);
			else
			{
				$this->Add($nIDTableau, $nIDEnreg);
				$this->Add($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);
			}
		}

		return $this;
	}

	public function SetAt($nIndice, $nIDTableau, $nIDEnreg)
	{
		$this->m_Tab[$nIndice]=new EnregTableau($nIDTableau, $nIDEnreg);
		return $this;
	}

	public function Resize($nNewSize)
	{
		$this->m_Tab = array_slice($this->m_Tab, 0, $nNewSize);
		return $this;
	}
}