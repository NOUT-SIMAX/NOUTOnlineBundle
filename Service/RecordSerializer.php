<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 07/03/2018
 * Time: 16:55
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;


use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheFactory;
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTFileInfo;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureDonnee;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureSection;
use NOUT\Bundle\NOUTOnlineBundle\Security\Authentication\Token\NOUTToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class RecordSerializer
{
    /**
     * @var NOUTClientCache
     */
    private $m_clCache = null;


    public function __construct(TokenStorageInterface $tokenStorage, NOUTCacheFactory $cacheFactory)
    {
        /** @var NOUTToken|TokenInterface $oSecurityToken */
        $oSecurityToken = $tokenStorage->getToken();
        $this->m_clCache = new NOUTClientCache($cacheFactory, $oSecurityToken->getSessionToken(), $oSecurityToken->getInfoLangage(), $oSecurityToken->clGetNOUTOnlineVersion());
    }

    /**
     * @param Record $clRecord
     * @param        $idcontexte
     * @param        $idihm
     * @param false  $bIsParam
     * @return string
     */
    public function getRecordUpdateData(Record $clRecord, $idcontexte, $idihm, $bIsParam=false)
    {
        $aFilesToSend = $this->_getModifiedFiles($clRecord, $idcontexte, $idihm, $bIsParam);
        return $clRecord->getUpdateData($aFilesToSend);
    }

    /**
     * @param Record $clRecord
     * @param        $paramXML
     */
    public function updateRecordFromParamXML(Record $clRecord, $paramXML)
    {
        if (!empty($paramXML)){
            $clXML = simplexml_load_string("<xml>$paramXML</xml>");
            foreach ($clXML->children() as $clParam) {
                $idcolonne = str_replace('id_', '', $clParam->getName());
                $value = (string)$clParam;

                $clRecord->setValCol($idcolonne, $value);
            }
        }
    }

    /**
     * @param Record $clRecord
     * @param        $idcontexte
     * @param        $idihm
     * @return string
     */
    public function serializeParamXML(Record $clRecord, $idcontexte, $idihm) : string
    {
        $sIDForm = $clRecord->getIDTableau();
        $paramXML = str_replace(array("<xml><id_$sIDForm>", "</id_$sIDForm></xml>"), array('',''), $this->getRecordUpdateData($clRecord, $idcontexte, $idihm, true));

        return $paramXML;
    }

    /**
     * on construit la structure qui contient tous les fichiers à envoyer
     * @param $clRecord
     * @param $idcontexte
     * @param $idihm
     * @return array
     */
    protected function _getModifiedFiles(Record $clRecord, $idcontexte, $idihm, $bIsParam)
    {
        $aModifiedFiles = array();

        $structElem = $clRecord->getStructElem();
        $fiche = $structElem->getFiche();

        $aModifiedFiles = $this->_getFilesFromSection($clRecord, $idcontexte, $idihm, $fiche, $aModifiedFiles, $bIsParam);


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
    protected function _getFilesFromSection(Record $clRecord, $idcontexte, $idihm, StructureSection $section, $aModifiedFiles, $bIsParam)
    {
        /** @var StructureDonnee[] $TabStructColonne */
        $TabStructColonne = $section->getTabStructureColonne();

        // Contient des structuresDonnes
        foreach ($TabStructColonne as $key => $colonne)
        {
            /**@var StructureDonnee $colonne */
            $idColonne = $colonne->getIDColonne();
            $typeElement = $colonne->getTypeElement();

            if ($typeElement == StructureColonne::TM_Separateur)
            {
                /**@var StructureSection $colonne */
                $aModifiedFiles = $this->_getFilesFromSection($clRecord, $idcontexte, $idihm, $colonne, $aModifiedFiles, $bIsParam);
                continue;
            }

            $bModeleFichier = ($typeElement == StructureColonne::TM_Fichier);
            $bModeleCheminDeFichier = $colonne->isOption(StructureColonne::OPTION_Modele_Directory);
            $bColModified = $clRecord->isModified($idColonne, true);

            if ( ($bModeleFichier || ($bModeleCheminDeFichier && $bIsParam)) && $bColModified)
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