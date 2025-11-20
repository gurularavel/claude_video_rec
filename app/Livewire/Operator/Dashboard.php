<?php

namespace App\Livewire\Operator;

use App\Events\CustomerWaiting;
use App\Models\SupportSession;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    public $generatedLink = '';
    public $customerName = '';
    public $customerEmail = '';
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
        ]);

        $this->generatedLink = route('support.waiting-room', $session->uuid);
        $this->showLinkModal = true;
        $this->customerName = '';
        $this->customerEmail = '';

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

    #[On('echo:operators,CustomerWaiting')]
    public function customerWaiting($data)
    {
        $this->loadWaitingSessions();
        $this->dispatch('customer-waiting', data: $data);
    }

    #[On('echo:operators,OperatorAccepted')]
    public function operatorAccepted($data)
    {
        $this->loadWaitingSessions();
    }

    public function loadWaitingSessions()
    {
        $this->waitingSessions = SupportSession::where('status', 'waiting')
            ->with('operator')
            ->latest()
            ->get()
            ->toArray();
    }

    public function acceptCall($sessionId)
    {
        $this->redirect(route('support.video-room', $sessionId));
    }

    public function render()
    {
        return view('livewire.operator.dashboard');
    }
}
