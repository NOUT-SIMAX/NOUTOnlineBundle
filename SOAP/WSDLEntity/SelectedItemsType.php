<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity;

/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 22/07/14
 * Time: 17:16
 */

//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------

class SelectedItemsType extends AbstractStructBase implements SerializableEntity
{
    /**
     * The _
     * @var string
     */
	public $_;

    /**
     * The column
     * Meta information extracted from the WSDL
     * - use: optional
     * @var string
     */
	public $column; // string

    static function getAttributes()
    {
        return array(
            'column'
        );
    }

    static function getValueType()
    {
        return 'string';
    }

    static function getEntityDefinition()
    {
        return new WSDLEntityDefinition(self::getAttributes(), self::getValueType());
    }

    /**
     * Constructor method for SelectedItemsType
     * @uses SelectedItemsType::set_()
     * @uses SelectedItemsType::setColumn()
     * @param string $_
     * @param string $column
     */
    public function __construct($_ = null, $column = null)
    {
        $this
            ->set_($_)
            ->setColumn($column);
    }
    /**
     * Get _ value
     * @return string|null
     */
    public function get_()
    {
        return $this->_;
    }
    /**
     * Set _ value
     * @param string $_
     * @return SelectedItemsType
     */
    public function set_($_ = null)
    {
        // validation for constraint: string
        if (!is_null($_) && !is_string($_)) {
            throw new \InvalidArgumentException(sprintf('Invalid value %s, please provide a string, %s given', var_export($_, true), gettype($_)), __LINE__);
        }
        $this->_ = $_;
        return $this;
    }
    /**
     * Get column value
     * @return string|null
     */
    public function getColumn()
    {
        return $this->column;
    }
    /**
     * Set column value
     * @param string $column
     * @return SelectedItemsType
     */
    public function setColumn($column = null)
    {
        // validation for constraint: string
        if (!is_null($column) && !is_string($column)) {
            throw new \InvalidArgumentException(sprintf('Invalid value %s, please provide a string, %s given', var_export($column, true), gettype($column)), __LINE__);
        }
        $this->column = $column;
        return $this;
    }
}
//***
