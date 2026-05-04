<?php
    function render_user_admin_modal(){
?>
    <div class="user-admin-modal-overlay" id="userAdminModal" hidden>
        <div class="user-admin-modal" id="userAdminModalBox">
            <h2 id="userAdminModalTitle">Conferma azione</h2>

            <p>
                Utente:
                <strong id="userAdminModalUsername"></strong>
            </p>

            <p class="user-admin-modal-message" id="userAdminModalMessage"></p>

            <div class="user-admin-modal-actions">
                <button type="button" class="btn-modal-cancel" id="userAdminModalCancel">
                    Annulla
                </button>

                <button type="button" class="btn-modal-confirm" id="userAdminModalConfirm">
                    Conferma
                </button>
            </div>
        </div>
    </div>
<?php
    }
?>