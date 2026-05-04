<?php
function render_article_card($articolo, $options = []){
    $toAssets = $options["to_assets"] ?? "../../../";
    $openUrl = $options["open_url"] ?? "../article/article.php";
    $showMapButton = $options["show_map_button"] ?? true;
    $showValidationActions = $options["show_validation_actions"] ?? false;

    $hasCoords = $articolo["latitudine"] !== null && $articolo["longitudine"] !== null;
    $banner = !empty($articolo["banner"]) ? $toAssets . $articolo["banner"] : "";
?>
    <article
        class="article-card <?= $hasCoords ? 'has-coords' : 'no-coords' ?>"
        data-article-id="<?= (int)$articolo["id_articolo"] ?>"
        data-title="<?= esc($articolo["titolo"]) ?>"
        data-lat="<?= $hasCoords ? esc($articolo["latitudine"]) : '' ?>"
        data-lng="<?= $hasCoords ? esc($articolo["longitudine"]) : '' ?>"
    >
        <div class="article-card-header">
            <div>
                <span class="type-badge type-<?= esc($articolo["tipo_articolo"]) ?>">
                    <?= esc(ucfirst($articolo["tipo_articolo"])) ?>
                </span>

                <?php if(!$hasCoords): ?>
                    <span class="coords-badge coords-badge--missing">Senza coordinate</span>
                <?php else: ?>
                    <span class="coords-badge coords-badge--ok">In mappa</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="article-card-body">
            <?php if(!empty($banner)): ?>
                <img class="article-banner" src="<?= esc($banner) ?>" alt="Banner <?= esc($articolo["titolo"]) ?>">
            <?php endif; ?>

            <div class="article-content">
                <h3><?= esc($articolo["titolo"]) ?></h3>

                <p class="meta">
                    Pubblicato da <strong><?= esc($articolo["autore"]) ?></strong>
                    il <?= esc(date("d/m/Y", strtotime($articolo["data_pubblicazione"]))) ?>
                </p>

                <p class="description">
                    <?= substr(nl2br(esc($articolo["descrizione"])), 0, 500) ?>
                    <?php if(strlen($articolo["descrizione"]) > 500): ?>
                        <span class="description-cont">(continua...)</span>
                    <?php endif; ?>
                </p>

                <?php if($hasCoords): ?>
                    <p class="coords">
                        Coordinate: <?= esc($articolo["latitudine"]) ?>, <?= esc($articolo["longitudine"]) ?>
                    </p>
                <?php endif; ?>

                <div class="article-actions">
                    <?php if($showMapButton && $hasCoords): ?>
                        <button
                            class="locate-on-map"
                            data-lat="<?= esc($articolo["latitudine"]) ?>"
                            data-lng="<?= esc($articolo["longitudine"]) ?>"
                        >
                            Mostra sulla mappa
                        </button>
                    <?php endif; ?>

                    <a class="btn-primary btn-apri" href="<?= esc($openUrl) ?>?id=<?= (int)$articolo["id_articolo"] ?>">
                        Apri
                    </a>

                    <?php if($showValidationActions): ?>
                        <form method="POST" action="../convalida/approve.php" class="approve-form">
                            <input type="hidden" name="id" value="<?= (int)$articolo["id_articolo"] ?>">

                            <button 
                                class="btn-primary btn-open-approve-modal" 
                                type="button"
                                data-title="<?= esc($articolo["titolo"]) ?>"
                            >
                                Approva
                            </button>
                        </form>

                        <form method="POST" action="../convalida/reject.php" class="reject-form">
                            <input type="hidden" name="id" value="<?= (int)$articolo["id_articolo"] ?>">

                            <button 
                                class="btn-delete btn-open-reject-modal" 
                                type="button"
                                data-title="<?= esc($articolo["titolo"]) ?>"
                            >
                                Rifiuta
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </article>
<?php
}
?>

<?php
function render_article_validation_modals(){
?>
    <div class="approve-modal-overlay" id="approveModal" hidden>
        <div class="validate-modal approve-modal">
            <h2>Approvare articolo?</h2>

            <p>
                Stai per approvare:
                <strong id="approveArticleTitle"></strong>
            </p>

            <p class="approve-warning">
                Questo articolo diventerà visibile nella Home.
            </p>

            <div class="validate-modal-actions">
                <button type="button" class="btn-modal-cancel" id="cancelApprove">
                    Annulla
                </button>

                <button type="button" class="btn-modal-approve" id="confirmApprove">
                    Approva
                </button>
            </div>
        </div>
    </div>

    <div class="reject-modal-overlay" id="rejectModal" hidden>
        <div class="validate-modal reject-modal">
            <h2>Rifiutare articolo?</h2>

            <p>
                Stai per rifiutare:
                <strong id="rejectArticleTitle"></strong>
            </p>

            <p class="reject-warning">
                Questa azione eliminerà l’articolo dal database.
            </p>

            <div class="validate-modal-actions">
                <button type="button" class="btn-modal-cancel" id="cancelReject">
                    Annulla
                </button>

                <button type="button" class="btn-modal-reject" id="confirmReject">
                    Rifiuta
                </button>
            </div>
        </div>
    </div>
<?php
}
?>