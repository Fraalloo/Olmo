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

const initArticlePdfExport = () => {
    const exportButton = document.getElementById("exportArticlePdf")
    const feedback = document.getElementById("copyArticleFeedback")
    const article = document.querySelector(".article-main")
    const sidebar = document.querySelector(".article-sidebar")
    const banner = document.querySelector(".article-hero")

    if(!exportButton || !feedback || !article) return

    const setFeedback = message => {
        feedback.textContent = message
        setTimeout(() => {
            feedback.textContent = ""
        }, 3500)
    }

    const filenameFromTitle = () => {
        const title = article.querySelector("h1")?.textContent.trim() || "articolo"
        const normalized = title
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/^-+|-+$/g, "")

        return `${normalized || "articolo"}.pdf`
    }

    const cloneInfoBox = title => {
        if(!sidebar) return null

        return Array
            .from(sidebar.querySelectorAll(".info-box"))
            .find(box => box.querySelector("h3")?.textContent.trim() === title)
            ?.cloneNode(true) || null
    }

    const buildPdfDocument = () => {
        const printableWrapper = document.createElement("div")
        const printableDocument = document.createElement("section")
        const printableArticle = article.cloneNode(true)
        const coordinateBox = cloneInfoBox("Coordinate")
        const linksBox = cloneInfoBox("Link utili")

        printableWrapper.className = "article-pdf-wrapper"
        printableDocument.className = "article-pdf-document"

        if(banner){
            const printableBanner = banner.cloneNode(true)
            printableBanner.classList.add("article-pdf-banner")
            printableDocument.appendChild(printableBanner)
        }

        printableArticle.querySelector(".article-actions")?.remove()
        printableArticle.querySelector(".article-description-actions")?.remove()
        printableArticle.classList.add("article-pdf-export")
        printableDocument.appendChild(printableArticle)

        if(coordinateBox || linksBox){
            const extraPanel = document.createElement("section")
            const extraTitle = document.createElement("h2")
            const extraInfo = document.createElement("div")

            extraPanel.className = "article-pdf-extra-panel"
            extraTitle.textContent = "Approfondimenti"
            extraInfo.className = "article-pdf-extra"

            if(coordinateBox && !coordinateBox.textContent.includes("Coordinate non disponibili")){
                extraInfo.appendChild(coordinateBox)
            }

            if(linksBox){
                extraInfo.appendChild(linksBox)
            }

            if(extraInfo.children.length > 0){
                extraPanel.appendChild(extraTitle)
                extraPanel.appendChild(extraInfo)
                printableDocument.appendChild(extraPanel)
            }
        }

        printableWrapper.appendChild(printableDocument)
        document.body.appendChild(printableWrapper)

        return {printableWrapper, printableDocument}
    }

    exportButton.addEventListener("click", async () => {
        if(typeof window.html2pdf !== "function"){
            setFeedback("Esportazione PDF non disponibile.")
            return
        }

        const {printableWrapper, printableDocument} = buildPdfDocument()

        const options = {
            margin: 12,
            filename: filenameFromTitle(),
            image: {type: "jpeg", quality: 0.95},
            html2canvas: {scale: 2, useCORS: true},
            jsPDF: {unit: "mm", format: "a4", orientation: "portrait"},
        }

        try{
            exportButton.disabled = true
            setFeedback("Generazione PDF...")
            await window.html2pdf().set(options).from(printableDocument).save()
            setFeedback("PDF generato.")
        }catch(e){
            setFeedback("Esportazione PDF non riuscita.")
            console.error(e)
        }finally{
            printableWrapper.remove()
            exportButton.disabled = false
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
    initArticlePdfExport()
    initWeather()
})