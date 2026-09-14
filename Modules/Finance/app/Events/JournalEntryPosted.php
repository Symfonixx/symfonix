<?php

namespace Modules\Finance\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Finance\Models\JournalEntry;

class JournalEntryPosted
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly JournalEntry $entry) {}
}
