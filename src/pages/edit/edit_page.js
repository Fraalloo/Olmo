const initMultipleFiles = () => {
    const pickerInput = document.getElementById("allegato_picker")
    const realInput = document.getElementById("allegati")
    const addFileButton = document.getElementById("add-file")
    const fileList = document.getElementById("file-list")
    const filePreviewBox = document.getElementById("file-preview-box")

    if(!pickerInput || !realInput || !addFileButton || !fileList || !filePreviewBox) return

    let selectedFiles = []

    const formatFileSize = bytes => {
        if(bytes < 1024) return `${bytes} B`

        if(bytes < 1024 * 1024){
            return `${(bytes / 1024).toFixed(1)} KB`
        }

        return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
    }

    const updateRealInput = () => {
        const dataTransfer = new DataTransfer()

        selectedFiles.forEach(file => {
            dataTransfer.items.add(file)
        })

        realInput.files = dataTransfer.files
    }

    const renderFileList = () => {
        fileList.innerHTML = ""

        if(selectedFiles.length === 0){
            filePreviewBox.hidden = true
            return
        }

        filePreviewBox.hidden = false

        selectedFiles.forEach((file, index) => {
            const li = document.createElement("li")

            const span = document.createElement("span")
            span.textContent = `${file.name} (${formatFileSize(file.size)})`

            const removeButton = document.createElement("button")
            removeButton.type = "button"
            removeButton.className = "btn-remove-resource"
            removeButton.textContent = "Rimuovi"

            removeButton.addEventListener("click", () => {
                selectedFiles = selectedFiles.filter((_, fileIndex) => fileIndex !== index)

                updateRealInput()
                renderFileList()
            })

            li.appendChild(span)
            li.appendChild(removeButton)

            fileList.appendChild(li)
        })
    }

    const addSelectedFiles = () => {
        const files = Array.from(pickerInput.files)

        if(files.length === 0){
            pickerInput.focus()
            return
        }

        files.forEach(file => {
            const alreadyExists = selectedFiles.some(existingFile => {
                return existingFile.name === file.name &&
                    existingFile.size === file.size &&
                    existingFile.lastModified === file.lastModified
            })

            if(!alreadyExists){
                selectedFiles.push(file)
            }
        })

        pickerInput.value = ""

        updateRealInput()
        renderFileList()
    }

    addFileButton.addEventListener("click", addSelectedFiles)
}

const initMultipleLinks = () => {
    const linkInput = document.getElementById("link_input")
    const addLinkButton = document.getElementById("add-link")
    const linkList = document.getElementById("link-list")
    const linkPreviewBox = document.getElementById("link-preview-box")
    const hiddenFieldsBox = document.getElementById("links-hidden-fields")

    if(!linkInput || !addLinkButton || !linkList || !linkPreviewBox || !hiddenFieldsBox) return

    let links = []

    const isValidUrl = value => {
        try{
            new URL(value)
            return true
        }catch(e){
            return false
        }
    }

    const updateHiddenFields = () => {
        hiddenFieldsBox.innerHTML = ""

        links.forEach(link => {
            const input = document.createElement("input")

            input.type = "hidden"
            input.name = "links[]"
            input.value = link

            hiddenFieldsBox.appendChild(input)
        })
    }

    const renderLinkList = () => {
        linkList.innerHTML = ""

        if(links.length === 0){
            linkPreviewBox.hidden = true
            return
        }

        linkPreviewBox.hidden = false

        links.forEach((link, index) => {
            const li = document.createElement("li")

            const span = document.createElement("span")
            span.textContent = link

            const removeButton = document.createElement("button")
            removeButton.type = "button"
            removeButton.className = "btn-remove-resource"
            removeButton.textContent = "Rimuovi"

            removeButton.addEventListener("click", () => {
                links = links.filter((_, linkIndex) => linkIndex !== index)

                updateHiddenFields()
                renderLinkList()
            })

            li.appendChild(span)
            li.appendChild(removeButton)

            linkList.appendChild(li)
        })
    }

    const addLink = () => {
        const link = linkInput.value.trim()

        if(link === "") return

        if(!isValidUrl(link)){
            linkInput.focus()
            linkInput.setCustomValidity("Inserisci un URL valido.")
            linkInput.reportValidity()
            return
        }

        linkInput.setCustomValidity("")

        if(!links.includes(link)){
            links.push(link)
        }

        linkInput.value = ""

        updateHiddenFields()
        renderLinkList()
    }

    addLinkButton.addEventListener("click", addLink)

    linkInput.addEventListener("keydown", e => {
        if(e.key === "Enter"){
            e.preventDefault()
            addLink()
        }
    })

    updateHiddenFields()
    renderLinkList()
}

