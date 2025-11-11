<?php

namespace App\Http\Traits;

use App\Exceptions\ExceptionHandler;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Google\Auth\Credentials\ServiceAccountCredentials;

trait FireStoreTrait
{
  protected $fireStoreBaseUrl;
  protected $fireStoreAccessToken;

  protected function initializeFireStore(): void
  {
    $projectId = config('services.firebase.project_id');
    $databaseId = config('services.firebase.database_id', '(default)');
    $this->fireStoreBaseUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/{$databaseId}/documents";
    $this->fireStoreAccessToken = $this->getFireStoreAccessToken();
  }

  protected function getFireStoreAccessToken(): string
  {
    return Cache::remember('fire_store_access_token', now()->addMinutes(50), function () {
      $serviceAccountPath = config('services.firebase.credentials');
      if (!$serviceAccountPath || !file_exists($serviceAccountPath)) {
        throw new ExceptionHandler('Service account credentials file not found at: ' . $serviceAccountPath);
      }

      $scopes = ['https://www.googleapis.com/auth/datastore'];
      $credentials = new ServiceAccountCredentials($scopes, $serviceAccountPath);
      $token = $credentials->fetchAuthToken();

      if (!isset($token['access_token'])) {
        throw new ExceptionHandler('Failed to obtain FireStore access token', 400);
      }

      return $token['access_token'];
    });
  }

  protected function fireStoreRequest(string $method, string $endpoint, array $data = [], int $retries = 2): array
  {
    $attempt = 0;
    $this->initializeFireStore();
    $url = $this->fireStoreBaseUrl . $endpoint;

    while ($attempt <= $retries) {
      try {
        $response = Http::withHeaders([
          'Authorization' => 'Bearer ' . $this->fireStoreAccessToken,
          'Content-Type' => 'application/json',
        ])->{$method}($url, $data);


        if ($response->failed()) {
          $errorBody = $response->json();
          if ($response->status() === 409 && isset($errorBody['error']['status']) && $errorBody['error']['status'] === 'ALREADY_EXISTS') {
            if ($method === 'post' && strpos($endpoint, ':') === false) {
              $parts = explode('/?', $endpoint);
              $pathParts = explode('/', $parts[0]);
              $collection = $pathParts[1];
              parse_str($parts[1] ?? '', $query);
              $documentId = $query['documentId'] ?? null;
              if ($documentId) {
                return $this->fireStoreUpdateDocument($collection, $documentId, $data['fields'], false, true);
              }
            }
          }

          throw new ExceptionHandler($response->body(), $response->status());
        }

        return $response->json() ?: [];
      } catch (\Exception $e) {
        throw new ExceptionHandler($e->getMessage(), $e->getCode());
      }
    }

    return [];
  }

  public function fireStoreGetDocument(string $collection, string $documentId, ?array $defaultData = null, bool $createIfMissing = true): ?array
  {
    $endpoint = "/{$collection}/{$documentId}";
    try {
      $response = $this->fireStoreRequest('get', $endpoint);
      return $this->parseFireStoreDocument($response);
    } catch (ExceptionHandler $e) {
        if ($e->getCode() === 404) {
            if ($createIfMissing && $defaultData !== null) {
                return $this->fireStoreAddDocument($collection, $defaultData, $documentId);
            }
            return null;
        }


      throw $e;
    }
  }

  public function fireStoreAddDocument(string $collection, array $data, ?string $documentId = null, array $subCollections = []): array
  {
    $endpoint = $documentId ? "/{$collection}/?documentId={$documentId}" : "/{$collection}";
    $payload = ['fields' => $this->formatFireStoreFields($data)];
    $response = $this->fireStoreRequest('post', $endpoint, $payload);
    $doc = $this->parseFireStoreDocument($response);

    $docId = $documentId ?? $doc['id'] ?? null;
    if ($docId && !empty($subCollections)) {
      $this->createSubCollections($collection, $docId, $subCollections);
    }

    return $doc;
  }

  protected function createSubCollections(string $parentCollection, string $parentDocId, array $subCollections): void
  {
    $parentDoc = $this->fireStoreGetDocument($parentCollection, $parentDocId);
    if (empty($parentDoc)) {
      throw new ExceptionHandler("Parent document {$parentCollection}/{$parentDocId} does not exist", 404);
    }

    foreach ($subCollections as $subCollection) {
      if (!isset($subCollection['name']) || !isset($subCollection['documents'])) {
        throw new ExceptionHandler('Sub collection must specify "name" and "documents"');
      }

      $subCollectionName = $subCollection['name'];
      $documents = $subCollection['documents'];
      foreach ($documents as $doc) {
        if (!isset($doc['id']) || !isset($doc['data'])) {
          throw new ExceptionHandler('Each sub collection document must specify "id" and "data"');
        }

        $subDocId = $doc['id'];
        $subDocData = $doc['data'];
        $subSubCollections = $doc['sub_collections'] ?? [];
        $subCollectionPath = "{$parentCollection}/{$parentDocId}/{$subCollectionName}";
        $existingSubDoc = $this->fireStoreGetDocument($subCollectionPath, $subDocId, $subDocData);
      }
    }
  }

