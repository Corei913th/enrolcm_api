<?php

namespace Tests\Unit\Traits;

use Tests\TestCase;
use App\Traits\HasActivityLogger;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use Mockery;

class HasActivityLoggerTest extends TestCase
{
  private $service;
  private $loggerMock;

  protected function setUp(): void
  {
    parent::setUp();

    $this->loggerMock = Mockery::mock(ActivityLoggerService::class);

    // Créer une classe anonyme qui utilise le trait
    $this->service = new class($this->loggerMock) {
      use HasActivityLogger;

      public function __construct(ActivityLoggerService $logger)
      {
        $this->logger = $logger;
      }

      // Exposer les méthodes protégées pour les tests
      public function testLogCreate(string $entity, string $entityId, ?array $data = null): void
      {
        $this->logCreate($entity, $entityId, $data);
      }

      public function testLogUpdate(string $entity, string $entityId, ?array $data = null): void
      {
        $this->logUpdate($entity, $entityId, $data);
      }

      public function testLogDelete(string $entity, string $entityId): void
      {
        $this->logDelete($entity, $entityId);
      }

      public function testLogView(string $entity, string $entityId): void
      {
        $this->logView($entity, $entityId);
      }

      public function testLogStatusChange(string $entity, string $entityId, bool $newStatus): void
      {
        $this->logStatusChange($entity, $entityId, $newStatus);
      }

      public function testLogOperation(string $action, string $entity, ?string $entityId = null, ?array $data = null): void
      {
        $this->logOperation($action, $entity, $entityId, $data);
      }
    };
  }

  protected function tearDown(): void
  {
    Mockery::close();
    parent::tearDown();
  }

  public function test_log_create_calls_logger_with_correct_parameters()
  {
    $this->loggerMock
      ->shouldReceive('logActivity')
      ->once()
      ->with('create', 'user', '123', null);

    $this->service->testLogCreate('user', '123');
  }

  public function test_log_create_with_data()
  {
    $data = ['email' => 'test@example.com'];

    $this->loggerMock
      ->shouldReceive('logActivity')
      ->once()
      ->with('create', 'user', '123', $data);

    $this->service->testLogCreate('user', '123', $data);
  }

  public function test_log_update_calls_logger()
  {
    $this->loggerMock
      ->shouldReceive('logActivity')
      ->once()
      ->with('update', 'user', '123', null);

    $this->service->testLogUpdate('user', '123');
  }

  public function test_log_delete_calls_logger()
  {
    $this->loggerMock
      ->shouldReceive('logActivity')
      ->once()
      ->with('delete', 'user', '123');

    $this->service->testLogDelete('user', '123');
  }

  public function test_log_view_calls_logger()
  {
    $this->loggerMock
      ->shouldReceive('logActivity')
      ->once()
      ->with('view', 'user', '123');

    $this->service->testLogView('user', '123');
  }

  public function test_log_status_change_activate()
  {
    $this->loggerMock
      ->shouldReceive('logActivity')
      ->once()
      ->with('activate', 'user', '123', ['status' => true]);

    $this->service->testLogStatusChange('user', '123', true);
  }

  public function test_log_status_change_deactivate()
  {
    $this->loggerMock
      ->shouldReceive('logActivity')
      ->once()
      ->with('deactivate', 'user', '123', ['status' => false]);

    $this->service->testLogStatusChange('user', '123', false);
  }

  public function test_log_operation_custom_action()
  {
    $data = ['reason' => 'test'];

    $this->loggerMock
      ->shouldReceive('logActivity')
      ->once()
      ->with('custom_action', 'user', '123', $data);

    $this->service->testLogOperation('custom_action', 'user', '123', $data);
  }
}
