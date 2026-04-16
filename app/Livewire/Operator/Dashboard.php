<?php

namespace App\Livewire\Operator;

use App\Events\OperatorAccepted;
use App\Models\SupportSession;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Polling;
use Livewire\Component;

#[Polling(3000)]
class Dashboard extends Component
{
    public $generatedLink = '';
    public $customerName = '';
    public $customerEmail = '';
    public $customerPhone = '';
    public $waitingSessions = [];
    public $showLinkModal = false;

    public function mount()
    {
        $this->loadWaitingSessions();
    }

    public function generateLink()
    {
        $session = SupportSession::create([
            'operator_id' => auth()->id(),
            'status' => 'pending',
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
            'customer_phone' => $this->customerPhone,
        ]);

        $this->generatedLink = route('support.waiting-room', $session->uuid);
        $this->showLinkModal = true;
        $this->customerName = '';
        $this->customerEmail = '';
        $this->customerPhone = '';

        $this->dispatch('link-generated', link: $this->generatedLink);
    }

    public function closeModal()
    {
        $this->showLinkModal = false;
        $this->generatedLink = '';
    }

    public function copyLink()
    {
        $this->dispatch('copy-to-clipboard', text: $this->generatedLink);
    }

    public function loadWaitingSessions()
    {
        $this->waitingSessions = SupportSession::where('status', 'waiting')
            ->with('operator')
            ->latest()
            ->get()
            ->toArray();
    }

    public function acceptCall($sessionUuid)
    {
        $accepted = DB::transaction(function () use ($sessionUuid) {
            $session = SupportSession::where('uuid', $sessionUuid)
                ->where('status', 'waiting')
                ->whereNull('accepted_by')
                ->lockForUpdate()
                ->first();

            if (!$session) {
                return false;
            }

            $session->update([
                'accepted_by'        => auth()->id(),
                'status'             => 'active',
                'operator_joined_at' => now(),
            ]);

            return $session;
        });

        if (!$accepted) {
            session()->flash('error', 'Bu zəng artıq başqa operator tərəfindən qəbul edilib.');
            return;
        }

        broadcast(new OperatorAccepted($accepted));

        $this->redirect(route('support.video-room', $accepted->uuid));
    }

    public function render()
    {
        $this->loadWaitingSessions();
        return view('livewire.operator.dashboard');
    }
}
