<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 05/01/15
 * Time: 18:18
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;


class ValidateError implements \JsonSerializable
{
	/**
	<ValidateError>
	<ListCol>
	<Column title="Entier">226904345564985</Column>
	</ListCol>
	<Message>La colonne obligatoire 'Entier' de 'Type de champ mini' n'a pas été remplie.</Message>
	</ValidateError>
	 */

	/**
	 * @var string message d'erreur
	 */
	protected $m_sMessage;

	/**
	 * @var array tableau des identifiants des colonnes avec erreurs
	 */
	protected $m_TabIDColonne;


	/**
	 * @param \SimpleXMLElement $clNoeud
	 */
	public function __construct(\SimpleXMLElement $clNoeud)
	{
		$this->m_sMessage = (string)$clNoeud->children()->Message;
		$this->m_TabIDColonne = array();

		foreach($clNoeud->children()->ListCol->children() as $ndColumn)
		{
			$this->m_TabIDColonne[]=(string)$ndColumn;
		}
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->m_sMessage;
	}

	/**
	 * @return array
	 */
	public function getTabIDColonne()
	{
		return $this->m_TabIDColonne;
	}

	/**
	 * @param $indice
	 * @return mixed
	 */
	public function getIDColonne($indice)
	{
		return $this->m_TabIDColonne[$indice];
	}

    public function jsonSerialize() {
        return array(
            'message' => $this->m_sMessage,
            'columns' => $this->m_TabIDColonne,
        );
    }
} 