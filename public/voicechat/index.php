<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Voice Chat WebRTC</title>
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
</head>

<body>
    <h2>🔊 Voice Chat</h2>
    <button id="joinBtn">Join Room</button>
    <audio id="remoteAudio" autoplay></audio>

    <script>
        const socket = io("http://localhost:3001"); // signaling server
        const roomId = "global-room";
        const remoteAudio = document.getElementById("remoteAudio");
        let localStream, peer;

        document.getElementById("joinBtn").onclick = async () => {
            socket.emit("join", roomId);
            localStream = await navigator.mediaDevices.getUserMedia({
                audio: true
            });
            createPeerConnection();
        };

        function createPeerConnection() {
            peer = new RTCPeerConnection();
            localStream.getTracks().forEach(track => peer.addTrack(track, localStream));

            peer.ontrack = event => {
                remoteAudio.srcObject = event.streams[0];
            };

            peer.onicecandidate = event => {
                if (event.candidate) {
                    socket.emit("ice-candidate", {
                        to: "all",
                        candidate: event.candidate
                    });
                }
            };
        }

        socket.on("user-joined", async (userId) => {
            const offer = await peer.createOffer();
            await peer.setLocalDescription(offer);
            socket.emit("offer", {
                to: userId,
                sdp: offer
            });
        });

        socket.on("offer", async (data) => {
            await peer.setRemoteDescription(new RTCSessionDescription(data.sdp));
            const answer = await peer.createAnswer();
            await peer.setLocalDescription(answer);
            socket.emit("answer", {
                to: data.from,
                sdp: answer
            });
        });

        socket.on("answer", async (data) => {
            await peer.setRemoteDescription(new RTCSessionDescription(data.sdp));
        });

        socket.on("ice-candidate", async (data) => {
            if (data.candidate) {
                try {
                    await peer.addIceCandidate(new RTCIceCandidate(data.candidate));
                } catch (e) {
                    console.error("Error adding ICE:", e);
                }
            }
        });
    </script>
</body>

</html>