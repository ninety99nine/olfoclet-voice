<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Support\Facades\Log;

class PineconeService
{
    /**
     * @var Client Control plane client for management operations
     */
    protected $controlClient;

    /**
     * @var Client Data plane client for vector operations
     */
    protected $dataClient;

    /**
     * @var string Index endpoint (host)
     */
    protected $indexEndpoint;

    /**
     * PineconeService constructor.
     */
    public function __construct()
    {
        // Client for control plane operations (e.g., getIndexDetails, listNamespaces)
        $this->controlClient = new Client([
            'base_uri' => 'https://api.pinecone.io/',
            'headers' => [
                'Api-Key' => config('services.pinecone.api_key'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'timeout' => 10, // Add timeout for control plane requests
        ]);

        // Fetch the index endpoint (host) for data operations
        $indexDetails = $this->getIndexDetails();
        $this->indexEndpoint = 'https://'.$indexDetails['host'];
        Log::info('PineconeService: Index endpoint set', ['index_endpoint' => $this->indexEndpoint]);

        // Client for data plane operations (e.g., upsert, query, fetch, delete)
        $this->dataClient = new Client([
            'base_uri' => $this->indexEndpoint,
            'headers' => [
                'Api-Key' => config('services.pinecone.api_key'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-Pinecone-API-Version' => '2025-01', // Specify API version as per docs
            ],
            'timeout' => 10, // Add timeout to prevent hanging
            'handler' => \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\CurlHandler()),
            'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
                Log::info('PineconeService: Request URL', ['url' => (string) $stats->getEffectiveUri()]);
                if ($stats->hasResponse()) {
                    Log::info('PineconeService: Request completed', [
                        'response_time_ms' => $stats->getTransferTime() * 1000,
                        'status' => $stats->getResponse()->getStatusCode(),
                    ]);
                } else {
                    Log::error('PineconeService: Request failed or timed out', [
                        'error' => $stats->getHandlerErrorData() ?? 'No additional error data',
                    ]);
                }
            },
        ]);
    }

    /**
     * Get the namespace for an organization.
     *
     * @param string $organizationId
     * @return string
     */
    protected function getNamespace(string $organizationId): string
    {
        return 'org-' . $organizationId;
    }

    /**
     * Fetch details of the Pinecone index.
     *
     * @return array
     * @throws \Exception
     */
    public function getIndexDetails(): array
    {
        try {
            Log::info('PineconeService: Fetching index details', [
                'index' => config('services.pinecone.index'),
                'url' => 'https://api.pinecone.io/indexes/' . config('services.pinecone.index'),
            ]);

            $response = $this->controlClient->get('indexes/' . config('services.pinecone.index'));
            $result = json_decode($response->getBody()->getContents(), true);

            Log::info('PineconeService: Index details fetched successfully', [
                'index' => config('services.pinecone.index'),
                'details' => $result,
            ]);

            return $result;
        } catch (GuzzleException $e) {
            Log::error('PineconeService: Failed to fetch index details from Pinecone', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'code' => $e->getCode(),
            ]);
            throw new \Exception('Failed to fetch index details from Pinecone: ' . $e->getMessage());
        }
    }

    /**
     * Upsert a vector in Pinecone for a specific organization.
     *
     * @param string $id
     * @param array $vector
     * @param string $organizationId
     * @param array $metadata
     * @return void
     * @throws \Exception
     */
    public function upsertVector(string $id, array $vector, string $organizationId, array $metadata = []): void
    {
        $payload = [
            'vectors' => [
                [
                    'id' => $id,
                    'values' => $vector,
                    'metadata' => array_merge($metadata, ['organization_id' => $organizationId]),
                ],
            ],
            'namespace' => $this->getNamespace($organizationId),
        ];

        Log::info('PineconeService: Preparing to upsert vector', [
            'organization_id' => $organizationId,
            'namespace' => $this->getNamespace($organizationId),
            'vector_id' => $id,
            'payload' => $payload,
        ]);

        try {
            $response = $this->dataClient->post('vectors/upsert', [
                'json' => $payload,
            ]);

            Log::info('PineconeService: Successfully upserted vector', [
                'organization_id' => $organizationId,
                'vector_id' => $id,
                'response_status' => $response->getStatusCode(),
                'response_body' => $response->getBody()->getContents(),
                'lsn' => $response->getHeaderLine('x-pinecone-max-indexed-lsn') ?: 'Not provided',
            ]);
        } catch (GuzzleException $e) {
            Log::error('PineconeService: Failed to upsert vector in Pinecone', [
                'organization_id' => $organizationId,
                'vector_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'code' => $e->getCode(),
            ]);
            throw new \Exception('Failed to upsert vector in Pinecone: ' . $e->getMessage());
        }
    }

