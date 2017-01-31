<?php

namespace NOUT\Bundle\NOUTOnlineBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NOUTOnlineExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

	    foreach($config as $key=>$value)
        {
            $container->setParameter('nout_online.' . $key, $value);
        }

        /**
         * Symfony creates a big classes.php file in the cache directory to aggregate the contents of the PHP classes that are used in every request.
         * This reduces the I/O operations and increases the application performance.
         * Your bundles can also add their own classes into this file thanks to the addClassesToCompile() method.
         * Define the classes to compile as an array of their fully qualified class names
         *
         * Beware that this technique can't be used in some cases:
         * - When classes contain annotations, such as controllers with @Route annotations and entities with @ORM or @Assert annotations,
         *      because the file location retrieved from PHP reflection changes;
         * - When classes use the __DIR__ and __FILE__ constants, because their values will change when loading these classes from the classes.php file.
         */
        $this->addClassesToCompile(array(
                                       \NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy::class,

                                       //\NOUT\Bundle\NOUTOnlineBundle\Entity\Header
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Header\OptionDialogue::class,

                                       //\NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken::class,

                                       //\NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\CalculationListType::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ColListType::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionColonne::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionFileNPI::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionOperateur::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\GetTokenSession::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ReorderList::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ReorderSubList::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SetOrderList::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SetOrderSubList::class,

                                       //\NOUT\Bundle\NOUTOnlineBundle\Entity\REST
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\REST\Identification::class,

                                       //\NOUT\Bundle\NOUTOnlineBundle\Entity\Record
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Record\ColonneRestriction::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Record\EnregTableau::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Record\EnregTableauArray::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Record\InfoButton::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Record\InfoColonne::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Record\RecordList::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureBouton::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureColonne::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureDonnee::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureElement::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureSection::class,


                                       //\NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\Parser::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ParserChart::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ParserList::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ParserListCalculation::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ParserPlanning::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ParserScheduler::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ParserXSDSchema::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\RecordCache::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ReponseWSParser::class,


                                       //\NOUT\Bundle\NOUTOnlineBundle\Entity
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\Langage::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTFileInfo::class,
                                       \NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineVersion::class,
                                   ));

    }

}
