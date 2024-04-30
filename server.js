import express from "express";
import { createServer } from "http";
import { Server } from "socket.io";

const PORT = 8000;
const app = express();
const server = createServer(app);
const io = new Server(server, {
    cors: {
        origin: "*"
    }
});

app.get("/", (req, res) => {
  res.send("<h1>Game server is up!</h1>");
});

class Stanza {
    constructor() {
        this.nome = Math.random().toString(36).slice(2, 7);
        this.posti = 2;
        this.postiDisponibili = 2;
        this.giocatori = [];
        this.giocatoriPronti = 0;
        this.turno = "";
    }
}

let stanze = [];
function trovaStanza() {
    for(let stanza of stanze) {
        if(stanza.postiDisponibili) {
            return stanza;
        }
    }
    return null;
}

function creaStanza() {
    let nuovaStanza = new Stanza();
    stanze.push(nuovaStanza);
    return nuovaStanza;
}

function aggiungiGiocatore(player, socket) {
    let stanza = trovaStanza();
    if(!stanza) stanza = creaStanza();
    stanza.postiDisponibili--;
    stanza.giocatori.push(player);
    socket.join(stanza.nome);
    console.log(`player ${player} connected to room ${stanza.nome}`);
    return stanza;
}

io.on("connection", (socket) => {
    let stanza, player;
    socket.on("player", (playerName) => {
        player = playerName;
        stanza = aggiungiGiocatore(player, socket);
        if(stanza.postiDisponibili) {
            socket.emit("server", "in attesa dell'avversario");
        } else {
            io.to(stanza.nome).emit("server", "avversario trovato");
        }
    });
    socket.on("pronto", (msg) => {
        stanza.giocatoriPronti++;
        if(stanza.giocatoriPronti===stanza.posti) {
            io.to(stanza.nome).emit("play", stanza.giocatori[0]);
        }
    });
    socket.on("colpo", (id) => {
        socket.broadcast.emit("colpo", id);
    });
    socket.on("risposta-colpo", (msg) => {
        socket.broadcast.emit("risposta-colpo", msg);
    });
    socket.on("cambia-turno", () => {
        io.to(stanza.nome).emit("cambia-turno");
    });
    socket.on("end", () => {
        socket.broadcast.emit("end");
    });
    socket.on("chat", (msg) => {
        io.to(stanza.nome).emit("chat", msg);
    });
    socket.on("disconnect", () => {
        /*socket.leave(stanza.nome);*/
        stanza.postiDisponibili++;
        stanza.giocatori.splice(stanza.giocatori.indexOf(player), 1);
        stanza.giocatoriPronti--;
        console.log(`player ${player} disconnected`);
    });
});

server.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});
