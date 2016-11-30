<?php

namespace App\Service;


use GuzzleHttp\Client;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class UserAuthenticationServiceClientFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config')['app']['authentication']['service'];

        return new Client([
            'base_uri' => $this->getBaseUri($config)
        ]);
    }

    /**
     * @param array $config
     * @return string
     */
    private function getBaseUri($config)
    {
        if (array_key_exists('port', $config) && isset($config['port'])) {
            return sprintf('%s:%d', $config['host'], $config['port']);
        }

        return $config['host'];
    }
}