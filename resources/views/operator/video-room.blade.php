<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Video Support - Operator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #video-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 10px;
            height: calc(100vh - 200px);
        }
        .participant {
            background: #000;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }
        .participant video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .participant-name {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
        }
        #controls {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <div class="alert alert-info">
            <strong>Video Support Session</strong>
            <span class="float-end">Session: {{ $sessionUuid }}</span>
        </div>

        <div id="video-container"></div>

        <div id="controls">
            <button id="toggle-video" class="btn btn-primary">
                <span id="video-icon">📹</span> Video
            </button>
            <button id="toggle-audio" class="btn btn-primary">
                <span id="audio-icon">🎤</span> Audio
            </button>
            <button id="end-call" class="btn btn-danger">
                End Call
            </button>
        </div>
    </div>

    <script src="https://media.twiliocdn.com/sdk/js/video/releases/2.27.0/twilio-video.min.js"></script>
    <script>
        const sessionUuid = '{{ $sessionUuid }}';
        let room;
        let localVideoTrack;
        let localAudioTrack;

        async function initializeCall() {
            try {
                // Get access token from server
                const response = await fetch(`/support/${sessionUuid}/token`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        identity: 'operator-{{ auth()->id() }}'
                    })
                });

                const data = await response.json();

                if (!data.success) {
                    alert('Failed to get video token');
                    return;
                }

                // Connect to Twilio room
                room = await Twilio.Video.connect(data.token, {
                    name: data.room_name,
                    audio: true,
                    video: { width: 640 }
                });

                console.log('Connected to room:', room.name);

                // Handle local participant
                room.localParticipant.tracks.forEach(publication => {
                    if (publication.track) {
                        if (publication.track.kind === 'video') {
                            localVideoTrack = publication.track;
                        } else if (publication.track.kind === 'audio') {
                            localAudioTrack = publication.track;
                        }
                        document.getElementById('video-container').appendChild(
                            createParticipantDiv(room.localParticipant, publication.track)
                        );
                    }
                });

                // Handle remote participants
                room.participants.forEach(participantConnected);
                room.on('participantConnected', participantConnected);
                room.on('participantDisconnected', participantDisconnected);

                // Handle disconnect
                room.on('disconnected', () => {
                    room.localParticipant.tracks.forEach(publication => {
                        if (publication.track) {
                            publication.track.stop();
                            const attachedElements = publication.track.detach();
                            attachedElements.forEach(element => element.remove());
                        }
                    });
                    window.location.href = '/dashboard';
                });

            } catch (error) {
                console.error('Error initializing call:', error);
                alert('Error connecting to video call: ' + error.message);
            }
        }

        function participantConnected(participant) {
            console.log('Participant connected:', participant.identity);

            participant.tracks.forEach(publication => {
                if (publication.isSubscribed) {
                    trackSubscribed(participant, publication.track);
                }
            });

            participant.on('trackSubscribed', track => trackSubscribed(participant, track));
            participant.on('trackUnsubscribed', track => trackUnsubscribed(participant, track));
        }

        function participantDisconnected(participant) {
            console.log('Participant disconnected:', participant.identity);
            document.getElementById(participant.sid).remove();
        }

        function trackSubscribed(participant, track) {
            document.getElementById('video-container').appendChild(
                createParticipantDiv(participant, track)
            );
        }

        function trackUnsubscribed(participant, track) {
            const attachedElements = track.detach();
            attachedElements.forEach(element => element.remove());
        }

        function createParticipantDiv(participant, track) {
            const participantDiv = document.createElement('div');
            participantDiv.id = participant.sid;
            participantDiv.className = 'participant';

            const media = track.attach();
            participantDiv.appendChild(media);

            const nameTag = document.createElement('div');
            nameTag.className = 'participant-name';
            nameTag.textContent = participant.identity;
            participantDiv.appendChild(nameTag);

            return participantDiv;
        }

        // Controls
        document.getElementById('toggle-video').addEventListener('click', () => {
            if (localVideoTrack) {
                if (localVideoTrack.isEnabled) {
                    localVideoTrack.disable();
                    document.getElementById('video-icon').textContent = '🚫';
                } else {
                    localVideoTrack.enable();
                    document.getElementById('video-icon').textContent = '📹';
                }
            }
        });

        document.getElementById('toggle-audio').addEventListener('click', () => {
            if (localAudioTrack) {
                if (localAudioTrack.isEnabled) {
                    localAudioTrack.disable();
                    document.getElementById('audio-icon').textContent = '🔇';
                } else {
                    localAudioTrack.enable();
                    document.getElementById('audio-icon').textContent = '🎤';
                }
            }
        });

        document.getElementById('end-call').addEventListener('click', async () => {
            if (confirm('Are you sure you want to end the call?')) {
                // End call on server
                await fetch(`/support/${sessionUuid}/end-call`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (room) {
                    room.disconnect();
                }

                window.location.href = '/dashboard';
            }
        });

        // Initialize on page load
        window.addEventListener('load', async () => {
            // Start call on server first
            await fetch(`/support/${sessionUuid}/start-call`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            // Then connect to room
            await initializeCall();
        });
    </script>
</body>
</html>
