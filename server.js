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
    constructor(settings) {
        this.nome = Math.random().toString(36).slice(2, 7);
        this.posti = 2;
        this.postiDisponibili = 2;
        this.giocatori = [];
        this.giocatoriPronti = 0;
        this.settings = settings;
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

function creaStanza(settings) {
    let nuovaStanza = new Stanza(settings);
    stanze.push(nuovaStanza);
    return nuovaStanza;
}

function aggiungiGiocatore(player, socket, stanza) {
    stanza.postiDisponibili--;
    stanza.giocatori.push(player);
    socket.join(stanza.nome);
    console.log(`player ${player} connected to room ${stanza.nome}`);
}

io.on("connection", (socket) => {
    let stanza = null;
    let player = null;
    socket.on("nuovo-giocatore", (gameData) => {
        if(gameData.username==="") gameData.username = "user-" + Math.random().toString(36).slice(2, 7);
        player = gameData.username;
        let settings = gameData.settings;
        if(settings.mode==="create") {
            stanza = creaStanza(settings);
        } else if(settings.mode==="join") {
            stanza = trovaStanza();
            if(!stanza) stanza = creaStanza(settings);
        } else {
            console.log(`player ${player} didn't specify a mode`);
            return;
        }
        aggiungiGiocatore(player, socket, stanza);
        socket.emit("nuovo-giocatore", player);
        if(!stanza.postiDisponibili) {
            io.to(stanza.nome).emit("avversario-trovato", stanza.settings, stanza.giocatori);
        }
    });
    socket.on("pronto", () => {
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
        console.log(`chat: ${msg}`);
        socket.broadcast.emit("chat", msg);
    });
    socket.on("nave-affondata", () => {
        socket.broadcast.emit("chat", "<b>Nave nemica affondata!</b>");
    });
    socket.on("disconnect", () => {
        if(stanza===null) return;
        stanza.postiDisponibili++;
        stanza.giocatori.splice(stanza.giocatori.indexOf(player), 1);
        stanza.giocatoriPronti--;
        stanza.settings = {};
        console.log(`player ${player} disconnected`);
        if(stanza.giocatori.length===0) {
            stanze.splice(stanze.indexOf(stanza), 1);
        }
        socket.broadcast.emit("disconnesso");
    });
});

server.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});
