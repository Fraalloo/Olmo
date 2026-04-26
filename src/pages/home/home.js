import {escapeHtml} from "../../utils/utils.js"
import {fetch_nominatim} from "../../utils/api.js"

const defaultCenter = [41.7066, 15.7270] // Coordinate SGR
const map = L.map('map').setView(defaultCenter, 14)

const markersLayer = L.layerGroup().addTo(map)
const bounds = []
const articleMarkers = new Map()

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map)

const buildPopupContent = item => {
    const shortDescription = item.descrizione.length > 120
        ? item.descrizione.substring(0, 120) + '...'
        : item.descrizione

    return `
        <div class="popup-content">
            <strong>${escapeHtml(item.titolo)}</strong><br>
            <span>${escapeHtml(item.tipo_articolo)}</span><br>
            <small>${escapeHtml(shortDescription)}</small><br><br>
            <a href="#">Apri articolo</a>
        </div>
    `
}

const initMarkers = () => {
    window.mapArticles.forEach(item => {
        if(item.latitudine !== null && item.longitudine !== null){
            const marker = L.marker([item.latitudine, item.longitudine])
                .addTo(markersLayer)
                .bindPopup(buildPopupContent(item))

            articleMarkers.set(item.id_articolo, marker)
            bounds.push([item.latitudine, item.longitudine])
        }
    })
}

const zoomToMarkers = () => {
    if(bounds.length > 0){
        map.fitBounds(bounds, {padding: [40, 40]})
    }
}

const goToMap = (lat, lng) => {
    document.getElementById("map").scrollIntoView({
        behavior: "smooth",
        block: "center"
    })

    map.flyTo([lat, lng], 14)

    const marker = [...articleMarkers.values()].find(m => {
        const pos = m.getLatLng()
        return pos.lat == lat && pos.lng == lng
    })

    if(marker){
        setTimeout(() => marker.openPopup(), 500)
    }
}

const initLocateButtons = () => {
    document.querySelectorAll('.locate-on-map').forEach(button => {
        button.addEventListener('click', function (){
            const lat = parseFloat(this.dataset.lat)
            const lng = parseFloat(this.dataset.lng)

            goToMap(lat, lng)
        })
    })

    document.getElementById('zoomToMarkersBtn').addEventListener('click', zoomToMarkers)
}

const initNominatimSearch = () => {
    document.getElementById('nominatimForm').addEventListener('submit', async event => {
        event.preventDefault()

        const query = document.getElementById('nominatimSearch').value.trim()
        if(!query) return

        try{
            const place = await fetch_nominatim(query)

            if(!place){
                alert('Luogo non trovato.')
                return
            }

            map.setView([place.lat, place.lon], 14)

            const marker = L.marker([place.lat, place.lon]).addTo(map)
            marker.bindPopup(`
                <strong>${escapeHtml(place.name)}</strong>
            `).openPopup()
        }catch(error){
            alert('Errore durante la ricerca del luogo.')
        }
    })
}

initMarkers()
initLocateButtons()
initNominatimSearch()