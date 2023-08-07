<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 13/11/14
 * Time: 11:37
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;

/**
 * classe qui contient les identifiants du langage ainsi que les différents checksum de version pour l'IHM
 * Class Langage
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity
 */
class Langage
{
    protected string $sVersionLangage ='';
    protected string $sVersionIcone ='';
    protected bool $bWithUndo = false;
    protected bool $bWithRedo = false;


    /**
     * @return array
     */
    public function forSerialization() : array
    {
        return [$this->sVersionLangage, $this->sVersionIcone, $this->bWithUndo, $this->bWithRedo];
    }

    /**
     * @param array $data
     */
    public function fromSerialization(array $data)
    {
        if (count($data)<=2){
            list($this->sVersionLangage, $this->sVersionIcone) = $data;
            return;
        }
        list($this->sVersionLangage, $this->sVersionIcone, $this->bWithUndo, $this->bWithRedo) = $data;
    }

    /**
     * @param $serialized
     */
    public function unserialize($serialized)
    {
        list($this->sVersionLangage, $this->sVersionIcone) = unserialize($serialized);
    }

    /**
     * @param $sVLangage string
     * @param $sVIcone string
     * @param bool $bUndo
     * @param bool $bRedo
     */
    public function __construct($sVLangage='', $sVIcone='', $bUndo=false, $bRedo=false)
    {
        $this->sVersionLangage = $sVLangage;
        $this->sVersionIcone   = $sVIcone;
        $this->bWithUndo     = $bUndo;
        $this->bWithRedo = $bRedo;
    }

    /**
     * @return string
     */
    public function getVersionLangage(): string
    {
        return $this->sVersionLangage;
    }

    /**
     * @param $sVersion string
     * @return $this
     */
    public function setVersionLangage(string $sVersion): Langage
    {
        $this->sVersionLangage = $sVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersionIcone(): string
    {
        return $this->sVersionIcone;
    }

    /**
     * @param $sVersion string
     * @return $this
     */
    public function setVersionIcone(string $sVersion): Langage
    {
        $this->sVersionIcone = $sVersion;

        return $this;
    }

    /**
     * @return bool
     */
    public function getWithUndo(): bool
    {
        return $this->bWithUndo;
    }

    /**
     * @return bool
     */
    public function getWithRedo(): bool
    {
        return $this->bWithRedo;
    }

    /**
     * @param bool $bUndo
     * @param bool $bRedo
     * @return $this
     */
    public function setUndoRedo(bool $bUndo, bool $bRedo){
        $this->bWithRedo = $bRedo;
        $this->bWithUndo = $bUndo;
        return $this;
    }

    public static function s_isActionReadOnly($eTYPEACTION)
    {
        // Indique quels boutons sont dispo en readOnly
        $isReadOnlyButton =[
            TypeAction::Unknown      => false,
            TypeAction::Creation     => false,
            TypeAction::Modification => false,
            TypeAction::Liste        => false,
            TypeAction::Suppression  => false,
            TypeAction::AjouterA     => false,
            TypeAction::EnleverDe    => false,
            TypeAction::Particuliere => false,
            TypeAction::Exporter     => true,
            TypeAction::Importer     => false,

            TypeAction::Consultation => true,
            TypeAction::Impression   => true,
        ];

        return $isReadOnlyButton[$eTYPEACTION];
    }

    public static function s_needResetLanguageCache($idTableau)
    {
        $aTab = [
            LangageTableau::Tableau,
            LangageTableau::TableauBase,
            LangageTableau::TableauCroise,
            LangageTableau::Vue,
            LangageTableau::Colonne,
            LangageTableau::ColInfo,
            LangageTableau::Calcul,
            LangageTableau::ColLibelle,
            LangageTableau::CalculCompteur,
            LangageTableau::CalculFormule,
            LangageTableau::CalculMax,
            LangageTableau::CalculMin,
            LangageTableau::CalculMoyenne,
            LangageTableau::CalculSomme,
            LangageTableau::ColReference,
            LangageTableau::Modele,
            LangageTableau::ModeleClassique,
            LangageTableau::ModeleElem,
            LangageTableau::ModeleListeElem,
            LangageTableau::ModeleFichier,
            LangageTableau::ModeleChoixMult,
            LangageTableau::Choix
        ];
        return in_array($idTableau, $aTab);
    }


    // Si ajout de boutons, penser à modifier méthode s_isActionReadOnly pour préciser si le bouton est visible

    const idBUTTONTYPE_AjouterA = 1;
    const idBUTTONTYPE_Creer = 2;
    const idBUTTONTYPE_Consulter = 3;
    const idBUTTONTYPE_Modifier = 4;
    const idBUTTONTYPE_Supprimer = 5;
    const idBUTTONTYPE_EnleverDe = 6;
    const idBUTTONTYPE_Imprimer = 7;
    const idBUTTONTYPE_ImprimerListe = 8;
    const idBUTTONTYPE_Fusionner = 9;
    const idBUTTONTYPE_Exporter = 10;
    const idBUTTONTYPE_Importer = 11;
    const idBUTTONTYPE_Dupliquer = 12;

    /*************************************************************
     * enum JOUR SEMAINE
     *************************************************************/

    const JS_Invalide = 0;
    const JS_Lundi    = 9678;
    const JS_Mardi    = 9679;
    const JS_Mercredi = 9680;
    const JS_Jeudi    = 9681;
    const JS_Vendredi = 9682;
    const JS_Samedi   = 9683;
    const JS_Dimanche = 9684;

    /*************************************************************
     * IDENTIFIANT DE MODELE Particulier
     *************************************************************/
    const MT_TypeDAction = 2061;

    //les mimes types
    const MIMETYPE_png = 'image/png';

    //les couleurs définies
    const COLOR_Magenta = 'ff00ff';

    const SMC_Horizontal = 1; //dans le paramétrage SIMAX 16308
    const SMC_Vertical = 2; //dans le paramétrage SIMAX 16307


    const ICONCENTRAL_TitreImgHelp  = 1;
    const ICONCENTRAL_ImgTitreHelp  = 2;
    const ICONCENTRAL_OverlayTop    = 3;
    const ICONCENTRAL_OverlayMiddle = 4;
    const ICONCENTRAL_OverlayBottom = 5;

    //-------------------------------------------------
    const eSTATE_Editable = 0;
    const eSTATE_LectureSeuleAvecModifie = 1;
    const eSTATE_LectureSeuleSansModifie = 2;
    const eSTATE_Grise = 3;
    const eSTATE_Invisible = 4;

}
