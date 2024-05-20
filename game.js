// Esegui il codice solo quando il DOM è completamente caricato
$(document).ready(function() {

// Connessione al server di gioco
const GAME_SERVER = "http://localhost:8000";
//const GAME_SERVER = "";
const socket = io.connect(GAME_SERVER);

// Recupero dei dati di gioco
$.ajax({
    url: "readGameData.php",
    type: "POST",
    dataType: "json",
    success: function(response) {
        socket.emit("nuovo-giocatore", response);
    },
    error: function(error) {
        console.log("Error reading settings:", error);
    }
});

// Riempimento delle griglie con gli elementi cella
let width = 10;
function riempiGriglia(griglia, player) {
    for(let i=0; i<width*width; i++) {
        const cella = document.createElement("div");
        cella.classList.add("cella");
        if(player==="g") cella.setAttribute("draggable", "true");
        cella.id = player+i.toString();
        griglia.append(cella);
    }
    $(".cella").css({width: `${100/width}%`});
}


// Creazione delle navi e dei powerup
class Elemento {
    constructor(nome, classe, lunghezza) {
        this.nome = nome;
        this.classe = classe;
        this.lunghezza = lunghezza;
        this.celle = [];
        this.celleColpite = [];
        this.affondata = false;
        this.rotazione = 0;
        this.div = document.createElement("div"); // div è usato per gli spostamenti degli elementi nell'area gioco, da non usare per lo scambio di dati!
        this.div.id = nome; // potrebbe semplificare le funzioni, da vedere
        this.div.classList.add(...classe);
        this.div.setAttribute("draggable", "true");
    }

    ruotaElemento() {
        this.rotazione = this.rotazione===0 ? 90 : 0;
        this.div.style.transform = `rotate(${this.rotazione}deg)`;
    }
}

let elementi = [];
function creaElemento(nome, classe, lunghezza) {
    const elemento = new Elemento(nome, classe, lunghezza);
    elementi.push(elemento);
    return elemento;
}

// Riempimento dell'area navi
const areaNavi = document.getElementById("area-navi");
const row = document.createElement("div");
row.classList.add("row");
areaNavi.append(row);
function riempiAreaNavi() {
    for(let elem of elementi) {
        const col = document.createElement("div");
        col.classList.add("col");
        col.append(elem.div);
        row.append(col);
    }
}

// Associazione degli EventListener alle navi e ai powerup
let elementoSpostato;
function aggiungiEventListenerElementi() {
    for(let elem of elementi) {
        elem.div.addEventListener("click", function(event) {
            elem.ruotaElemento();
        });
        elem.div.addEventListener("dragstart", function(event) {
            elementoSpostato = elem;
            event.dataTransfer.clearData();
            event.dataTransfer.setData("text/plain", elem.nome);
            event.dataTransfer.setDragImage(new Image(), 0, 0);
            event.dataTransfer.effectAllowed = "move";
        });
    }
}

// Funzioni per il posizionamento delle navi e dei powerup sulla griglia del giocatore
function trovaElemento(id) {
    for(let elem of elementi) {
        if(elem.celle.includes(id))
            return elem;
    }
}

function togliElemento(elem) {
    for(let id of elem.celle) {
        const cella = document.getElementById(id);
        cella.className = "cella";
    }
    elem.celle.length = 0;
}

function posizioneValida(elem, id) {
    const player = id.match(/([ga])(\d+)/)[1];
    let index = Number(id.match(/([ga])(\d+)/)[2]);
    if(elem.rotazione===0 && (width-index%width)<elem.lunghezza) return false;
    if(elem.rotazione===90 && index+width*(elem.lunghezza-1)>=width*width) return false;
    for(let i=0; i<elem.lunghezza; i++) {
        const idCella = player+index.toString();
        const cella = document.getElementById(idCella);
        if(cella.classList.contains("cella-piena") && trovaElemento(idCella)!=elem) return false;
        if(elem.rotazione===0)
            index++;
        else
            index+=width;
    }
    return true;
}

function posizionaElemento(elem, id) {
    const player = id.match(/([ga])(\d+)/)[1];
    let index = Number(id.match(/([ga])(\d+)/)[2]);
    togliElemento(elem);
    for(let i=0; i<elem.lunghezza; i++) {
        const idCella = player+index.toString();
        const cella = document.getElementById(idCella);
        if(i===0) {
            cella.classList.add("inizio");
        }
        if(i===elem.lunghezza-1) {
            cella.classList.add("fine");
        }
        if(elem.rotazione===0) {
            cella.classList.add("orizzontale");
        } else {
            cella.classList.add("verticale");
        }
        if(elem.div.classList.contains("nave")) {
            cella.classList.add("cella-piena", "cella-nave");
        } else if(elem.div.classList.contains("powerup")) {
            cella.classList.add("cella-piena", "cella-powerup", elem.classe[1]);
        }
        elem.celle.push(idCella);
        if(elem.rotazione===0)
            index++;
        else
            index+=width;
    }
}

// Funzioni per la gestione dei turni
let giocatorePronto = false;
let giocoIniziato = false;
let giocoFinito = false;
let player = null;
let avversario = null;
let turno = null;

function stampaMessaggio(msg) {
    if(!giocoFinito) {
        const message = document.createElement("div");
        const date = new Date();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const seconds = String(date.getSeconds()).padStart(2, '0');
        const time = `${hours}:${minutes}:${seconds}`;
        message.innerHTML = `<i>(${time})</i> ${msg}`;
        document.getElementById("storico-messaggi").prepend(message);
    }
}

let timer = null;
function resetTimer() {
    if(timer)
        clearInterval(timer);
}

function impostaTimer() {
    resetTimer();
    if(giocoFinito) return;
    let time = 30;
    let minutes = String(Math.floor(time/60)).padStart(2, '0');
    let seconds = String(time%60).padStart(2, '0');
    $('#timer').html(`${minutes}:${seconds}`);
    if(turno==="g") {
        $('#area-timer').css({'background-color': 'white'});
        timer = setInterval(function() {
            if(time===0) {
                socket.emit("cambia-turno");
            }
            time--;
            minutes = String(Math.floor(time/60)).padStart(2, '0');
            seconds = String(time%60).padStart(2, '0');
            $('#timer').html(`${minutes}:${seconds}`);
            if(time<=5)
                $('#area-timer').css({'background-color': 'rgba(209, 20, 20, 1)'});
        }, 1000);
    } else {
        $('#area-timer').css({'background-color': 'gray'});
    }
}

function cambiaTurno() {
    turno = turno==="g" ? "a" : "g";
    const message = turno==="g" ? "<b>È il tuo turno!</b>" : `<b>È il turno di ${avversario}</b>`;
    stampaMessaggio(message);
    impostaTimer();
}

function iniziaPartita(firstPlayer) {
    turno = firstPlayer===player ? "g" : "a";
    giocoIniziato = true;
    stampaMessaggio("<b>Partita iniziata!</b>")
    const message = turno==="g" ? "<b>È il tuo turno!</b>" : `<b>È il turno di ${avversario}</b>`;
    stampaMessaggio(message);
    impostaTimer();
}

function terminaPartita() {
    giocoFinito = true;
    resetTimer();
    socket.emit("end");
    // Aggiornamento della classifica
    $.ajax({
        url: "updateRanking.php",
        type: "POST",
        data: {
            opponent: avversario,
            result: "lost"
        }
    });
    $('#lost-modal').modal('show');
}

// Funzioni per le meccaniche base di gioco
function affondaNave(nave) {
    nave.affondata = true;
    socket.emit("nave-affondata");
    if(elementi.filter(e => e.div.classList.contains("nave")).length===elementi.filter(e => e.affondata && e.div.classList.contains("nave")).length) {
        terminaPartita();
    }
}

function cellaAttiva(cella) {
    if(!cella) return false;
    if(turno==="a") return false;
    if(cella.classList.contains("cella-colpita")) return false;
    if(cella.classList.contains("cella-acqua")) return false;
    return true;
}

function colpisciElemento(id) {
    const cella = document.getElementById(id);
    if(!cella.classList.replace("cella-piena", "cella-colpita")) {
        cella.classList.add("cella-acqua");
        return;
    }
    const elemento = trovaElemento(id);
    elemento.celleColpite.push(id);
    if(elemento.lunghezza-elemento.celleColpite.length===0 && cella.classList.contains("cella-nave")) {
        affondaNave(elemento);
    }
}

// Funzioni per la gestione dei powerup
let powerUps = [];
function recuperaPowerUp(classePowerUp) {
    const powerUp = new Elemento(Math.random().toString(36).slice(2, 7), classePowerUp, 1);
    powerUps.push(powerUp);
    powerUp.div.addEventListener("dragstart", function(event) {
        elementoSpostato = powerUp;
        event.dataTransfer.clearData();
        event.dataTransfer.setData("text/plain", powerUp.nome);
        event.dataTransfer.setDragImage(new Image(), 0, 0);
        event.dataTransfer.effectAllowed = "move";
    });
    const row = document.getElementById("area-powerup").firstElementChild;
    const col = document.createElement("div");
    col.classList.add("col");
    col.append(powerUp.div);
    row.append(col);
    
    stampaMessaggio(`<b>Powerup ${classePowerUp[1].match(/powerup(\w+)/)[1]} trovato!</b>`);
}

function usaPowerUp(powerUp, id) {
    const player = id.match(/([ga])(\d+)/)[1];
    let index = Number(id.match(/([ga])(\d+)/)[2]);
    if(powerUp.classe.includes("powerupriga")) {
        let start = index-index%width;
        let end = index-index%width+width-1;
        for(let i=start; i<=end; i++) {
            const idCella = player+i.toString();
            const cella = document.getElementById(idCella);
            if(cellaAttiva(cella)) {
                socket.emit("colpo", i.toString());
            }
        }
    } else if(powerUp.classe.includes("powerupcolonna")) {
        let start = index%width;
        let end = width*(width-1)+index%width;
        for(let i=start; i<=end; i+=width) {
            const idCella = player+i.toString();
            const cella = document.getElementById(idCella);
            if(cellaAttiva(cella)) {
                socket.emit("colpo", i.toString());
            }
        }
    } else if(powerUp.classe.includes("powerupbomba")) {
        for(let j=index-width; j<=index+width; j+=width) {
            for(let i=j-1; i<=j+1; i++) {
                const idCella = player+i.toString();
                const cella = document.getElementById(idCella);
                if(cellaAttiva(cella)) {
                    socket.emit("colpo", i.toString());
                }
            }
        }
    }
    stampaMessaggio(`<b>Powerup ${powerUp.classe[1].match(/powerup(\w+)/)[1]} usato</b>`);
}

// Funzione per controllo sul bottone di inizio partita
function controllaBottoneInizio() {
    const bottoneInizio = document.getElementById("bottone-inizio");
    if(elementi.filter(e => e.celle.length===0).length) {
        bottoneInizio.setAttribute("disabled", "");
    } else {
        bottoneInizio.removeAttribute("disabled");
    }
}

// Associazione degli EventListener alle celle e all'area navi
function aggiungiEventListenerCelle() {
    const grigliaGiocatore = document.getElementById("griglia-giocatore");
    for(let cella of grigliaGiocatore.children) {
        cella.addEventListener("dragstart", function(event) {
            event.dataTransfer.setDragImage(new Image(), 0, 0);
            event.dataTransfer.effectAllowed = "move";
            if(!giocatorePronto && !giocoIniziato && event.target.classList.contains("cella-piena")) {
                const elemento = trovaElemento(event.target.id);
                elementoSpostato = elemento;
                event.dataTransfer.clearData();
                event.dataTransfer.setData("text/plain", elemento.nome);
            }
        });
        cella.addEventListener("dragover", function(event) {
            event.preventDefault();
        });
        cella.addEventListener("dragenter", function(event) {
            const elemento = elementoSpostato;
            if(!giocatorePronto && !giocoIniziato && elemento!=null) {
                const idCella = event.target.id;
                if(posizioneValida(elemento, idCella)) {
                    posizionaElemento(elemento, idCella);
                }
            }
        });
        cella.addEventListener("drop", function(event) {
            const nome = event.dataTransfer.getData("text/plain");
            const elemento = elementi.find(elem => elem.nome===nome);
            elementoSpostato = null;
            if(!giocatorePronto && !giocoIniziato && elemento!=null) {
                const idCella = event.target.id;
                if(posizioneValida(elemento, idCella)) {
                    posizionaElemento(elemento, idCella);
                    if(elemento.div.parentNode!=null)
                        elemento.div.parentNode.removeChild(elemento.div);
                    controllaBottoneInizio();
                }
            }
        });
        cella.addEventListener("click", function(event) {
            if(!giocatorePronto && !giocoIniziato && event.target.classList.contains("cella-piena")) {
                const elemento = trovaElemento(event.target.id);
                const idCella = elemento.celle[0];
                elemento.ruotaElemento();
                if(posizioneValida(elemento, idCella)) {
                    posizionaElemento(elemento, idCella);
                } else {
                    elemento.ruotaElemento();
                }
            }
        });
    }
    const grigliaAvversario = document.getElementById("griglia-avversario");
    for(let cella of grigliaAvversario.children) {
        cella.addEventListener("dragover", function(event) {
            event.preventDefault();
        });
        cella.addEventListener("dragenter", function(event) {
            const powerUp = elementoSpostato;
            if(giocoIniziato && powerUp!=null && powerUp.div.classList.contains("powerup") && cellaAttiva(event.target)) {
                const idCella = event.target.id;
                if(posizioneValida(powerUp, idCella)) {
                    posizionaElemento(powerUp, idCella);
                }
            }
        });
        cella.addEventListener("drop", function(event) {
            const nome = event.dataTransfer.getData("text/plain");
            const powerUp = powerUps.find(elem => elem.nome===nome);
            elementoSpostato = null;
            if(giocoIniziato && powerUp!=null && powerUp.div.classList.contains("powerup") && cellaAttiva(event.target)) {
                usaPowerUp(powerUp, event.target.id);
                powerUp.div.parentNode.remove();
                setTimeout(function() {
                    socket.emit("cambia-turno");
                }, 100); // ritardo di 0.1s inserito per ricevere prima l'evento risposta-colpo
            }
        });
        cella.addEventListener("click", function(event) {
            if(giocoIniziato && cellaAttiva(event.target)) {
                let index = event.target.id.match(/([ga])(\d+)/)[2];
                socket.emit("colpo", index);
                setTimeout(function() {
                    socket.emit("cambia-turno");
                }, 100); // ritardo di 0.1s inserito per ricevere prima l'evento risposta-colpo
            }
        });
    }
}

function aggiungiEventListenerAreaNavi() {
    areaNavi.addEventListener("dragover", function(event) {
        event.preventDefault();
    });
    areaNavi.addEventListener("drop", function(event) {
        const nome = event.dataTransfer.getData("text/plain");
        const elemento = elementi.find(elem => elem.nome===nome);
        elementoSpostato = null;
        if(!giocatorePronto && !giocoIniziato && elemento!=null) {
            togliElemento(elemento);
            if(elemento.div.parentNode===null) {
                document.querySelector("#area-navi > .row > .col:empty").append(elemento.div);
                controllaBottoneInizio();
            }
        }
    });
}

// Funzione per l'inizializzazione del campo di gioco
function inizializzaCampo(gameData) {
    width = gameData.width;
    riempiGriglia(document.getElementById("griglia-giocatore"), "g");
    riempiGriglia(document.getElementById("griglia-avversario"), "a");

    let navi = gameData.navi;
    let i = 0;
    for(let nave in navi) {
        for(let j=0; j<navi[nave]; j++) {
            creaElemento("n"+i.toString(), ["nave", nave], parseInt(nave.slice(-1)));
            i++;
        }
    }
    let powerUps = gameData.powerUps;
    i = 0;
    for(let powerUp in powerUps) {
        for(let j=0; j<powerUps[powerUp]; j++) {
            creaElemento("p"+i.toString(), ["powerup", powerUp], 1);
            i++;
        }
    }

    riempiAreaNavi();

    aggiungiEventListenerElementi();
    aggiungiEventListenerCelle();
    aggiungiEventListenerAreaNavi();
}

// Gestione della connessione degli utenti
socket.on("nuovo-giocatore", (playerName) => {
    console.log(`giocatore: ${playerName}`);
    player = playerName;
    $('#wait-modal').modal('show');
});
socket.on("avversario-trovato", (gameData, players) => {
    avversario = players.filter(p => p!=player)[0];
    console.log(`avversario trovato: ${avversario}`);
    inizializzaCampo(gameData);
    setTimeout(function() {
        $('#wait-modal').modal('hide');
    }, 500); // ritardo di 0.5s inserito per far finire l'animazione di apertura della finestra modale
});

// Gestione degli eventi di gioco
socket.on("play", (player) => {
    iniziaPartita(player);
});
socket.on("colpo", (index) => {
    const idCella = "g"+index;
    colpisciElemento(idCella);
    const classeElemento = trovaElemento(idCella) ? trovaElemento(idCella).classe : [];
    const classeCella = [...(document.getElementById(idCella).classList)].filter(c => c!="inizio" && c!="fine" && c!="orizzontale" && c!="verticale");
    const msg = {
        id: index,
        classeCella: classeCella.join(" "),
        classePowerUp: classeElemento.includes("powerup") ? classeElemento : []
    };
    socket.emit("risposta-colpo", msg);
});
socket.on("risposta-colpo", (msg) => {
    const idCella = "a"+msg.id;
    const cella = document.getElementById(idCella);
    cella.className = msg.classeCella;
    if(msg.classePowerUp.length) {
        recuperaPowerUp(msg.classePowerUp);
    }
});
socket.on("cambia-turno", () => {
    cambiaTurno();
});
socket.on("end", () => {
    giocoFinito = true;
    resetTimer();
    // Aggiornamento della classifica
    $.ajax({
        url: "updateRanking.php",
        type: "POST",
        data: {
            opponent: avversario,
            result: "win"
        }
    });
    $('#win-modal').modal('show');
});
socket.on("disconnesso", () => {
    giocoFinito = true;
    resetTimer();
    $('#disconnect-modal').modal('show');
});

// Gestione della chat utenti
const formChat = document.getElementById("form-chat");
const inputChat = document.getElementById("input-chat");
formChat.addEventListener("submit", function(event) {
    event.preventDefault();
    if(inputChat.value) {
        socket.emit("chat", `<b>${player}:</b> ${inputChat.value}`);
        inputChat.value = "";
    }
});
socket.on("chat", (msg) => {
    const message = msg.replace(player, "Tu");
    stampaMessaggio(message);
});

// Associazione degli EventListener al bottone "Inizia la partita"
const bottoneInizio = document.getElementById("bottone-inizio");
bottoneInizio.addEventListener("click", function(event) {
    bottoneInizio.parentNode.removeChild(bottoneInizio);
    document.getElementById("area-navi").style.display="none";
    document.getElementById("area-timer").style.display="flex";
    document.getElementById("area-chat").style.display="block";
    $(".cella").attr("draggable", "false");
    $(".cella-piena").css({cursor: 'default'});
    giocatorePronto = true;
    socket.emit("pronto");
});

// Mostra un alert se durante la partita si cerca di ricaricare o cambiare pagina
window.addEventListener("beforeunload", function(event) {
    if(!giocoFinito) {
        event.preventDefault();
    }
});

});