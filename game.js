// username è preso dalla variabile di sessione php (stringa vuota se non è settata)
const player = username==="" ? "user-"+Math.random().toString(36).slice(2, 7) : username;

// Riempimento delle griglie con gli elementi cella
const width = 10;
function riempiGriglia(griglia, player) {
    for(let i=0; i<width*width; i++) {
        const cella = document.createElement("div");
        cella.classList.add("cella");
        if(player==="g") cella.setAttribute("draggable", "true");
        cella.id = player+i.toString();
        griglia.append(cella);
    }
}
riempiGriglia(document.getElementById("griglia-giocatore"), "g");
riempiGriglia(document.getElementById("griglia-avversario"), "a");

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
elementi.push(new Elemento("n1", ["nave", "nave1"], 1));
elementi.push(new Elemento("n2", ["nave", "nave2"], 2));
elementi.push(new Elemento("n3", ["nave", "nave3"], 3));
elementi.push(new Elemento("n4", ["nave", "nave4"], 4));
elementi.push(new Elemento("n5", ["nave", "nave5"], 5));
elementi.push(new Elemento("p1", ["powerup", "powerup-riga"], 1));
elementi.push(new Elemento("p2", ["powerup", "powerup-colonna"], 1));
elementi.push(new Elemento("p3", ["powerup", "powerup-bomba"], 1));

// Riempimento dell'area navi
const areaNavi = document.getElementById("area-navi");
const row = document.createElement("div");
row.classList.add("row");
areaNavi.append(row);
for(let elem of elementi) {
    const col = document.createElement("div");
    col.classList.add("col");
    col.append(elem.div);
    row.append(col);
}

// Associazione degli EventListener alle navi e ai powerup
let elementoSpostato;
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
        cella.classList.remove("cella-piena", "cella-nave", "cella-powerup");
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
        if(elem.div.classList.contains("nave")) {
            cella.classList.add("cella-piena", "cella-nave");
        } else if(elem.div.classList.contains("powerup")) {
            cella.classList.add("cella-piena", "cella-powerup");
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
let turno = null;

function cambiaTurno() {
    turno = turno==="g" ? "a" : "g";
}

function iniziaPartita(firstPlayer) {
    console.log(`inizia ${firstPlayer}`);
    turno = firstPlayer===player ? "g" : "a";
    giocoIniziato = true;
    console.log(`partita iniziata, turno: ${turno}`);
}

function terminaPartita() {
    console.log("hai perso");
    socket.emit("end");
}

// Funzioni per le meccaniche base di gioco
function affondaNave(nave) {
    nave.affondata = true;
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
}

function usaPowerUp(powerUp, id) {
    const player = id.match(/([ga])(\d+)/)[1];
    let index = Number(id.match(/([ga])(\d+)/)[2]);
    // verifica del powerup usato, si potrebbe migliorare
    if(powerUp.classe.includes("powerup-riga")) {
        let start = index-index%width;
        let end = index-index%width+width-1;
        for(let i=start; i<=end; i++) {
            const idCella = player+i.toString();
            const cella = document.getElementById(idCella);
            if(cellaAttiva(cella)) {
                socket.emit("colpo", i.toString());
            }
        }
    } else if(powerUp.classe.includes("powerup-colonna")) {
        let start = index%width;
        let end = width*(width-1)+index%width;
        for(let i=start; i<=end; i+=width) {
            const idCella = player+i.toString();
            const cella = document.getElementById(idCella);
            if(cellaAttiva(cella)) {
                socket.emit("colpo", i.toString());
            }
        }
    } else if(powerUp.classe.includes("powerup-bomba")) {
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
    console.log(`${powerUp.classe[1]} usato`);
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
        if(giocoIniziato && powerUp!=null && powerUp.div.classList.contains("powerup") && cellaAttiva(event.target)) {
            usaPowerUp(powerUp, event.target.id);
            powerUp.div.parentNode.remove();
            socket.emit("cambia-turno");
        }
    });
    cella.addEventListener("click", function(event) {
        if(giocoIniziato && cellaAttiva(event.target)) {
            let index = event.target.id.match(/([ga])(\d+)/)[2];
            socket.emit("colpo", index);
            socket.emit("cambia-turno"); // vedere se funziona con il fine partita
        }
    });
}
areaNavi.addEventListener("dragover", function(event) {
    event.preventDefault();
});
areaNavi.addEventListener("drop", function(event) {
    const nome = event.dataTransfer.getData("text/plain");
    const elemento = elementi.find(elem => elem.nome===nome);
    if(!giocatorePronto && !giocoIniziato && elemento!=null && elemento.div.parentNode===null) {
        togliElemento(elemento);
        document.querySelector("#area-navi > .row > .col:empty").append(elemento.div);
        controllaBottoneInizio();
    }
});

// Finestra modale per attesa giocatore
const waitModal = new bootstrap.Modal("#wait-modal", {
    backdrop: "static",
    keyboard: false
});

// Gestione della connessione degli utenti
const socket = io("http://localhost:8000");
//volendo si può testare il progetto sulla rete di casa, va inserito l'indirizzo ip locale del computer
//const socket = io("http://192.168.1.201:8000");

socket.emit("player", player); 
socket.on("server", (msg) => {
    if(msg==="in attesa dell'avversario") {
        console.log(msg);
        waitModal.show();
    }
    else if(msg==="avversario trovato") {
        setTimeout(function() {
            console.log(msg);
            waitModal.hide();
        }, 500); // ritardo di 0.5s inserito per far finire l'animazione di apertura della waitModale
    }
});
socket.on("play", (player) => {
    iniziaPartita(player);
});
socket.on("colpo", (index) => {
    const idCella = "g"+index;
    colpisciElemento(idCella);
    const classeElemento = trovaElemento(idCella) ? trovaElemento(idCella).classe : [];
    const msg = {
        id: index,
        classeCella: document.getElementById(idCella).className,
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
    console.log(`turno: ${turno}`);
});
socket.on("end", () => {
    console.log("hai vinto");
});

// Gestione della chat utenti
const formChat = document.getElementById("form-chat");
const inputChat = document.getElementById("input-chat");
formChat.addEventListener("submit", function(event) {
    event.preventDefault();
    if(inputChat.value) {
        socket.emit("chat", `${player}: ${inputChat.value}`);
        inputChat.value = "";
    }
});
socket.on("chat", (msg) => {
    const message = document.createElement("div");
    message.innerHTML = msg.replace(player, "Tu"); // vedere se conviene scrivere "tu" o il nome del player
    document.getElementById("storico-messaggi").prepend(message);
});

// Associazione degli EventListener al bottone "Inizia la partita"
const bottoneInizio = document.getElementById("bottone-inizio");
bottoneInizio.addEventListener("click", function(event) {
    bottoneInizio.parentNode.removeChild(bottoneInizio);
    document.getElementById("area-navi").style.display="none";
    document.getElementById("area-chat").style.display="block";
    $(".cella").attr("draggable", "false");
    $(".cella-piena").css({cursor: 'default'});
    giocatorePronto = true;
    socket.emit("pronto", elementi);
});

// Mostra un alert se durante la partita si cerca di ricaricare o cambiare pagina
window.addEventListener("beforeunload", function(event) {
    event.preventDefault();
});