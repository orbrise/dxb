<?php

namespace App\Livewire;

use App\Models\Call;
use Livewire\Component;

class CallLog extends Component
{
    public string $filter = 'all'; // all | missed | incoming | outgoing

    public function mount(): void
    {
        if (!auth()->check()) {
            redirect()->route('sign-in');
            return;
        }

        // Any missed call the user now sees on this page is acknowledged.
        Call::where('callee_id', auth()->id())
            ->where('status', 'missed')
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);
    }

    public function setFilter(string $filter): void
    {
        $this->filter = in_array($filter, ['all', 'missed', 'incoming', 'outgoing'])
            ? $filter
            : 'all';
    }

    public function render()
    {
        $userId = auth()->id();
        $q = Call::with(['caller', 'callee'])
            ->where(function ($q) use ($userId) {
                $q->where('caller_id', $userId)->orWhere('callee_id', $userId);
            });

        match ($this->filter) {
            'missed'   => $q->where('callee_id', $userId)->where('status', 'missed'),
            'incoming' => $q->where('callee_id', $userId),
            'outgoing' => $q->where('caller_id', $userId),
            default    => null,
        };

        $calls = $q->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(function ($call) use ($userId) {
                $isOutgoing = $call->caller_id === $userId;
                $peer = $isOutgoing ? $call->callee : $call->caller;
                return [
                    'id' => $call->id,
                    'is_outgoing' => $isOutgoing,
                    'type' => $call->type,
                    'status' => $call->status,
                    'peer_id' => $peer?->id,
                    'peer_name' => $peer?->name ?? $peer?->email ?? 'Unknown',
                    'peer_avatar' => $peer?->avatar ?? null,
                    'started_at' => $call->started_at ?? $call->created_at,
                    'duration' => $call->duration,
                ];
            });

        return view('livewire.call-log', [
            'calls' => $calls,
            'filter' => $this->filter,
        ])->layout('components.layouts.app-evoory');
    }
}
