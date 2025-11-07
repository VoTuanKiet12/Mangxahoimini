// server.js
const express = require("express");
const http = require("http");
const { Server } = require("socket.io");

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: ["http://127.0.0.1:8000", "http://127.0.0.1/MangXaHoiMiNi"],
        methods: ["GET", "POST"]
    }
});

// Lưu thông tin phòng
// rooms = { roomName: { sockets: { socketId: { username, avatar } } } }
const rooms = {};

io.on("connection", (socket) => {
    console.log("socket connected", socket.id);

    // Khi người dùng join voice chat
    socket.on("join", ({ room, username, avatar }) => {
        socket.join(room);

        // Nếu chưa có phòng thì tạo mới
        if (!rooms[room]) rooms[room] = { sockets: {} };

        // Lưu thông tin người dùng
        rooms[room].sockets[socket.id] = {
            username: username || "Ẩn danh",
            avatar: avatar || null
        };

        // Gửi danh sách các peer hiện có cho người mới
        const existing = Object.keys(rooms[room].sockets)
            .filter(id => id !== socket.id)
            .map(id => ({
                id,
                username: rooms[room].sockets[id].username,
                avatar: rooms[room].sockets[id].avatar
            }));

        socket.emit("existing-peers", existing);

        // Thông báo cho các peer khác rằng có người mới
        socket.to(room).emit("new-peer", {
            id: socket.id,
            username,
            avatar
        });

        console.log(`${username || socket.id} joined ${room}`);
    });

    // Gửi tín hiệu WebRTC giữa các peer
    socket.on("signal", ({ to, signal, username, avatar }) => {
        io.to(to).emit("signal", {
            from: socket.id,
            signal,
            username,
            avatar
        });
    });

    // Sự kiện chia sẻ màn hình
    // socket.on("start-screen-share", ({ room }) => {
    //     socket.to(room).emit("peer-screen-start", {
    //         id: socket.id,
    //         username: rooms[room]?.sockets[socket.id]?.username,
    //         avatar: rooms[room]?.sockets[socket.id]?.avatar
    //     });
    // });

    // socket.on("stop-screen-share", ({ room }) => {
    //     socket.to(room).emit("peer-screen-stop", socket.id);
    // });

    // Khi người dùng rời phòng
    socket.on("leave", ({ room }) => {
        socket.leave(room);
        if (rooms[room]) {
            delete rooms[room].sockets[socket.id];
            socket.to(room).emit("peer-disconnect", socket.id);
        }
    });

    // Khi ngắt kết nối
    socket.on("disconnect", () => {
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
