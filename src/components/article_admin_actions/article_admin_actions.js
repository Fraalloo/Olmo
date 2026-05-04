let currentAdminActionForm = null

const modal = document.getElementById("articleAdminModal")
const modalBox = document.getElementById("articleAdminModalBox")
const modalTitle = document.getElementById("articleAdminModalTitle")
const articleTitle = document.getElementById("articleAdminModalArticleTitle")
const modalMessage = document.getElementById("articleAdminModalMessage")
const cancelBtn = document.getElementById("articleAdminModalCancel")
const confirmBtn = document.getElementById("articleAdminModalConfirm")

const modalConfig = {
    approve: {
        title: "Convalidare articolo?",
        message: "Questo articolo verrà approvato e diventerà la nuova versione attiva.",
        confirmText: "Convalida",
        className: "modal-approve"
    },
    reject: {
        title: "Rifiutare articolo?",
        message: "Questa azione eliminerà definitivamente la proposta dal database.",
        confirmText: "Rifiuta",
        className: "modal-reject"
    },
    hide: {
        title: "Eliminare articolo?",
        message: "L’articolo verrà nascosto e non sarà più visibile nella Home.",
        confirmText: "Elimina",
        className: "modal-hide"
    },
    restore: {
        title: "Ripristinare articolo?",
        message: "Questa versione verrà ripristinata e tornerà a essere la versione attiva.",
        confirmText: "Ripristina",
        className: "modal-restore"
    }
}

const closeModal = () => {
    if(!modal || !modalBox){
        return
    }

    modal.hidden = true
    currentAdminActionForm = null
    document.body.classList.remove("modal-open")

    modalBox.classList.remove(
        "modal-approve",
        "modal-reject",
        "modal-hide",
        "modal-restore"
    )
}

document.querySelectorAll(".js-open-admin-modal").forEach(button => {
    button.addEventListener("click", () => {
        const action = button.dataset.action
        const config = modalConfig[action]

        if(!config || !modal || !modalBox){
            return
        }

        currentAdminActionForm = button.closest("form")

        modalTitle.textContent = config.title
        articleTitle.textContent = button.dataset.title || "questo articolo"
        modalMessage.textContent = config.message
        confirmBtn.textContent = config.confirmText

        modalBox.classList.add(config.className)

        modal.hidden = false
        document.body.classList.add("modal-open")
    })
})

if(cancelBtn){
    cancelBtn.addEventListener("click", closeModal)
}

if(confirmBtn){
    confirmBtn.addEventListener("click", () => {
        if(currentAdminActionForm){
            currentAdminActionForm.submit()
        }
    })
}

if(modal){
    modal.addEventListener("click", event => {
        if(event.target === modal){
            closeModal()
        }
    })
}

document.addEventListener("keydown", event => {
    if(event.key === "Escape" && modal && !modal.hidden){
        closeModal()
    }
})