    /**
     * Query Pinecone to retrieve the top-k similar vectors for a specific organization, with optional re-ranking.
     *
     * @param array $vector
     * @param string $queryText
     * @param string $organizationId
     * @param int $topK
     * @param ?array $filter
     * @param ?string $rerankerId
     * @return array
     * @throws \Exception
     */
    public function queryVectors(array $vector, string $queryText, string $organizationId, int $topK = 5, ?array $filter = null, ?string $rerankerId = null): array
    {
        $payload = [
            'vector' => array_values($vector),
            'top_k' => $topK,
            'include_metadata' => true,
            'include_values' => false,
            'filter' => $filter, // e.g., ['tags' => [ '$in' => ['123'] ]]
            'namespace' => $this->getNamespace($organizationId),
        ];

        // Add reranker if specified
        if ($rerankerId) {
            $payload['rerank'] = [
                'reranker_id' => $rerankerId,
                'query' => $queryText, // Include the original query text for hybrid search
                'top_k' => $topK, // Ensure the reranker returns the same number of results
                'rank_fields' => ['content', 'title'], // Specify fields for the reranker to focus on
            ];
        }

        Log::info('PineconeService: Preparing to query vectors', [
            'organization_id' => $organizationId,
            'namespace' => $this->getNamespace($organizationId),
            'payload' => $payload,
        ]);

        try {
            $response = $this->dataClient->post('query', [
                'json' => $payload,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            $matches = $result['matches'] ?? [];

            Log::info('PineconeService: Successfully queried vectors', [
                'organization_id' => $organizationId,
                'response_status' => $response->getStatusCode(),
                'response_body' => $response->getBody()->getContents(),
                'matches' => $matches,
                'lsn' => $response->getHeaderLine('x-pinecone-max-indexed-lsn') ?: 'Not provided',
            ]);

            return $matches;
        } catch (GuzzleException $e) {
            Log::error('PineconeService: Failed to query vectors in Pinecone', [
                'organization_id' => $organizationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'code' => $e->getCode(),
            ]);
            throw new \Exception('Failed to query vectors in Pinecone: ' . $e->getMessage());
        }
    }

    /**
     * Delete a vector from Pinecone for a specific organization.
     *
     * @param string $id
     * @param string $organizationId
     * @return void
     * @throws \Exception
     */
    public function deleteVector(string $id, string $organizationId): void
    {
        $payload = [
            'ids' => [$id],
            'namespace' => $this->getNamespace($organizationId),
        ];

        Log::info('PineconeService: Preparing to delete vector', [
            'organization_id' => $organizationId,
            'vector_id' => $id,
            'namespace' => $this->getNamespace($organizationId),
            'payload' => $payload,
        ]);

        try {
            $response = $this->dataClient->post('vectors/delete', [
                'json' => $payload,
            ]);

            Log::info('PineconeService: Successfully deleted vector', [
                'organization_id' => $organizationId,
                'vector_id' => $id,
                'response_status' => $response->getStatusCode(),
                'response_body' => $response->getBody()->getContents(),
                'lsn' => $response->getHeaderLine('x-pinecone-max-indexed-lsn') ?: 'Not provided',
            ]);
        } catch (GuzzleException $e) {
            Log::error('PineconeService: Failed to delete vector in Pinecone', [
                'organization_id' => $organizationId,
                'vector_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'code' => $e->getCode(),
            ]);
            throw new \Exception('Failed to delete vector in Pinecone: ' . $e->getMessage());
        }
    }

    /**
     * Fetch a vector from Pinecone by its ID for a specific organization.
     *
     * @param string $id
     * @param string $organizationId
     * @return array|null
     * @throws \Exception
     */
    public function fetchVector(string $id, string $organizationId): ?array
    {
        $payload = [
            'ids' => [$id],
            'namespace' => $this->getNamespace($organizationId),
        ];

        Log::info('PineconeService: Preparing to fetch vector', [
            'organization_id' => $organizationId,
            'vector_id' => $id,
            'namespace' => $this->getNamespace($organizationId),
            'payload' => $payload,
        ]);

        try {
            $response = $this->dataClient->post('vectors/fetch', [
                'json' => $payload,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            $vector = $result['vectors'][$id] ?? null;

            Log::info('PineconeService: Successfully fetched vector', [
                'organization_id' => $organizationId,
                'vector_id' => $id,
                'response_status' => $response->getStatusCode(),
                'response_body' => $response->getBody()->getContents(),
                'vector' => $vector,
                'lsn' => $response->getHeaderLine('x-pinecone-max-indexed-lsn') ?: 'Not provided',
            ]);

            return $vector;
        } catch (GuzzleException $e) {
            Log::error('PineconeService: Failed to fetch vector in Pinecone', [
                'organization_id' => $organizationId,
                'vector_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'code' => $e->getCode(),
            ]);
            throw new \Exception('Failed to fetch vector in Pinecone: ' . $e->getMessage());
        }
    }

    /**
     * List all namespaces in the Pinecone index.
     *
     * @return array
     * @throws \Exception
     */
    public function listNamespaces(): array
    {
        try {
            Log::info('PineconeService: Preparing to list namespaces', [
                'index' => config('services.pinecone.index'),
                'url' => 'https://api.pinecone.io/databases/' . config('services.pinecone.index') . '/namespaces',
            ]);

            $response = $this->controlClient->get('databases/' . config('services.pinecone.index') . '/namespaces');
            $result = json_decode($response->getBody()->getContents(), true);
            $namespaces = $result['namespaces'] ?? [];

            Log::info('PineconeService: Successfully listed namespaces', [
                'index' => config('services.pinecone.index'),
                'response_status' => $response->getStatusCode(),
                'response_body' => $response->getBody()->getContents(),
                'namespaces' => $namespaces,
            ]);

            return $namespaces;
        } catch (GuzzleException $e) {
            Log::error('PineconeService: Failed to list namespaces in Pinecone', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'code' => $e->getCode(),
            ]);
            throw new \Exception('Failed to list namespaces in Pinecone: ' . $e->getMessage());
        }
    }

    /**
     * Delete a namespace and all its vectors for a specific organization.
     *
     * @param string $organizationId
     * @return void
     * @throws \Exception
     */
    public function deleteNamespace(string $organizationId): void
    {
        $payload = [
            'deleteAll' => true,
            'namespace' => $this->getNamespace($organizationId),
        ];

        Log::info('PineconeService: Preparing to delete namespace', [
            'organization_id' => $organizationId,
            'namespace' => $this->getNamespace($organizationId),
            'payload' => $payload,
        ]);

        try {
            $response = $this->dataClient->post('vectors/delete', [
                'json' => $payload,
            ]);

            Log::info('PineconeService: Successfully deleted namespace', [
                'organization_id' => $organizationId,
                'response_status' => $response->getStatusCode(),
                'response_body' => $response->getBody()->getContents(),
                'lsn' => $response->getHeaderLine('x-pinecone-max-indexed-lsn') ?: 'Not provided',
            ]);
        } catch (GuzzleException $e) {
            Log::error('PineconeService: Failed to delete namespace in Pinecone', [
                'organization_id' => $organizationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'code' => $e->getCode(),
            ]);
            throw new \Exception('Failed to delete namespace in Pinecone: ' . $e->getMessage());
        }
    }
}