const initCoordinatePicker = () => {
    const mapElement = document.getElementById("insert-map")
    const latInput = document.getElementById("latitudine")
    const lngInput = document.getElementById("longitudine")
    const clearButton = document.getElementById("clear-coords")
    const toggleButton = document.getElementById("toggle-map")
    const mapPicker = document.getElementById("map-picker")

    if(
        !mapElement ||
        !latInput ||
        !lngInput ||
        !clearButton ||
        !toggleButton ||
        !mapPicker ||
        typeof L === "undefined"
    ) return

    const initialLat = Number(latInput.value)
    const initialLng = Number(lngInput.value)
    const hasInitialCoords = Number.isFinite(initialLat) && Number.isFinite(initialLng)
    const defaultCenter = hasInitialCoords ? [initialLat, initialLng] : [41.7066, 15.7270]
    let map = null
    let marker = null

    const formatCoordinate = value => Number(value).toFixed(6)

    const initMap = () => {
        if(map) return

        map = L.map(mapElement).setView(defaultCenter, hasInitialCoords ? 15 : 13)

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 19,
            attribution: "&copy; OpenStreetMap contributors"
        }).addTo(map)

        if(hasInitialCoords){
            marker = L.marker(defaultCenter).addTo(map)
        }

        map.on("click", event => {
            const {lat, lng} = event.latlng

            setInputs(lat, lng)
            setMarker(lat, lng)
        })
    }

    const refreshMap = () => {
        initMap()
        setTimeout(() => map.invalidateSize(), 0)
    }

    const setMarker = (lat, lng, shouldCenter = false) => {
        initMap()

        const position = [lat, lng]

        if(marker){
            marker.setLatLng(position)
        }else{
            marker = L.marker(position).addTo(map)
        }

        if(shouldCenter){
            map.setView(position, 15)
        }
    }

    const setInputs = (lat, lng) => {
        latInput.value = formatCoordinate(lat)
        lngInput.value = formatCoordinate(lng)
    }

    const syncMarkerFromInputs = () => {
        const lat = Number(latInput.value)
        const lng = Number(lngInput.value)

        if(latInput.value === "" || lngInput.value === "") return
        if(!Number.isFinite(lat) || !Number.isFinite(lng)) return
        if(lat < -90 || lat > 90 || lng < -180 || lng > 180) return

        setMarker(lat, lng, true)
    }

    latInput.addEventListener("change", syncMarkerFromInputs)
    lngInput.addEventListener("change", syncMarkerFromInputs)

    toggleButton.addEventListener("click", () => {
        const isHidden = mapPicker.hidden

        mapPicker.hidden = !isHidden
        toggleButton.textContent = isHidden ? "Nascondi mappa" : "Mostra mappa"
        toggleButton.setAttribute("aria-expanded", isHidden ? "true" : "false")

        if(isHidden){
            refreshMap()
        }
    })

    clearButton.addEventListener("click", () => {
        latInput.value = ""
        lngInput.value = ""

        if(marker){
            map.removeLayer(marker)
            marker = null
        }
    })
}

const initEditPage = () => {
    initMultipleFiles()
    initMultipleLinks()
    initCoordinatePicker()
}

document.addEventListener("DOMContentLoaded", initEditPage)
