// Riempimento delle griglie con gli elementi cella
const width = 10;
function riempiGriglia(griglia, player) {
    for(let i=0; i<width*width; i++) {
        const cella = document.createElement("div");
        cella.classList.add("cella");
        cella.setAttribute("draggable", "true");
        cella.id = player+i.toString();
        griglia.append(cella);
    }
}

riempiGriglia(document.getElementById("griglia-giocatore"), "g");
riempiGriglia(document.getElementById("griglia-avversario"), "a");

// Creazione delle navi e dei powerup
class Elemento {
    constructor(nome, classi, lunghezza) {
        this.nome = nome;
        this.lunghezza = lunghezza;
        this.celle = [];
        this.celleColpite = [];
        this.affondata = false;
        this.rotazione = 0;
        this.div = document.createElement("div");
        this.div.classList.add(...classi);
        this.div.setAttribute("draggable", "true");

        this.player = ""; // Serve solo nella demo senza multiplayer
    }

    ruotaElemento() {
        this.rotazione = this.rotazione===0 ? 90 : 0;
        /*event.target.style.transformOrigin = "10px 10px";*/
        this.div.style.transform = `rotate(${this.rotazione}deg)`;
    }
}

let navi = [];
navi.push(new Elemento("n1", ["nave", "nave1"], 1));
navi.push(new Elemento("n2", ["nave", "nave2"], 2));
navi.push(new Elemento("n3", ["nave", "nave3"], 3));
navi.push(new Elemento("n4", ["nave", "nave4"], 4));
navi.push(new Elemento("n5", ["nave", "nave5"], 5));

let powerUps = [];
powerUps.push(new Elemento("p1", ["powerup"], 1));
powerUps.push(new Elemento("p2", ["powerup"], 1));
powerUps.push(new Elemento("p3", ["powerup"], 1));
powerUps.push(new Elemento("p4", ["powerup"], 1));

let elementi = navi.concat(powerUps);

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
for(let elem of elementi) {
    elem.div.addEventListener("click", (event) => {
        elem.ruotaElemento();
    });
    elem.div.addEventListener("dragstart", (event) => {
        event.dataTransfer.clearData();
        event.dataTransfer.setData("text/plain", elem.nome);
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
    elem.player = player;
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
let giocoIniziato = false;
let turno = null;
function cambiaTurno() {
    turno = turno==="g" ? "a" : "g";
}

function iniziaPartita() {
    cambiaTurno();
    giocoIniziato = true;
}

function terminaPartita() {
    console.log("vince " + turno);
}

// Funzioni per le meccaniche base di gioco
function affondaNave(nave) {
    nave.affondata = true;
    console.log("affondato", nave);
    if(navi.filter(e => e.player===nave.player).length===navi.filter(e => e.affondata && e.player===nave.player).length) {
        terminaPartita();
    }
}

function recuperaPowerUp(powerUp) {
    const row = document.getElementById("area-powerup").firstElementChild;
    const col = document.createElement("div");
    col.classList.add("col");
    col.append(powerUp.div);
    row.append(col);
}

function cellaAttiva(cella) {
    const proprietarioCella = cella.id.match(/([ga])(\d+)/)[1];
    if(turno===proprietarioCella) return false;
    if(cella.classList.contains("cella-colpita")) return false;
    if(cella.classList.contains("cella-acqua")) return false;
    return true;
}

function colpisciElemento(id) {
    const cella = document.getElementById(id);
    if(!cella.classList.replace("cella-piena", "cella-colpita")) {
        cella.classList.add("cella-acqua");
        console.log("acqua");
        return;
    }
    const elemento = trovaElemento(id);
    elemento.celleColpite.push(id);
    console.log("colpito", elemento);
    if(cella.classList.contains("cella-powerup")) {
        recuperaPowerUp(elemento);
    } else if(elemento.lunghezza-elemento.celleColpite.length===0 && cella.classList.contains("cella-nave")) {
        affondaNave(elemento);
    }
}

function usaPowerUp(powerUp, id) {
    const player = id.match(/([ga])(\d+)/)[1];
    let index = Number(id.match(/([ga])(\d+)/)[2]);
    let start = index-index%width;
    let end = index-index%width+width;
    for(let i=start; i<end; i++) {
        const idCella = player+i.toString();
        colpisciElemento(idCella);
    }
    console.log("powerup usato");
}

// Associazione degli EventListener alle celle e all'area navi
const griglie = document.getElementsByClassName("griglia");
for(let griglia of griglie) {
    for(let cella of griglia.children) {
        cella.addEventListener("dragstart", (event) => {
            if(!giocoIniziato && event.target.classList.contains("cella-piena")) {
                const elemento = trovaElemento(event.target.id);
                event.dataTransfer.clearData();
                event.dataTransfer.setData("text/plain", elemento.nome);
            }
        });
        cella.addEventListener("dragover", (event) => {
            event.preventDefault();
        });
        cella.addEventListener("drop", (event) => {
            const nome = event.dataTransfer.getData("text/plain");
            const elemento = elementi.find(elem => elem.nome===nome);
            if(!giocoIniziato && elemento!=null) {
                const idCella = event.target.id;
                if(posizioneValida(elemento, idCella)) {
                    posizionaElemento(elemento, idCella);
                    if(elemento.div.parentNode!=null)
                        elemento.div.parentNode.removeChild(elemento.div);
                }
            } else if(elemento!=null && elemento.div.classList.contains("powerup") && cellaAttiva(event.target)) {
                usaPowerUp(elemento, event.target.id);
                elemento.div.parentNode.removeChild(elemento.div);
                cambiaTurno();
            }
        });
        cella.addEventListener("click", (event) => {
            if(!giocoIniziato && event.target.classList.contains("cella-piena")) {
                const elemento = trovaElemento(event.target.id);
                const idCella = elemento.celle[0];
                elemento.ruotaElemento();
                if(posizioneValida(elemento, idCella)) {
                    posizionaElemento(elemento, idCella);
                } else {
                    elemento.ruotaElemento();
                }
            } else if(giocoIniziato && cellaAttiva(event.target)) {
                colpisciElemento(event.target.id);
                cambiaTurno();
            }
        });
    }
}
areaNavi.addEventListener("dragover", (event) => {
    event.preventDefault();
});
areaNavi.addEventListener("drop", (event) => {
    const nome = event.dataTransfer.getData("text/plain");
    const elemento = elementi.find(elem => elem.nome===nome);
    if(!giocoIniziato && elemento!=null && elemento.div.parentNode===null) {
        togliElemento(elemento);
        document.querySelector("#area-navi > .row > .col:empty").append(elemento.div);
    }
});

// Associazione degli EventListener al bottone "Inizia la partita"
const bottone_inizio = document.getElementById("area-bottone-navi").firstElementChild;
bottone_inizio.addEventListener("click", (event) => {
    bottone_inizio.parentNode.removeChild(bottone_inizio);
    iniziaPartita();
});
