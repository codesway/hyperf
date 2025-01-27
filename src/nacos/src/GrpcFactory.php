<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\Nacos;

use Hyperf\Context\ApplicationContext;
use Hyperf\Nacos\Exception\InvalidArgumentException;
use Psr\Container\ContainerInterface;

class GrpcFactory
{
    /**
     * @var array<string, GrpcClient>
     */
    protected array $clients = [];

    public function __construct(protected Application $app, protected Config $config)
    {
        if (! $this->config->getGrpc()['enable']) {
            throw new InvalidArgumentException('GRPC module is disable, please set `nacos.default.grpc.enable = true`.');
        }
    }

    public function get(string $namespaceId, string $module = 'config'): GrpcClient
    {
        if (isset($this->clients[$namespaceId][$module])) {
            return $this->clients[$namespaceId][$module];
        }

        return $this->clients[$namespaceId][$module] = new GrpcClient($this->app, $this->config, $this->container(), $namespaceId, $module);
    }

    /**
     * @return GrpcClient[]
     */
    public function getClients(): array
    {
        return $this->clients;
    }

    private function container(): ContainerInterface
    {
        return ApplicationContext::getContainer();
    }
}
