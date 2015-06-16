<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 19/09/14
 * Time: 11:18
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Cache;

use Doctrine\Common\Cache\CacheProvider;

class NOUTFileCache extends CacheProvider
{
	private $m_sDir;
	private $m_sExtension = '.noutcache.data';


	public function __construct($dir)
	{
		$this->m_sDir = $dir;
	}


	protected function _sGetFilename($id)
	{
		return $this->m_sDir.'/'.$id.$this->m_sExtension;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doFetch($id)
	{
		$sFilePath = $this->_sGetFilename($id);
		if (!file_exists($sFilePath))
		{
			return false;
		}

		return unserialize(file_get_contents($sFilePath));
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doContains($id)
	{
		$sFilePath = $this->_sGetFilename($id);

		return file_exists($sFilePath);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doSave($id, $data, $lifeTime = 0)
	{
		if (!file_exists($this->m_sDir))
		{
			if (!@mkdir($this->m_sDir))
			{
				return false;
			}
		}

		file_put_contents($this->_sGetFilename($id), serialize($data));

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doDelete($id)
	{
        $sFilePath = $this->_sGetFilename($id);
        if (file_exists($sFilePath))
        {
            unlink($this->_sGetFilename($id));
        }

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doFlush()
	{
		array_map('unlink', glob($this->m_sDir.'/*')); //on supprime tous les anciens fichier de cache WSDL
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doGetStats()
	{
		return;
	}
}
