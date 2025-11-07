// server.js
const express = require("express");
const http = require("http");
const { Server } = require("socket.io");

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: ["https://your-laravel-domain.com", "http://localhost:8000"],
        methods: ["GET", "POST"]
    }
});

const rooms = {}; // { roomName: { sockets: { socketId: username }, ... } }

io.on("connection", (socket) => {
    console.log("socket connected", socket.id);

    socket.on("join", ({ room, username }) => {
        socket.join(room);
        if (!rooms[room]) rooms[room] = { sockets: {} };
        rooms[room].sockets[socket.id] = username || "Ẩn danh";

        // send to new peer the list of existing peers
        const existing = Object.keys(rooms[room].sockets)
            .filter(id => id !== socket.id)
            .map(id => ({ id, username: rooms[room].sockets[id] }));

        socket.emit("existing-peers", existing);

        // notify existing peers there's a new peer
        socket.to(room).emit("new-peer", { id: socket.id, username });

        console.log(`${username || socket.id} joined ${room}`);
    });

    socket.on("signal", ({ to, signal, username }) => {
        // forward signal to target
        io.to(to).emit("signal", { from: socket.id, signal, username });
    });

    socket.on("start-screen-share", ({ room }) => {
        socket.to(room).emit("peer-screen-start", { id: socket.id, username: rooms[room]?.sockets[socket.id] });
    });

    socket.on("stop-screen-share", ({ room }) => {
        socket.to(room).emit("peer-screen-stop", socket.id);
    });

    socket.on("leave", ({ room }) => {
        socket.leave(room);
        if (rooms[room]) {
            delete rooms[room].sockets[socket.id];
            socket.to(room).emit("peer-disconnect", socket.id);
        }
    });

    socket.on("disconnect", () => {
        // cleanup from all rooms
        for (const room of Object.keys(rooms)) {
            if (rooms[room].sockets[socket.id]) {
                delete rooms[room].sockets[socket.id];
                socket.to(room).emit("peer-disconnect", socket.id);
            }
            if (Object.keys(rooms[room].sockets).length === 0) {
                delete rooms[room];
            }
        }
        console.log("socket disconnected", socket.id);
    });
});

const PORT = process.env.PORT || 3000;
server.listen(PORT, () => console.log(`Signaling server listening on ${PORT}`));
