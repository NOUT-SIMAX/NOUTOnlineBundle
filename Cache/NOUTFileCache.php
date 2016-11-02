<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 19/09/14
 * Time: 11:18
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Cache;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class NOUTFileCache extends NOUTCacheProvider
{
    const FILE_EXTENSION = '.noutcache.data';

    protected function makeKey($id, $prefix)
    {
        if (!is_array($id)){
            if (empty($id)){
                return $prefix;
            }
            return sprintf('%s/%s', $prefix, (string)$id);
        }

        $key = $prefix;
        foreach($id as $subid){
            $key.=sprintf('/%s', (string)$subid);
        }
        return $key;
    }

    protected function getNamespacedId($id)
    {
        $key = $this->makeKey($id, $this->namespace);
        if (!empty($id))
        {
            $key.=self::FILE_EXTENSION;
        }
        return $key;
    }

	/**
	 * {@inheritdoc}
	 */
	protected function doFetch($id)
	{
		if (!file_exists($id))
		{
			return false;
		}

		return unserialize(file_get_contents($id));
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doContains($id)
	{
		return file_exists($id);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doSave($id, $data, $lifeTime = 0)
	{
        $dir = dirname($id);
		if (!file_exists($dir))
		{
			if (!@mkdir($dir, 0777, true))
			{
				return false;
			}
		}
		file_put_contents($id, serialize($data));
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doDelete($id)
	{
        if (file_exists($id))
        {
            unlink($id);
        }

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doFlush()
	{
		array_map('unlink', glob($this->namespace.'/*'));
		return true;
	}

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id The beginning of the id of the cache entry to list.
     *
     * @return array|false.
     */
    protected function doListEntry($id)
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in($id.'/*')
            ->name('*'.self::FILE_EXTENSION);

        $aRet = array();
        foreach ($finder as $file) {
            /** @var SplFileInfo $file */
            $aRet[]=$file->getRealPath();
        }

        return $aRet;
    }

    /**
     * {@inheritDoc}
     */
    public function flushAll($prefix='')
    {
        $nbFile = parent::flushAll($prefix);

        $id = $this->getNamespacedId($prefix);
        $aDir = $this->getSubDir($id);

        usort($aDir, function ($a, $b)
        {
            return -strcmp($a, $b);
        });

        //on supprime les sous-rep
        foreach($aDir as $dir)
        {
            if (is_dir($dir))
            {
                @rmdir($dir);
            }
        }
        //on supprime le rep de la session
        @rmdir($id);

        return $nbFile;
    }

    protected function getSubDir($id)
    {
        $finder = new Finder();
        $finder->directories()->in($id.'/*');
        $finder->sort(function (\SplFileInfo $a, \SplFileInfo $b)
        {
            return -strcmp($a->getRealPath(), $b->getRealPath());
        });

        $id = str_replace(array('\\', '/'), array('_', '_'), $id);

        $aRet = array();
        foreach ($finder as $dir) {
            /** @var SplFileInfo $dir */
            $path_dir = $dir->getRealPath();
            do
            {
                $aRet[]=$path_dir;
                $path_dir=dirname($path_dir);
            }
            while(str_replace(array('\\', '/'), array('_', '_'), $path_dir) != $id);
        }
        return $aRet;
    }


}
