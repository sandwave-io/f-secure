<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Client;

use SandwaveIo\FSecure\Entity\NewOrder;
use SandwaveIo\FSecure\Entity\Order;
use SandwaveIo\FSecure\Entity\OrderCollection;
use SandwaveIo\FSecure\Entity\SuspendOrder;
use SandwaveIo\FSecure\Entity\SuspendOrderCollection;

final class OrderClient
{
    private const GET_ORDERS = 'licenses/get_orders';
    private const CREATE_ORDER = 'licenses/new_order';
    private const SUSPEND_ORDER = 'licenses/suspend_order';

    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function get(): OrderCollection
    {
        return $this->client->getEntity(self::GET_ORDERS, OrderCollection::class);
    }

    public function create(NewOrder $newOrder): Order
    {
        return $this->client->post(self::CREATE_ORDER, $newOrder, Order::class);
    }

    public function suspend(SuspendOrder $suspendOrder): SuspendOrderCollection
    {
        return $this->client->post(self::SUSPEND_ORDER, $suspendOrder, SuspendOrderCollection::class);
    }
}