  public function fireStoreUpdateDocument(
    string $collection,
    string $documentId,
    array $data,
    bool $merge = true,
    bool $formatted = false,
    string $arrayOperation = 'push',
    array $subCollections = []
  ): array {
    $endpoint = "/{$collection}/{$documentId}";
    if ($merge) {
      $fieldPaths = $this->getFieldPaths($data);
      $endpoint .= '?updateMask.fieldPaths=' . implode('&updateMask.fieldPaths=', array_map('urlencode', $fieldPaths));
    }
    $payload = ['fields' => $formatted ? $data : $this->formatFireStoreFields($data)];
    $response = $this->fireStoreRequest('patch', $endpoint, $payload);
    $doc = $this->parseFireStoreDocument($response);

    if (!empty($subCollections)) {
      $this->createSubCollections($collection, $documentId, $subCollections);
    }

    return $doc;
  }

  protected function formatFireStoreValue($value): array
  {
    if (is_null($value)) {
      return ['nullValue' => null];
    } elseif (is_bool($value)) {
      return ['booleanValue' => $value];
    } elseif (is_int($value)) {
      return ['integerValue' => $value];
    } elseif (is_float($value)) {
      return ['doubleValue' => $value];
    } elseif (is_array($value)) {
      if (array_keys($value) !== range(0, count($value) - 1)) {
        return ['mapValue' => ['fields' => $this->formatFireStoreFields($value)]];
      }
      return ['arrayValue' => ['values' => array_map(fn($v) => $this->formatFireStoreValue($v), $value)]];
    } elseif ($value instanceof \DateTimeInterface) {
      return ['timestampValue' => $value->format('Y-m-d\TH:i:s.v\Z')];
    }
    return ['stringValue' => (string) $value];
  }

  protected function formatFireStoreFields(array $data): array
  {
    $fields = [];
    foreach ($data as $key => $value) {
      $fields[$key] = $this->formatFireStoreValue($value);
    }
    return $fields;
  }

  protected function decodeParseFireStoreDocument(array $doc): array
  {
    $data = [];
    if (isset($doc['name'])) {
      $data['id'] = basename($doc['name']);
    } elseif (isset($doc['id'])) {
      $data['id'] = $doc['id'];
    }

    if (isset($doc['fields']) && is_array($doc['fields'])) {
      foreach ($doc['fields'] as $key => $value) {
        $data[$key] = $this->parseFireStoreValue($value);
      }
    } elseif (is_array($doc) && !empty($doc)) {
      foreach ($doc as $key => $value) {
        if ($key !== 'id') {
          $data[$key] = $this->parseFireStoreValue($value);
        }
      }
    }

    return $data;
  }

  protected function parseFireStoreDocument(array $doc): array
  {
    if (isset($doc['name'])) {
      $data = ['id' => basename($doc['name'])];
      foreach ($doc['fields'] ?? [] as $key => $value) {
        if (isset($value['stringValue'])) {
          $data[$key] = $value['stringValue'];
        } elseif (isset($value['arrayValue'])) {
          $data[$key] = array_map(fn($v) => $v['stringValue'] ?? $v, $value['arrayValue']['values'] ?? []);
        } elseif (isset($value['mapValue'])) {
          $nestedFields = $value['mapValue']['fields'] ?? [];
          $nestedData = [];
          foreach ($nestedFields as $nestedKey => $nestedValue) {
            $nestedData[$nestedKey] = $this->parseFireStoreValue($nestedValue);
          }
          $data[$key] = $nestedData;
        } elseif (isset($value['booleanValue'])) {
          $data[$key] = $value['booleanValue'];
        } elseif (isset($value['integerValue'])) {
          $data[$key] = (int) $value['integerValue'];
        } elseif (isset($value['doubleValue'])) {
          $data[$key] = (float) $value['doubleValue'];
        } elseif (isset($value['timestampValue'])) {
          $data[$key] = $value['timestampValue'];
        } elseif (isset($value['nullValue'])) {
          $data[$key] = null;
        } else {
          $data[$key] = $value;
        }
      }
      return $data;
    }

    return [];
  }

