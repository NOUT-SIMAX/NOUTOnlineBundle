<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 18/11/14
 * Time: 14:55
 */

namespace NOUT\Bundle\NOUTSessionManagerBundle\DependencyInjection\Factory;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\FormLoginFactory;

class SecurityFactory extends FormLoginFactory
{
	public function getKey()
	{
		return 'noutonline_login';
	}

	protected function getListenerId()
	{
		return 'security.authentication.listener.form';
	}

	protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
	{
		$provider = 'nout_online.authentication_provider.'.$id;
		$container
			->setDefinition($provider, new DefinitionDecorator('nout_online.authentication_provider'))
			->replaceArgument(2, new Reference($userProviderId))
			->replaceArgument(4, $id)
		;

		return $provider;
	}
}