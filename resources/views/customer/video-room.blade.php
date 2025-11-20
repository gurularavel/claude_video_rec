<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Video Support</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #video-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 10px;
            height: calc(100vh - 150px);
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
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <div class="alert alert-success">
            <strong>Connected!</strong> You're now in a video call with support.
        </div>

        <div id="video-container"></div>
    </div>

    <script src="https://media.twiliocdn.com/sdk/js/video/releases/2.27.0/twilio-video.min.js"></script>
    <script>
        const sessionUuid = '{{ $sessionUuid }}';
        let room;

        async function initializeCall() {
            try {
                const response = await fetch(`/support/call/${sessionUuid}/token`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        identity: 'customer-' + Date.now()
                    })
                });

                const data = await response.json();

                if (!data.success) {
                    alert('Failed to get video token');
                    return;
                }

                room = await Twilio.Video.connect(data.token, {
                    name: data.room_name,
                    audio: true,
                    video: { width: 640 }
                });

                console.log('Connected to room:', room.name);

                room.localParticipant.tracks.forEach(publication => {
                    if (publication.track) {
                        document.getElementById('video-container').appendChild(
                            createParticipantDiv(room.localParticipant, publication.track)
                        );
                    }
                });

                room.participants.forEach(participantConnected);
                room.on('participantConnected', participantConnected);
                room.on('participantDisconnected', participantDisconnected);

                room.on('disconnected', () => {
                    alert('Call ended');
                    window.close();
                });

            } catch (error) {
                console.error('Error:', error);
                alert('Error connecting: ' + error.message);
            }
        }

        function participantConnected(participant) {
            participant.tracks.forEach(publication => {
                if (publication.isSubscribed) {
                    trackSubscribed(participant, publication.track);
                }
            });

            participant.on('trackSubscribed', track => trackSubscribed(participant, track));
        }

        function participantDisconnected(participant) {
            document.getElementById(participant.sid).remove();
        }

        function trackSubscribed(participant, track) {
            document.getElementById('video-container').appendChild(
                createParticipantDiv(participant, track)
            );
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

        window.addEventListener('load', initializeCall);
    </script>
</body>
</html>
