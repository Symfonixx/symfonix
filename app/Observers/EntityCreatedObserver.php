<?php

namespace App\Observers;

use App\Support\Notifications\EntityCreatedNotificationDispatcher;
use Illuminate\Database\Eloquent\Model;

class EntityCreatedObserver
{
    public function __construct(
        private readonly EntityCreatedNotificationDispatcher $dispatcher,
    ) {}

    public function created(Model $model): void
    {
        $this->dispatcher->handleCreated($model);
    }
}
