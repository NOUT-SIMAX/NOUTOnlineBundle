<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 07/03/2018
 * Time: 16:55
 */

namespace NOUT\Bundle\ContextsBundle\Service;


use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheFactory;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureSection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class RecordSerializer
{
    /**
     * @var NOUTClientCache
     */
    private $m_clCache = null;


    public function __construct(TokenStorage $tokenStorage, NOUTCacheFactory $cacheFactory)
    {
        $oSecurityToken = $tokenStorage->getToken();
        $this->m_clCache = new NOUTClientCache($cacheFactory, $oSecurityToken->getSessionToken(), $oSecurityToken->getLangage());
    }

    public function getRecordUpdateData(Record $clRecord, $idcontexte, $idihm)
    {
        $aFilesToSend = $this->_getModifiedFiles($clRecord, $idcontexte, $idihm);
        return $clRecord->getUpdateData($aFilesToSend);
    }

    public function getParamXML(Record $clRecord, $paramXML, $idcontexte, $idihm)
    {
        if (strlen($paramXML)==0){
            return $paramXML;
        }

        //mise à jour depuis les paramètres
        $clXML = simplexml_load_string("<xml>$paramXML</xml>");
        foreach ($clXML->children() as $clParam) {
            $idcolonne = str_replace('id_', '', $clParam->getName());
            $value = (string)$clParam;

            $clRecord->setValCol($idcolonne, $value);
        }

        $sIDForm = $clRecord->getIDTableau();
        $paramXML = str_replace(array("<xml><id_$sIDForm>", "</id_$sIDForm></xml>"), array('',''), $this->getRecordUpdateData($clRecord, $idcontexte, $idihm));

        return $paramXML;
    }

    /**
     * on construit la structure qui contient tous les fichiers à envoyer
     * @param $clRecord
     * @param $idcontexte
     * @param $idihm
     * @return array
     */
    protected function _getModifiedFiles(Record $clRecord, $idcontexte, $idihm)
    {
        $aModifiedFiles = array();

        $structElem = $clRecord->clGetStructElem();
        $fiche = $structElem->getFiche();

        $aModifiedFiles = $this->_getFilesFromSection($clRecord, $idcontexte, $idihm, $fiche, $aModifiedFiles);


        return $aModifiedFiles;
    }

    /**
     * recherche récursive des fichiers
     * @param $clRecord
     * @param $idcontexte
     * @param $idihm
     * @param $section
     * @param $aModifiedFiles
     * @return array
     */
    protected function _getFilesFromSection(Record $clRecord, $idcontexte, $idihm, StructureSection $section, $aModifiedFiles)
    {
        $structColonne = $section->getTabStructureColonne();

        // Contient des structuresDonnes
        foreach ($structColonne as $key => $colonne)
        {
            /**@var StructureDonnee $colonne */
            $idColonne = $colonne->getIDColonne();
            $typeElement = $colonne->getTypeElement();

            if ($typeElement == StructureColonne::TM_Separateur)
            {
                /**@var StructureSection $colonne */
                $aModifiedFiles = $this->_getFilesFromSection($clRecord, $idcontexte, $idihm, $colonne, $aModifiedFiles);
            }
            else if ($typeElement == StructureColonne::TM_Fichier && $clRecord->isModified($idColonne))
            {
                // On a un fichier modifié, on doit le récupérer
                $fullPath = $clRecord->getValCol($idColonne);
                if ($fullPath != "")
                {
                    $name = explode('?', $fullPath); // Le nom du fichier se trouve après le path
                    /** @var NOUTFileInfo $data */
                    $file = $this->m_clCache->fetchFileFromName($idcontexte, $idihm, $name[0]);

                    // Ajout du fichier dans le tableau
                    $aModifiedFiles[$idColonne] = $file;
                }
                else
                {
                    $aModifiedFiles[$idColonne] = null;
                }
            }
        }

        return $aModifiedFiles;
    }
}