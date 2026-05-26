import {fetch_weather} from "../../utils/api.js"

const initCopyArticleContent = () => {
    const copyButton = document.getElementById("copyArticleContent")
    const feedback = document.getElementById("copyArticleFeedback")

    if(!copyButton || !feedback) return

    const target = document.getElementById(copyButton.dataset.copyTarget)

    if(!target) return

    const setFeedback = message => {
        feedback.textContent = message
        setTimeout(() => {
            feedback.textContent = ""
        }, 3500)
    }

    copyButton.addEventListener("click", async () => {
        const text = target.textContent.trim()

        if(text === ""){
            setFeedback("Nessun contenuto da copiare.")
            return
        }

        try{
            if(navigator.clipboard && window.isSecureContext){
                await navigator.clipboard.writeText(text)
            }

            setFeedback("Contenuto copiato.")
        }catch(e){
            setFeedback("Copia non riuscita.")
            console.error(e)
        }
    })
}

const initWeather = async () => {
    const box = document.getElementById("weatherBox")
    if(!box) return

    const lat = box.dataset.lat
    const lng = box.dataset.lng

    if(!lat || !lng){
        box.textContent = "Coordinate non disponibili."
        return
    }

    try{
        const weather = await fetch_weather(lat, lng)

        if(!weather){
            box.textContent = "Meteo non disponibile."
            return
        }

        box.innerHTML = `
            Temperatura: <strong>${weather.temperatura} °C</strong><br>
            Percepita: <strong>${weather.temperaturaPercepita} °C</strong><br>
            Vento: <strong>${weather.vento} km/h</strong>
        `
    }catch(e){
        box.textContent = "Meteo non disponibile."
        console.error(e)
    }
}

document.addEventListener("DOMContentLoaded", () => {
    initCopyArticleContent()
    initWeather()
})