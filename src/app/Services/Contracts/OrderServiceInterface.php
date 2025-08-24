<?php
namespace App\Services\Contracts;
use App\Models\Order;

interface OrderServiceInterface {
  /** @return array{0: ?Order, 1: ?\Illuminate\Http\JsonResponse} */
  public function createForUser(int $userId): array;
}
