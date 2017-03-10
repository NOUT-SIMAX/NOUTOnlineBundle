<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/03/2017
 * Time: 16:16
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SOAPParameter;

class CondType extends SOAPParameter
{
    //type de condition
    const COND_EQUAL               = 'Equal';
    const COND_DIFFERENT           = 'Different';
    const COND_LESS                = 'Less';
    const COND_LESSOREQUAL         = 'LessOrEqual';
    const COND_BETTER              = 'Better';
    const COND_BETTEROREQUAL       = 'BetterOrEqual';
    const COND_CONTAIN             = 'Contain';
    const COND_DONOTCONTAIN        = 'DoNotContain';
    const COND_BEGINWITH           = 'BeginWith';
    const COND_DONOTBEGINWITH      = 'DoNotBeginWith';
    const COND_ENDWITH             = 'EndWith';
    const COND_DONOTENDWITH        = 'DoNotEndWith';
    const COND_ISWITHIN            = 'IsWithin';
    const COND_WITHRIGHT           = 'WithRight';
    const COND_BEGINWITHWORDBYWORD = 'BeginWithWordByWord';
    const COND_MENUVISIBLE         = 'MenuVisible';

    /** @var  string $type*/
    protected $type;

    public function __construct($type)
    {
        if(!$this->isValid($type))
            throw new \InvalidArgumentException('Condition type' . $type . ' doesn\'t exist');
        $this->type = $type;
    }

    public function getOpeningTag()
    {
        return '<CondType>';
    }

    public function getClosingTag()
    {
        return '</CondType>';
    }

    public function getContent()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return bool
     */
    protected function isValid($type){
        $ops = array(
            self::COND_EQUAL,
            self::COND_DIFFERENT,
            self::COND_LESS,
            self::COND_LESSOREQUAL,
            self::COND_BETTER,
            self::COND_CONTAIN,
            self::COND_DONOTCONTAIN,
            self::COND_BEGINWITH,
            self::COND_ENDWITH,
            self::COND_DONOTENDWITH,
            self::COND_ISWITHIN,
            self::COND_WITHRIGHT,
            self::COND_BEGINWITHWORDBYWORD,
            self::COND_MENUVISIBLE,
        );
        return in_array($type, $ops);
    }
}