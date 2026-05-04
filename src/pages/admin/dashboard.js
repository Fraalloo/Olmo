let currentUserAdminForm = null

const modal = document.getElementById("userAdminModal")
const modalBox = document.getElementById("userAdminModalBox")
const modalTitle = document.getElementById("userAdminModalTitle")
const modalUsername = document.getElementById("userAdminModalUsername")
const modalMessage = document.getElementById("userAdminModalMessage")
const cancelBtn = document.getElementById("userAdminModalCancel")
const confirmBtn = document.getElementById("userAdminModalConfirm")

const modalConfig = {
    promote: {
        title: "Promuovere utente?",
        message: "Questo utente riceverà i privilegi da amministratore.",
        confirmText: "Promuovi",
        className: "modal-promote"
    },
    demote: {
        title: "Revocare admin?",
        message: "Questo utente perderà i privilegi da amministratore.",
        confirmText: "Revoca",
        className: "modal-demote"
    }
}

const closeUserAdminModal = () => {
    if(!modal || !modalBox){
        return
    }

    modal.hidden = true
    currentUserAdminForm = null
    document.body.classList.remove("modal-open")

    modalBox.classList.remove(
        "modal-promote",
        "modal-demote"
    )
}

document.querySelectorAll(".js-open-user-admin-modal").forEach(button => {
    button.addEventListener("click", () => {
        const action = button.dataset.action
        const config = modalConfig[action]

        if(!config || !modal || !modalBox){
            return
        }

        currentUserAdminForm = button.closest("form")

        modalTitle.textContent = config.title
        modalUsername.textContent = button.dataset.username || "utente selezionato"
        modalMessage.textContent = config.message
        confirmBtn.textContent = config.confirmText

        modalBox.classList.add(config.className)

        modal.hidden = false
        document.body.classList.add("modal-open")
    })
})

if(cancelBtn){
    cancelBtn.addEventListener("click", closeUserAdminModal)
}

if(confirmBtn){
    confirmBtn.addEventListener("click", () => {
        if(currentUserAdminForm){
            currentUserAdminForm.submit()
        }
    })
}

if(modal){
    modal.addEventListener("click", event => {
        if(event.target === modal){
            closeUserAdminModal()
        }
    })
}

document.addEventListener("keydown", event => {
    if(event.key === "Escape" && modal && !modal.hidden){
        closeUserAdminModal()
    }
})