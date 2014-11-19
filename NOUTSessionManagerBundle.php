<?php

namespace NOUT\Bundle\NOUTSessionManagerBundle;

use NOUT\Bundle\NOUTSessionManagerBundle\DependencyInjection\Factory\SecurityFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NOUTSessionManagerBundle extends Bundle
{
	public function build(ContainerBuilder $container)
	{
		$extension = $container->getExtension('security');
		$extension->addSecurityListenerFactory(new SecurityFactory());
	}
}
