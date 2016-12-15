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
	/**
	 * @var array
	 */
	protected $m_Tab;

	private $_key;

	public function __construct()
	{
		$this->_key  = 0;
		$this->m_Tab = array();
	}

	public function rewind()
	{
		$this->_key = 0;
	}

	/**
	 * @return bool
	 */
	public function valid()
	{
		return array_key_exists($this->_key, $this->m_Tab);
	}

	/**
	 * @return int|mixed
	 */
	public function key()
	{
		return $this->_key;
	}

	/**
	 * @return mixed
	 */
	public function current()
	{
		return $this->m_Tab[$this->_key];
	}

	public function next()
	{
		++$this->_key;
	}

	/**
	 * @param null $nIDTableau
	 * @param bool $bMemeTaille
	 * @return array
	 */
	public function GetTabIDEnreg($nIDTableau = null, $bMemeTaille = false)
	{
		$aRet = array();

		foreach ($this->m_Tab as $clEnreg)
		{
			if (($clEnreg->m_nIDTableau == $nIDTableau) || empty($nIDTableau))
			{
				$aRet[] = $clEnreg->m_nIDEnreg;
			}
			elseif ($bMemeTaille)
			{
				$aRet[] = null;
			}
		}

		return $aRet;
	}

	/**
	 * @return array
	 */
	public function GetTabIDTableau()
	{
		$aRet = array();
		foreach ($this->m_Tab as $clEnregTab)
		{
			$aRet[] = $clEnregTab->m_nIDTableau;
		}

		return $aRet;
	}

	/**
	 * @return bool
	 */
	public function bEstIDTableauUnique()
	{
		if (count($this->m_Tab) == 0)
		{
			return false;
		}

		$nIDPrec = $this->m_Tab[0]->m_nIDTableau;
		foreach ($this->m_Tab as $clEnregTab)
		{
			if ($clEnregTab->m_nIDTableau != $nIDPrec)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * @param $nIDEnreg
	 * @return null|string
	 */
	public function nGetIDTableauEnreg($nIDEnreg)
	{
		foreach ($this->m_Tab as $clEnregTab)
		{
			if ($clEnregTab->m_nIDEnreg == $nIDEnreg)
			{
				return $clEnregTab->m_nIDTableau;
			}
		}

		return;
	}

	/**
	 * @param $nIndice
	 * @return null|string
	 */
	public function nGetIDTableau($nIndice)
	{
		if (isset($this->m_Tab[$nIndice]))
		{
			return $this->m_Tab[$nIndice]->m_nIDTableau;
		}

		return;
	}

	/**
	 * @param $nIndice
	 * @return null|string
	 */
	public function nGetIDEnreg($nIndice)
	{
		if (isset($this->m_Tab[$nIndice]))
		{
			return $this->m_Tab[$nIndice]->m_nIDEnreg;
		}

		return;
	}

	/**
	 * @return int
	 */
	public function GetSize()
	{
		return count($this->m_Tab);
	}

	/**
	 * @return bool
	 */
	public function IsEmpty()
	{
		return count($this->m_Tab) == 0;
	}

	/**
	 * @return $this
	 */
	public function RemoveAll()
	{
		$this->m_Tab = array();
		$this->rewind();

		return $this;
	}

	/**
	 * @param $nIDTableau
	 * @param $nIDEnreg
	 * @return $this
	 */
	public function Add($nIDTableau, $nIDEnreg)
	{
		$this->m_Tab[] = new EnregTableau($nIDTableau, $nIDEnreg);

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
		foreach ($this->m_Tab as $clEnregTab)
		{
			if ($clEnregTab->m_nIDEnreg == $nIDEnreg)
			{
				return $this;
			}
		}

		return $this->Add($nIDTableau, $nIDEnreg);
	}

	/**
	 * @param $nIDTableau
	 * @param $sStr
	 * @return $this
	 */
	public function AddFromListeStr($nIDTableau, string $sStr)
	{
		$TabIDEnreg = explode('|', $sStr);

		foreach ($TabIDEnreg as $nIDEnreg)
		{
			$this->Add($nIDTableau, $nIDEnreg);
		}

		return $this;
	}

	/**
	 * @param $TabSrc
	 * @param null $nIDTableau
	 * @return $this
	 */
	public function Append($TabSrc, $nIDTableau = null)
	{
		if ($TabSrc instanceof EnregTableauArray)
		{
			$this->m_Tab = array_merge($this->m_Tab, $TabSrc->m_Tab);
		}
		elseif (is_array($TabSrc))
		{
			foreach ($TabSrc as $nIDEnreg)
			{
				$this->Add($nIDTableau, $nIDEnreg);
			}
		}

		return $this;
	}

	/**
	 * @param $TabSrc
	 * @param null $nIDTableau
	 * @return $this
	 */
	public function AppendNouveauIDAuto($TabSrc, $nIDTableau = null)
	{
		if ($TabSrc instanceof EnregTableauArray)
		{
			foreach ($TabSrc as $clEnreg)
			{
				$this->AddNouveau($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);
			}
		}
		elseif (is_array($TabSrc))
		{
			foreach ($TabSrc as $nIDEnreg)
			{
				$this->AddNouveau($nIDTableau, $nIDEnreg);
			}
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

		foreach ($Temp as $clEnreg)
		{
			$this->AddNouveau($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);
		}

		return $this;
	}

	/**
	 * @param $nIDEnreg
	 * @return int|null|string
	 */
	public function nGetIndice($nIDEnreg)
	{
		foreach ($this->m_Tab as $key => $clEnreg)
		{
			if ($clEnreg->m_nIDEnreg == $nIDEnreg)
			{
				return $key;
			}
		}

		return;
	}

	/**
	 * recherche si l'IDAuto est dans la liste
	 * @param $nIDEnreg
	 * @return bool
	 */
	public function bEstDansTableau($nIDEnreg)
	{
		return !is_null($this->nGetIndice($nIDEnreg));
	}

	/**
	 * @param $TabIDauto
	 * @return $this
	 */
	public function Intersection($TabIDauto)
	{
		$Temp = $this->m_Tab;
		$this->RemoveAll();

		foreach ($Temp as $clEnreg)
		{
			if ((($TabIDauto instanceof EnregTableauArray) && $TabIDauto->bEstDansTableau($clEnreg->m_nIDEnreg))
				||  (is_array($TabIDauto) && array_search($clEnreg->m_nIDEnreg, $this->m_Tab)))
			{
				$this->Add($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);
			}
		}

		return $this;
	}

	/**
	 * @param $TabIDauto
	 * @return $this
	 */
	public function Exclusion($TabIDauto)
	{
		$Temp = $this->m_Tab;
		$this->RemoveAll();

		foreach ($Temp as $clEnreg)
		{
			if ((($TabIDauto instanceof EnregTableauArray) && $TabIDauto->bEstDansTableau($clEnreg->m_nIDEnreg))
				||  (is_array($TabIDauto) && array_search($clEnreg->m_nIDEnreg, $this->m_Tab)))
			{
				continue;
			}

			$this->Add($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);
		}

		return $this;
	}

	/**
	 * @param $nIDEnreg
	 * @return int
	 */
	public function nGetOccurrence($nIDEnreg)
	{
		$nNb = 0;
		foreach ($this->m_Tab as $clEnreg)
		{
			if ($clEnreg->m_nIDEnreg == $nIDEnreg)
			{
				$nNb++;
			}
		}

		return $nNb;
	}

	/**
	 * @param $nIDTableau
	 * @return $this
	 */
	public function RemoveTableau($nIDTableau)
	{
		$Temp = $this->m_Tab;
		$this->RemoveAll();

		foreach ($Temp as $clEnreg)
		{
			if ($clEnreg->m_nIDTableau != $nIDTableau)
			{
				$this->Add($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);
			}
		}

		return $this;
	}

	/**
	 * @param $nIDTableauARemplacer
	 * @param $nIDTableau
	 * @return $this
	 */
	public function ReplaceTableau($nIDTableauARemplacer, $nIDTableau)
	{
		foreach ($this->m_Tab as $clEnreg)
		{
			if ($clEnreg->m_nIDTableau == $nIDTableauARemplacer)
			{
				$clEnreg->m_nIDTableau = $nIDTableau;
			}
		}

		return $this;
	}

	/**
	 * @param $nIDEnreg
	 * @return $this
	 */
	public function RemoveIDAuto($nIDEnreg)
	{
		$Temp = $this->m_Tab;
		$this->RemoveAll();

		foreach ($Temp as $clEnreg)
		{
			if ($clEnreg->m_nIDEnreg != $nIDEnreg)
			{
				$this->Add($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);
			}
		}

		return $this;
	}

	/**
	 * @param $nIndice
	 * @return $this
	 */
	public function RemoveAt($nIndice)
	{
		$Temp = $this->m_Tab;
		$this->RemoveAll();

		foreach ($Temp as $i => $clEnreg)
		{
			if ($i != $nIndice)
			{
				$this->Add($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);
			}
		}

		return $this;
	}

	/**
	 * @param $nIndice
	 * @param $nIDTableau
	 * @param $nIDEnreg
	 * @return $this
	 */
	public function InsertAt($nIndice, $nIDTableau, $nIDEnreg)
	{
		$Temp = $this->m_Tab;
		$this->RemoveAll();

		foreach ($Temp as $i => $clEnreg)
		{
			if ($i != $nIndice)
			{
				$this->Add($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);
			}
			else
			{
				$this->Add($nIDTableau, $nIDEnreg);
				$this->Add($clEnreg->m_nIDTableau, $clEnreg->m_nIDEnreg);
			}
		}

		return $this;
	}

	/**
	 * @param $nIndice
	 * @return EnregTableau
	 */
	public function GetAt($nIndice)
	{
		return $this->m_Tab[$nIndice];
	}

	/**
	 * @param $nIndice
	 * @param $nIDTableau
	 * @param $nIDEnreg
	 * @return $this
	 */
	public function SetAt($nIndice, $nIDTableau, $nIDEnreg)
	{
		$this->m_Tab[$nIndice] = new EnregTableau($nIDTableau, $nIDEnreg);

		return $this;
	}

	/**
	 * @param $nNewSize
	 * @return $this
	 */
	public function Resize($nNewSize)
	{
		$this->m_Tab = array_slice($this->m_Tab, 0, $nNewSize);

		return $this;
	}
}
