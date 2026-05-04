<?php
    function render_article_admin_actions($articolo){
        if(empty($articolo)){
            return;
        }

        $id = (int)$articolo["id_articolo"];
        $titolo = esc($articolo["titolo"]);

        $isPending = empty($articolo["id_admin"]);
        $isHidden = !empty($articolo["is_hidden"]);
?>
    <div class="admin-article-actions">
        <?php if($isPending): ?>
            <form method="POST" action="../convalida/approve.php" class="admin-action-form approve-form">
                <input type="hidden" name="id" value="<?= $id ?>">

                <button
                    class="admin-action-btn btn-admin-approve js-open-admin-modal"
                    type="button"
                    data-action="approve"
                    data-title="<?= $titolo ?>"
                >
                    Convalida
                </button>
            </form>

            <form method="POST" action="../convalida/reject.php" class="admin-action-form reject-form">
                <input type="hidden" name="id" value="<?= $id ?>">

                <button
                    class="admin-action-btn btn-admin-reject js-open-admin-modal"
                    type="button"
                    data-action="reject"
                    data-title="<?= $titolo ?>"
                >
                    Rifiuta
                </button>
            </form>

        <?php elseif(!$isHidden): ?>
            <form method="POST" action="hide_article.php" class="admin-action-form hide-form">
                <input type="hidden" name="id" value="<?= $id ?>">

                <button
                    class="admin-action-btn btn-admin-hide js-open-admin-modal"
                    type="button"
                    data-action="hide"
                    data-title="<?= $titolo ?>"
                >
                    Elimina
                </button>
            </form>

        <?php else: ?>
            <form method="POST" action="restore_article.php" class="admin-action-form restore-form">
                <input type="hidden" name="id" value="<?= $id ?>">

                <button
                    class="admin-action-btn btn-admin-restore js-open-admin-modal"
                    type="button"
                    data-action="restore"
                    data-title="<?= $titolo ?>"
                >
                    Ripristina
                </button>
            </form>
        <?php endif; ?>
    </div>
<?php
    }

    function render_article_admin_modals(){
?>
    <div class="admin-modal-overlay" id="articleAdminModal" hidden>
        <div class="admin-modal" id="articleAdminModalBox">
            <h2 id="articleAdminModalTitle">Conferma azione</h2>

            <p>
                Articolo:
                <strong id="articleAdminModalArticleTitle"></strong>
            </p>

            <p class="admin-modal-message" id="articleAdminModalMessage"></p>

            <div class="admin-modal-actions">
                <button type="button" class="btn-modal-cancel" id="articleAdminModalCancel">
                    Annulla
                </button>

                <button type="button" class="btn-modal-confirm" id="articleAdminModalConfirm">
                    Conferma
                </button>
            </div>
        </div>
    </div>
<?php
    }
?>