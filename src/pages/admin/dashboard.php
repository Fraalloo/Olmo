<?php
    require_once "../../config/config.php";
    require_once "../../config/app.php";
    require_once "../../utils/utils.php";
    require_once "../../utils/auth_guard.php";
    require_once "../../utils/extract_articles.php";
    require_once "../../utils/admin_stats.php";

    require_once "admin_modal.php";
    require_once "../../components/footer/footer.php";

    session_start();

    const TO_ASSETS = "../../../";

    require_login();
    require_password_change_if_needed("../profile/profilo.php");
    require_admin();

    $pfp = $_SESSION["pfp"] ?? DEFAULT_PFP;
    $username = $_SESSION["username"] ?? "";

    $stats = [
        "utenti" => count_users($conn),
        "admin" => count_admins($conn),
        "attivi" => count_active_articles($conn),
        "pending" => count_pending_articles($conn),
        "hidden" => count_hidden_articles($conn),
        "versioni" => count_total_versions($conn),
    ];

    $page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
    if($page < 1){
        $page = 1;
    }

    $limit = 10;

    $userSearch = isset($_GET["user_search"]) ? trim($_GET["user_search"]) : "";
    $userRole = isset($_GET["role"]) ? trim($_GET["role"]) : "";
    $userSecurity = isset($_GET["security"]) ? trim($_GET["security"]) : "";
    $userDateFrom = isset($_GET["date_from"]) ? trim($_GET["date_from"]) : "";
    $userDateTo = isset($_GET["date_to"]) ? trim($_GET["date_to"]) : "";

    $userFilters = [
        "search" => $userSearch,
        "role" => $userRole,
        "security" => $userSecurity,
        "date_from" => $userDateFrom,
        "date_to" => $userDateTo,
    ];

    $queryExtra = "";

    if($userSearch !== "") $queryExtra .= "&user_search=" . urlencode($userSearch);
    if($userRole !== "") $queryExtra .= "&role=" . urlencode($userRole);
    if($userSecurity !== "") $queryExtra .= "&security=" . urlencode($userSecurity);
    if($userDateFrom !== "") $queryExtra .= "&date_from=" . urlencode($userDateFrom);
    if($userDateTo !== "") $queryExtra .= "&date_to=" . urlencode($userDateTo);

    $totalUsers = count_filtered_users($conn, $userFilters);
    $totalUserPages = max(1, (int)ceil($totalUsers / $limit));

    $utenti = extract_users_for_admin($conn, $limit, $page, $userFilters);
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Dashboard Admin</title>

        <link rel="stylesheet" href="../../../style.css">
        <link rel="stylesheet" href="../../components/footer/footer.css">
        <link rel="stylesheet" href="dashboard.css">
    </head>

    <body>
        <header class="topbar">
            <div class="topbar_left">
                <a class="topbar_profile" href="../profile/profile.php">
                    <img class="topbar_pfp" src="<?= esc(TO_ASSETS . $pfp) ?>" alt="Foto profilo">
                    <span class="topbar_username"><?= esc($username) ?></span>
                </a>
            </div>

            <nav class="topbar_nav">
                <a href="../home/home.php">Home</a>
                <a href="../convalida/convalida.php">Convalida</a>
                <a class="btn-logout" href="../../auth/logout.php">Logout</a>
            </nav>
        </header>

        <main class="admin-page">
            <section class="admin-hero">
                <h1>Dashboard Admin</h1>
                <p>Statistiche, moderazione e gestione utenti.</p>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <strong><?= (int)$stats["utenti"] ?></strong>
                    <span>Utenti</span>
                </div>

                <div class="stat-card">
                    <strong><?= (int)$stats["admin"] ?></strong>
                    <span>Admin</span>
                </div>

                <div class="stat-card">
                    <strong><?= (int)$stats["attivi"] ?></strong>
                    <span>Articoli attivi</span>
                </div>

                <div class="stat-card">
                    <strong><?= (int)$stats["pending"] ?></strong>
                    <span>Da convalidare</span>
                </div>

                <div class="stat-card">
                    <strong><?= (int)$stats["hidden"] ?></strong>
                    <span>Hidden</span>
                </div>

                <div class="stat-card">
                    <strong><?= (int)$stats["versioni"] ?></strong>
                    <span>Versioni totali</span>
                </div>
            </section>

            <section class="admin-panel">
                <div class="panel-header">
                    <div>
                        <h2>Gestione utenti</h2>
                        <p>Promuovi o revoca privilegi admin.</p>
                    </div>

                    <a class="btn-panel" href="../convalida/convalida.php">
                        Vai alla convalida
                    </a>
                </div>

                <div class="users-table-wrapper">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>Utente</th>
                                <th>Ruolo</th>
                                <th>Registrazione</th>
                                <th>Sicurezza</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach($utenti as $utente): ?>
                                <?php
                                    $isCurrentUser = (int)$utente["id_utente"] === (int)$_SESSION["user_id"];
                                    $isBaseAdmin = $utente["id_utente"] == 1;
                                ?>
        
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <img
                                                src="<?= esc(TO_ASSETS . (!empty($utente["pfp"]) ? $utente["pfp"] : DEFAULT_PFP)) ?>"
                                                alt="Foto profilo"
                                            >
                                            <span><?= esc($utente["nome_utente"]) ?></span>
                                        </div>
                                    </td>

                                    <td>
                                        <?php if(!empty($utente["is_admin"])): ?>
                                            <span class="role-badge role-admin">Admin</span>
                                        <?php else: ?>
                                            <span class="role-badge role-user">Utente</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?= esc(date("d/m/Y", strtotime($utente["data_registrazione"]))) ?>
                                    </td>

                                    <td>
                                        <?php if(!empty($utente["must_change_password"])): ?>
                                            <span class="security-badge security-warning">Cambio password</span>
                                        <?php else: ?>
                                            <span class="security-badge security-ok">OK</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if($isCurrentUser): ?>
                                            <span class="muted">Tu</span>

                                        <?php elseif($isBaseAdmin): ?>
                                            <span class="muted">Admin base</span>

                                        <?php elseif(empty($utente["is_admin"])): ?>
                                            <form method="POST" action="promote_user.php" class="user-admin-form">
                                                <input type="hidden" name="id" value="<?= (int)$utente["id_utente"] ?>">

                                                <button 
                                                    class="btn-promote js-open-user-admin-modal"
                                                    type="button"
                                                    data-action="promote"
                                                    data-username="<?= esc($utente["nome_utente"]) ?>"
                                                >
                                                    Promuovi
                                                </button>
                                            </form>

                                        <?php else: ?>
                                            <form method="POST" action="demote_user.php" class="user-admin-form">
                                                <input type="hidden" name="id" value="<?= (int)$utente["id_utente"] ?>">

                                                <button 
                                                    class="btn-demote js-open-user-admin-modal"
                                                    type="button"
                                                    data-action="demote"
                                                    data-username="<?= esc($utente["nome_utente"]) ?>"
                                                >
                                                    Revoca
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if($totalUserPages > 1): ?>
                        <div class="pagination">
                            <?php if($page > 1): ?>
                                <a class="page-arrow" href="?page=<?= $page - 1 ?><?= $queryExtra ?>">←</a>
                            <?php endif; ?>

                            <span>Pagina <?= $page ?> di <?= $totalUserPages ?></span>

                            <?php if($page < $totalUserPages): ?>
                                <a class="page-arrow" href="?page=<?= $page + 1 ?><?= $queryExtra ?>">→</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <form method="GET" action="dashboard.php" class="admin-user-filters">
                <input 
                    type="text" 
                    name="user_search" 
                    placeholder="Cerca nome utente..."
                    value="<?= esc($userSearch) ?>"
                >

                <select name="role">
                    <option value="">Tutti i ruoli</option>
                    <option value="admin" <?= $userRole === "admin" ? "selected" : "" ?>>Solo admin</option>
                    <option value="user" <?= $userRole === "user" ? "selected" : "" ?>>Solo utenti</option>
                </select>

                <select name="security">
                    <option value="">Sicurezza: tutti</option>
                    <option value="must_change" <?= $userSecurity === "must_change" ? "selected" : "" ?>>
                        Cambio password richiesto
                    </option>
                    <option value="ok" <?= $userSecurity === "ok" ? "selected" : "" ?>>
                        OK
                    </option>
                </select>

                <label>
                    Da
                    <input 
                        type="date" 
                        name="date_from"
                        value="<?= esc($userDateFrom) ?>"
                    >
                </label>

                <label>
                    A
                    <input 
                        type="date" 
                        name="date_to"
                        value="<?= esc($userDateTo) ?>"
                    >
                </label>

                <button type="submit">Filtra</button>

                <a href="dashboard.php">Annulla</a>
            </form>
        </main>

        <?php render_footer("home-button", "../home/home.php", "Home"); ?>

        <?php render_user_admin_modal() ?>
        <script src="dashboard.js"></script>
    </body>
</html>