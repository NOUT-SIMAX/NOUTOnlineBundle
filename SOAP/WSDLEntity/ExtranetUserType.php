<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity;

/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 22/07/14
 * Time: 17:04
 */

//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------

class ExtranetUserType
{
    /**
     * The UsernameToken
     * Meta informations extracted from the WSDL
     * - choice: UsernameToken | Anonymous
     * - choiceMaxOccurs: 1
     * - choiceMinOccurs: 1
     * @var UsernameToken
     */
	public $UsernameToken;
    /**
     * The Anonymous
     * Meta informations extracted from the WSDL
     * - choice: UsernameToken | Anonymous
     * - choiceMaxOccurs: 1
     * - choiceMinOccurs: 1
     * @var int
     */
    public $Anonymous;
	/** @var string */
	public $Form;

    /**
     * Constructor method for ExtranetUserType
     * @uses ExtranetUserType::setUsernameToken()
     * @uses ExtranetUserType::setAnonymous()
     * @uses ExtranetUserType::setForm()
     * @param UsernameToken $usernameToken
     * @param int $anonymous
     * @param string $form
     */
    public function __construct(UsernameToken $usernameToken = null, $anonymous = null, $form = null)
    {
        $this
            ->setUsernameToken($usernameToken)
            ->setAnonymous($anonymous)
            ->setForm($form);
    }

    /**
     * Set Form value
     * @param string $form
     * @return ExtranetUserType
     */
    public function setForm($form = null) : ExtranetUserType
    {
        // validation for constraint: string
        if (!is_null($form) && !is_string($form) && !is_int($form)) {
            throw new \InvalidArgumentException(sprintf('Invalid value %s, please provide a string or int, %s given', var_export($form, true), gettype($form)), __LINE__);
        }
        $this->Form = $form;
        return $this;
    }


    /**
     * Set UsernameToken value
     * This property belongs to a choice that allows only one property to exist. It is
     * therefore removable from the request, consequently if the value assigned to this
     * property is null, the property is removed from this object
     * @throws \InvalidArgumentException
     * @param UsernameToken $usernameToken
     * @return ExtranetUserType
     */
    public function setUsernameToken(UsernameToken $usernameToken = null) : ExtranetUserType
    {
        // validation for constraint: choice(UsernameToken, Anonymous)
        if ('' !== ($usernameTokenChoiceErrorMessage = self::_validateUsernameTokenForChoiceConstraintsFromSetUsernameToken($usernameToken))) {
            throw new \InvalidArgumentException($usernameTokenChoiceErrorMessage, __LINE__);
        }
        if (is_null($usernameToken) || (is_array($usernameToken) && empty($usernameToken))) {
            unset($this->UsernameToken);
        } else {
            $this->UsernameToken = $usernameToken;
        }
        return $this;
    }
    /**
     * This method is responsible for validating the value passed to the setUsernameToken method
     * This method is willingly generated in order to preserve the one-line inline validation within the setUsernameToken method
     * This has to validate that the property which is being set is the only one among the given choices
     * @param mixed $value
     * @return string A non-empty message if the values does not match the validation rules
     */
    protected function _validateUsernameTokenForChoiceConstraintsFromSetUsernameToken($value)
    {
        $message = '';
        if (is_null($value)) {
            return $message;
        }
        $properties = [
            'Anonymous',
        ];
        try {
            foreach ($properties as $property) {
                if (isset($this->{$property})) {
                    throw new \InvalidArgumentException(sprintf('The property UsernameToken can\'t be set as the property %s is already set. Only one property must be set among these properties: UsernameToken, %s.', $property, implode(', ', $properties)), __LINE__);
                }
            }
        } catch (\InvalidArgumentException $e) {
            $message = $e->getMessage();
        }
        return $message;
    }

    /**
     * Set Anonymous value
     * This property belongs to a choice that allows only one property to exist. It is
     * therefore removable from the request, consequently if the value assigned to this
     * property is null, the property is removed from this object
     * @throws \InvalidArgumentException
     * @param int $anonymous
     * @return ExtranetUserType
     */
    public function setAnonymous($anonymous = null) : ExtranetUserType
    {
        // validation for constraint: int
        if (!is_null($anonymous) && !(is_int($anonymous) || ctype_digit($anonymous))) {
            throw new \InvalidArgumentException(sprintf('Invalid value %s, please provide an integer value, %s given', var_export($anonymous, true), gettype($anonymous)), __LINE__);
        }
        // validation for constraint: choice(UsernameToken, Anonymous)
        if ('' !== ($anonymousChoiceErrorMessage = self::_validateAnonymousForChoiceConstraintsFromSetAnonymous($anonymous))) {
            throw new \InvalidArgumentException($anonymousChoiceErrorMessage, __LINE__);
        }
        if (is_null($anonymous) || (is_array($anonymous) && empty($anonymous))) {
            unset($this->Anonymous);
        } else {
            $this->Anonymous = $anonymous;
        }
        return $this;
    }
    /**
     * This method is responsible for validating the value passed to the setAnonymous method
     * This method is willingly generated in order to preserve the one-line inline validation within the setAnonymous method
     * This has to validate that the property which is being set is the only one among the given choices
     * @param mixed $value
     * @return string A non-empty message if the values does not match the validation rules
     */
    public function _validateAnonymousForChoiceConstraintsFromSetAnonymous($value)
    {
        $message = '';
        if (is_null($value)) {
            return $message;
        }
        $properties = [
            'UsernameToken',
        ];
        try {
            foreach ($properties as $property) {
                if (isset($this->{$property})) {
                    throw new \InvalidArgumentException(sprintf('The property Anonymous can\'t be set as the property %s is already set. Only one property must be set among these properties: Anonymous, %s.', $property, implode(', ', $properties)), __LINE__);
                }
            }
        } catch (\InvalidArgumentException $e) {
            $message = $e->getMessage();
        }
        return $message;
    }
}
//***
