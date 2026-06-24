<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\CRM\Models\ContactForm;

class AdminLayout extends Component
{
    protected \Illuminate\Contracts\Auth\Authenticatable|null|\App\Models\User $user;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $recentMessages = collect();
        $unreadMessageCount = 0;

        if ($this->user?->can('CRM Management')) {
            $recentMessages = ContactForm::query()
                ->latest()
                ->limit(5)
                ->get(['id', 'name', 'subject', 'created_at']);

            $unreadMessageCount = ContactForm::query()
                ->where('created_at', '>=', now()->subDays(7))
                ->count();
        }

        return view('components.admin-layout', [
            'user' => $this->user,
            'recentMessages' => $recentMessages,
            'unreadMessageCount' => $unreadMessageCount,
        ]);
    }
}
