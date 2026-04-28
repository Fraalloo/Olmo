export const fetch_weather = async (lat, lng) => {
    const url = `https://api.open-meteo.com/v1/forecast?latitude=${encodeURIComponent(lat)}&longitude=${encodeURIComponent(lng)}&current=temperature_2m,apparent_temperature,weather_code,wind_speed_10m`
    const response = await fetch(url)

    if(!response.ok){
        throw new Error("Errore nel recupero del meteo")
    }

    const data = await response.json()
    if(!data.current){
        return null
    }

    return {
        temperatura: data.current.temperature_2m,
        temperaturaPercepita: data.current.apparent_temperature,
        vento: data.current.wind_speed_10m,
        codiceMeteo: data.current.weather_code
    }
}

export const fetch_wikipedia = async placeName => {
    const url = `https://it.wikipedia.org/api/rest_v1/page/summary/${encodeURIComponent(placeName)}`

    const response = await fetch(url)
    if(!response.ok){
        return null
    }

    const data = await response.json()

    return {
        titolo: data.title || placeName,
        estratto: data.extract || "Nessun estratto disponibile.",
        url: data.content_urls?.desktop?.page || null
    }
}

export async function fetch_nominatim(query) {
    const url = `https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(query)}`

    const response = await fetch(url, {
        headers: {
            'Accept': 'application/json'
        }
    })

    if(!response.ok){
        throw new Error("Errore richiesta API")
    }

    const data = await response.json()

    if(!data.length){
        return null
    }
    const place = data[0]

    return {
        lat: parseFloat(place.lat),
        lon: parseFloat(place.lon),
        name: place.display_name
    }
}