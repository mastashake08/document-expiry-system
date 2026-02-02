<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

class ClientService
{
    /**
     * Get all clients.
     */
    public function all(): Collection
    {
        return Client::all();
    }

    /**
     * Create a new client.
     */
    public function create(array $data): Client
    {
        return Client::create($data);
    }

    /**
     * Find a client by ID.
     */
    public function find(int $id): ?Client
    {
        return Client::find($id);
    }

    /**
     * Update a client.
     */
    public function update(Client $client, array $data): Client
    {
        $client->update($data);

        return $client->fresh();
    }

    /**
     * Delete a client.
     */
    public function delete(Client $client): bool
    {
        return $client->delete();
    }
}
