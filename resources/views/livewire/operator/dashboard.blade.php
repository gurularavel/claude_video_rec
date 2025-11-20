<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Operator Dashboard</h4>
                    <span class="badge bg-success">Available</span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5>Generate Support Link</h5>
                        <form wire:submit.prevent="generateLink">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" wire:model="customerName" class="form-control" placeholder="Customer Name (Optional)">
                                </div>
                                <div class="col-md-4">
                                    <input type="email" wire:model="customerEmail" class="form-control" placeholder="Customer Email (Optional)">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        Generate Link
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if($showLinkModal)
                    <div class="alert alert-success">
                        <h6>Support Link Generated!</h6>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $generatedLink }}" readonly id="generated-link">
                            <button class="btn btn-outline-secondary" type="button" wire:click="copyLink">
                                Copy
                            </button>
                        </div>
                        <button class="btn btn-sm btn-secondary mt-2" wire:click="closeModal">Close</button>
                    </div>
                    @endif

                    <hr>

                    <h5>Waiting Customers</h5>
                    @if(count($waitingSessions) > 0)
                        <div class="list-group">
                            @foreach($waitingSessions as $session)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            {{ $session['customer_name'] ?? 'Anonymous Customer' }}
                                        </h6>
                                        <small class="text-muted">
                                            Waiting since: {{ \Carbon\Carbon::parse($session['customer_joined_at'])->diffForHumans() }}
                                        </small>
                                    </div>
                                    <button wire:click="acceptCall('{{ $session['uuid'] }}')" class="btn btn-success">
                                        Accept Call
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No customers waiting</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('copy-to-clipboard', (event) => {
        navigator.clipboard.writeText(event.text);
        alert('Link copied to clipboard!');
    });

    $wire.on('customer-waiting', (event) => {
        // Show notification
        if (Notification.permission === 'granted') {
            new Notification('Customer Waiting!', {
                body: 'A customer is waiting for support',
                icon: '/favicon.ico'
            });
        }

        // Play sound
        const audio = new Audio('/notification.mp3');
        audio.play().catch(e => console.log('Could not play audio'));
    });

    // Request notification permission
    if (Notification.permission === 'default') {
        Notification.requestPermission();
    }
</script>
@endscript
