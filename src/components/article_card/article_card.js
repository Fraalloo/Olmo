let currentApproveForm = null
let currentRejectForm = null

const approveModal = document.getElementById("approveModal")
const rejectModal = document.getElementById("rejectModal")

const approveTitleBox = document.getElementById("approveArticleTitle")
const rejectTitleBox = document.getElementById("rejectArticleTitle")

const cancelApproveBtn = document.getElementById("cancelApprove")
const confirmApproveBtn = document.getElementById("confirmApprove")

const cancelRejectBtn = document.getElementById("cancelReject")
const confirmRejectBtn = document.getElementById("confirmReject")

const closeApproveModal = () => {
    if(!approveModal) return

    approveModal.hidden = true
    currentApproveForm = null
    document.body.classList.remove("modal-open")
}

const closeRejectModal = () => {
    if(!rejectModal) return

    rejectModal.hidden = true
    currentRejectForm = null
    document.body.classList.remove("modal-open")
}

document.querySelectorAll(".btn-open-approve-modal").forEach(button => {
    button.addEventListener("click", () => {
        currentApproveForm = button.closest("form")

        if(approveTitleBox){
            approveTitleBox.textContent = button.dataset.title || "questo articolo"
        }

        if(approveModal){
            approveModal.hidden = false
            document.body.classList.add("modal-open")
        }
    })
})

document.querySelectorAll(".btn-open-reject-modal").forEach(button => {
    button.addEventListener("click", () => {
        currentRejectForm = button.closest("form")

        if(rejectTitleBox){
            rejectTitleBox.textContent = button.dataset.title || "questo articolo"
        }

        if(rejectModal){
            rejectModal.hidden = false
            document.body.classList.add("modal-open")
        }
    })
})

if(cancelApproveBtn){
    cancelApproveBtn.addEventListener("click", closeApproveModal)
}

if(cancelRejectBtn){
    cancelRejectBtn.addEventListener("click", closeRejectModal)
}

if(confirmApproveBtn){
    confirmApproveBtn.addEventListener("click", () => {
        if(currentApproveForm){
            currentApproveForm.submit()
        }
    })
}

if(confirmRejectBtn){
    confirmRejectBtn.addEventListener("click", () => {
        if(currentRejectForm){
            currentRejectForm.submit()
        }
    })
}

if(approveModal){
    approveModal.addEventListener("click", event => {
        if(event.target === approveModal){
            closeApproveModal()
        }
    })
}

if(rejectModal){
    rejectModal.addEventListener("click", event => {
        if(event.target === rejectModal){
            closeRejectModal()
        }
    })
}

document.addEventListener("keydown", event => {
    if(event.key !== "Escape"){
        return
    }

    if(approveModal && !approveModal.hidden){
        closeApproveModal()
    }

    if(rejectModal && !rejectModal.hidden){
        closeRejectModal()
    }
})