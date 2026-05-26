<?php
    function render_footer($buttonClass = "home-button", $buttonHref = "#", $buttonText = "Home"){
        $version = defined("CURR_VERS") ? CURR_VERS : "";
        $privacyUrl = function_exists("other_page_url") ? other_page_url("privacy_policy") : "#";
        $termsUrl = function_exists("other_page_url") ? other_page_url("terms_of_service") : "#";
        $cookieUrl = function_exists("other_page_url") ? other_page_url("cookie_settings") : "#";
?>
    <footer class="footer">
        <div class="footer-inner">
            <p class="footer-copy">© 2026 Sotto l'Olmo. All rights reserved.</p>

            <a href="<?= htmlspecialchars($buttonHref) ?>" class="<?= htmlspecialchars($buttonClass) ?>">
                <?= htmlspecialchars($buttonText) ?>
            </a>

            <div class="footer-links">
                <a href="<?= htmlspecialchars($privacyUrl) ?>">Privacy Policy</a>
                <a href="<?= htmlspecialchars($termsUrl) ?>">Terms of Service</a>
                <a href="<?= htmlspecialchars($cookieUrl) ?>">Cookie Settings</a>
                <a href="https://github.com/Fraalloo/Olmo">GitHub Repository</a>

                <?php if($version !== ""): ?>
                    <p>version <?= htmlspecialchars($version) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </footer>
<?php
    }
?>