  protected function parseFireStoreValue($value): mixed
  {
    if(is_array($value)) {
      if(array_key_exists('nullValue',$value ?? [])) {
        return null;
      }
    }

    if (isset($value['nullValue'])) {
      return null;
    } elseif (isset($value['booleanValue'])) {
      return $value['booleanValue'];
    } elseif (isset($value['integerValue'])) {
      return (int) $value['integerValue'];
    } elseif (isset($value['doubleValue'])) {
      return (float) $value['doubleValue'];
    } elseif (isset($value['stringValue'])) {
      return $value['stringValue'];
    } elseif (isset($value['timestampValue'])) {
      return $value['timestampValue'];
    } elseif (isset($value['arrayValue'])) {
      $values = $value['arrayValue']['values'] ?? [];
      return array_map([$this, 'parseFireStoreValue'], $values);
    } elseif (isset($value['mapValue'])) {
      $fields = $value['mapValue']['fields'] ?? [];
      $result = [];
      foreach ($fields as $key => $nestedValue) {
        $result[$key] = $this->parseFireStoreValue($nestedValue);
      }
      return $result;
    }
    if (is_array($value)) {
      $result = [];
      foreach ($value as $key => $nestedValue) {
        $result[$key] = $this->parseFireStoreValue($nestedValue);
      }
      return $result;
    }
    return $value;
  }

  public function fireStoreDeleteDocument(string $collection, string $documentId): void
  {
    $endpoint = "/{$collection}/{$documentId}";
    $this->fireStoreRequest('delete', $endpoint);
  }

  public function fireStoreQueryCollection(string $collection, array $filters = [], array $options = []): array
  {
    $structuredQuery = [
      'structuredQuery' => [
        'from' => [['collectionId' => $collection]],
      ],
    ];

    if (!empty($filters)) {
      $where = [];
      foreach ($filters as $filter) {
        $this->validateFireStoreWhereClause($filter);
        $where[] = [
          'fieldFilter' => [
            'field' => ['fieldPath' => $filter[0]],
            'op' => strtoupper($filter[1]),
            'value' => $this->formatFireStoreValue($filter[2]),
          ],
        ];
      }
      $structuredQuery['structuredQuery']['where'] = [
        'compositeFilter' => [
          'op' => 'AND',
          'filters' => $where,
        ],
      ];
    }

    $response = $this->fireStoreRequest('post', ':runQuery', $structuredQuery);
    return $this->parseFireStoreDocuments($response);
  }

  protected function parseFireStoreDocuments(array $response): array
  {
    $documents = [];
    foreach ($response as $docWrapper) {
      if (!isset($docWrapper['document'])) {
        continue;
      }
      $documents[] = $this->parseFireStoreDocument($docWrapper['document']);
    }
    return $documents;
  }

  public function fireStoreBatchWrite(array $operations): void
  {
    $writes = [];
    foreach ($operations as $op) {
      if (!isset($op['type']) || !isset($op['collection']) || !isset($op['documentId'])) {
        throw new ExceptionHandler('Each operation must specify type, collection, and documentId', 400);
      }

      $path = "projects/" . config('services.firebase.project_id') . "/databases/" . config('services.firebase.database_id', '(default)') . "/documents/{$op['collection']}/{$op['documentId']}";
      if ($op['type'] === 'create' || $op['type'] === 'update') {
        $writes[] = [
          'update' => [
            'name' => $path,
            'fields' => $this->formatFireStoreFields($op['data'] ?? []),
          ],
        ];
      } elseif ($op['type'] === 'delete') {
        $writes[] = ['delete' => $path];
      }
    }

    $payload = ['writes' => $writes];
    $this->fireStoreRequest('post', ':batchWrite', $payload);
  }

  public function fireStoreListCollections(): array
  {
    $response = $this->fireStoreRequest('get', ':listCollectionIds');
    return $response['collectionIds'] ?? [];
  }

  protected function validateFireStoreWhereClause(array $where): void
  {
    if (count($where) !== 3) {
      throw new ExceptionHandler('Where clause must be an array of [field, operator, value]', 400);
    }

    $validOperators = ['EQUAL', 'LESS_THAN', 'GREATER_THAN', 'LESS_THAN_OR_EQUAL', 'GREATER_THAN_OR_EQUAL', 'ARRAY_CONTAINS', 'IN', 'ARRAY_CONTAINS_ANY'];
    if (!in_array(strtoupper($where[1]), $validOperators)) {
      throw new ExceptionHandler('Invalid operator: ' . $where[1], 400);
    }
  }

  protected function getFieldPaths(array $data, string $prefix = ''): array
  {
    $fieldPaths = [];
    foreach ($data as $key => $value) {
      $fieldPath = $prefix ? "{$prefix}.{$key}" : $key;
      if (is_array($value) && !array_is_list($value)) {
        $fieldPaths = array_merge($fieldPaths, $this->getFieldPaths($value, $fieldPath));
      } else {
        $fieldPaths[] = $fieldPath;
      }
    }
    return $fieldPaths;
  }
}
