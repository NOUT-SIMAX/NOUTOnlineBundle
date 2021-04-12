<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity;

/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 22/07/14
 * Time: 17:05
 */
//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------

class GetTokenSession
{
    /**
     * The Token
     * Meta informations extracted from the WSDL
     * - choice: Token | UsernameToken
     * - choiceMaxOccurs: 1
     * - choiceMinOccurs: 1
     * @var string
     */
    public $Token;

    /**
     * The UsernameToken
     * Meta informations extracted from the WSDL
     * - choice: Token | UsernameToken
     * - choiceMaxOccurs: 1
     * - choiceMinOccurs: 1
     * @var UsernameToken
     */
	public $UsernameToken;

    /**
     * The ExtranetUser
     * Meta informations extracted from the WSDL
     * - minOccurs: 0
     * @var ExtranetUserType
     */
	public $ExtranetUser;

    /**
     * The DefaultClientLanguageCode
     * @var int
     */
	public $DefaultClientLanguageCode;

    /**
     * Constructor method for GetTokenSession
     * @param string|null $token
     * @param UsernameToken|null $usernameToken
     * @param ExtranetUserType|null $extranetUser
     * @param int|null $defaultClientLanguageCode
     */
    public function __construct(string $token = null, UsernameToken $usernameToken = null, ExtranetUserType $extranetUser = null, int $defaultClientLanguageCode = null)
    {
        $this
            ->setToken($token)
            ->setUsernameToken($usernameToken)
            ->setExtranetUser($extranetUser)
            ->setDefaultClientLanguageCode($defaultClientLanguageCode);
    }

    /**
     * This method is responsible for validating the value passed to the setToken method
     * This method is willingly generated in order to preserve the one-line inline validation within the setToken method
     * This has to validate that the property which is being set is the only one among the given choices
     * @param mixed $value
     * @return string A non-empty message if the values does not match the validation rules
     */
    protected function _validateTokenForChoiceConstraintsFromSetToken($value) : string
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
                    throw new \InvalidArgumentException(sprintf('The property Token can\'t be set as the property %s is already set. Only one property must be set among these properties: Token, %s.', $property, implode(', ', $properties)), __LINE__);
                }
            }
        } catch (\InvalidArgumentException $e) {
            $message = $e->getMessage();
        }
        return $message;
    }
    /**
     * Set Token value
     * This property belongs to a choice that allows only one property to exist. It is
     * therefore removable from the request, consequently if the value assigned to this
     * property is null, the property is removed from this object
     * @throws \InvalidArgumentException
     * @param string $token
     * @return GetTokenSession
     */
    public function setToken($token = null) : GetTokenSession
    {
        // validation for constraint: string
        if (!is_null($token) && !is_string($token)) {
            throw new \InvalidArgumentException(sprintf('Invalid value %s, please provide a string, %s given', var_export($token, true), gettype($token)), __LINE__);
        }
        // validation for constraint: choice(Token, UsernameToken)
        if ('' !== ($tokenChoiceErrorMessage = self::_validateTokenForChoiceConstraintsFromSetToken($token))) {
            throw new \InvalidArgumentException($tokenChoiceErrorMessage, __LINE__);
        }
        if (is_null($token) || (is_array($token) && empty($token))) {
            unset($this->Token);
        } else {
            $this->Token = $token;
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
    protected function _validateUsernameTokenForChoiceConstraintsFromSetUsernameToken($value) : string
    {
        $message = '';
        if (is_null($value)) {
            return $message;
        }
        $properties = [
            'Token',
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
     * Set UsernameToken value
     * This property belongs to a choice that allows only one property to exist. It is
     * therefore removable from the request, consequently if the value assigned to this
     * property is null, the property is removed from this object
     * @throws \InvalidArgumentException
     * @param UsernameToken|null $usernameToken
     * @return GetTokenSession
     */
    public function setUsernameToken(UsernameToken $usernameToken = null) : GetTokenSession
    {
        // validation for constraint: choice(Token, UsernameToken)
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
     * Set ExtranetUser value
     * @param ExtranetUserType|null $extranetUser
     * @return GetTokenSession
     */
    public function setExtranetUser(ExtranetUserType $extranetUser = null) : GetTokenSession
    {
        $this->ExtranetUser = $extranetUser;
        return $this;
    }

    /**
     * Set DefaultClientLanguageCode value
     * @param int|null $defaultClientLanguageCode
     * @return GetTokenSession
     */
    public function setDefaultClientLanguageCode(int $defaultClientLanguageCode=null) : GetTokenSession
    {
        // validation for constraint: int
        if (!is_null($defaultClientLanguageCode) && !(is_int($defaultClientLanguageCode) || ctype_digit($defaultClientLanguageCode))) {
            throw new \InvalidArgumentException(sprintf('Invalid value %s, please provide an integer value, %s given', var_export($defaultClientLanguageCode, true), gettype($defaultClientLanguageCode)), __LINE__);
        }
        $this->DefaultClientLanguageCode = $defaultClientLanguageCode;
        return $this;
    }

}
//***
