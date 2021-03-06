<?php
/**
 * @author Dmitry Gladyshev <deel@email.ru>
 * @date 23/08/2016 13:42
 */

namespace Yandex\Direct;

use Yandex\Direct\Exception\InvalidArgumentException;
use Yandex\Direct\Exception\ServiceNotFoundException;
use Yandex\Direct\Transport\Json\Transport;
use Yandex\Direct\Transport\TransportInterface;

/**
 * Class ServiceFactory
 *
 * @package Yandex\Direct
 */
class ServiceFactory implements ServiceFactoryInterface
{
    /**
     * @var string
     */
    protected $serviceNamespace = __NAMESPACE__ . '\\' . 'Service';

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     */
    public function createService($serviceName, array $serviceOptions = [])
    {
        if (empty($serviceOptions[self::OPTION_TRANSPORT])) {
            // Use default transport
            $serviceOptions[self::OPTION_TRANSPORT] = new Transport;
        }

        if (empty($serviceOptions[self::OPTION_CREDENTIALS])) {
            throw new InvalidArgumentException('Credentials is required.');
        }

        $className = $this->getServiceNamespace() . '\\' . ucfirst($serviceName);

        if (!class_exists($className)) {
            throw new ServiceNotFoundException("Service class `{$className}` is not found.");
        }

        $instance = new $className;

        if (!$instance instanceof Service) {
            throw new ServiceNotFoundException(
                "Service class `{$className}` is not instance of `" . Service::class . "`."
            );
        }

        $serviceOptions['name'] = ucfirst($serviceName);
        $instance->setOptions($serviceOptions);

        return $instance;
    }

    /**
     * @param string $namespace
     */
    public function setServiceNamespace($namespace)
    {
        $this->serviceNamespace = $namespace;
    }

    /**
     * @return string
     */
    protected function getServiceNamespace()
    {
        return $this->serviceNamespace;
    }
}
