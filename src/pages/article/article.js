import {fetch_weather} from "../../utils/api.js";

const initWeather = async () => {
    const box = document.getElementById("weatherBox");
    if(!box) return;

    const lat = box.dataset.lat;
    const lng = box.dataset.lng;

    if(!lat || !lng){
        box.textContent = "Coordinate non disponibili.";
        return;
    }

    try{
        const weather = await fetch_weather(lat, lng);

        if(!weather){
            box.textContent = "Meteo non disponibile.";
            return;
        }

        box.innerHTML = `
            Temperatura: <strong>${weather.temperatura} °C</strong><br>
            Percepita: <strong>${weather.temperaturaPercepita} °C</strong><br>
            Vento: <strong>${weather.vento} km/h</strong>
        `;
    }catch(e){
        box.textContent = "Meteo non disponibile.";
        console.error(e);
    }
};

document.addEventListener("DOMContentLoaded", initWeather);