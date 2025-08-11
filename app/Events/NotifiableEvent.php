<?php
// app/Events/NotifiableEvent.php
namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class NotifiableEvent
{
    use Dispatchable, SerializesModels;

    abstract public function getNotificationData(): array;
    abstract public function getUserIds(): array;
}