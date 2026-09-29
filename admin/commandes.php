<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
exigerConnexion();

// =========================================================
// CHANGEMENT DE STATUT
// =========================================================
if (isset($_GET['statut'], $_GET['id'])) {
    $idStatut = (int)$_GET['id'];
    $statut   = $_GET['statut'];
    $statutsValides = ['en_attente', 'confirmee', 'livree', 'annulee'];

    if (in_array($statut, $statutsValides) && $idStatut > 0) {
        $stmt = $pdo->prepare('UPDATE commandes SET statut = ? WHERE id = ?');
        $stmt->execute([$statut, $idStatut]);
    }
    header('Location: commandes.php?msg=Statut mis à jour');
    exit;
}

// =========================================================
// SUPPRESSION
// =========================================================
if (isset($_GET['supprimer'])) {
    $idSuppr = (int)$_GET['supprimer'];
    if ($idSuppr > 0) {
        $pdo->prepare('DELETE FROM commandes WHERE id = ?')->execute([$idSuppr]);
    }
    header('Location: commandes.php?msg=Commande supprimée');
    exit;
}

// =========================================================
// FILTRE
// =========================================================
$filtreStatut = $_GET['filtre'] ?? '';
$sql = 'SELECT * FROM commandes';
$params = [];

if ($filtreStatut && in_array($filtreStatut, ['en_attente', 'confirmee', 'livree', 'annulee'])) {
    $sql .= ' WHERE statut = ?';
    $params[] = $filtreStatut;
}
$sql .= ' ORDER BY created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$commandes = $stmt->fetchAll();

// =========================================================
// STATISTIQUES
// =========================================================
$stats = $pdo->query('
    SELECT
        COUNT(*) AS total_commandes,
        COALESCE(SUM(total), 0) AS chiffre_affaires,
        SUM(CASE WHEN statut = "en_attente" THEN 1 ELSE 0 END) AS en_attente,
        SUM(CASE WHEN statut = "livree" THEN 1 ELSE 0 END) AS livrees
    FROM commandes
')->fetch();

// =========================================================
// FONCTIONS UTILITAIRES
// =========================================================
function libelleStatut(string $statut): string
{
    return match ($statut) {
        'en_attente' => 'En attente',
        'confirmee'  => 'Confirmée',
        'livree'     => 'Livrée',
        'annulee'    => 'Annulée',
        default      => $statut,
    };
}

function badgeStatut(string $statut): string
{
    return match ($statut) {
        'en_attente' => 'warning text-dark',
        'confirmee'  => 'info text-dark',
        'livree'     => 'success',
        'annulee'    => 'danger',
        default      => 'secondary',
    };
}

/**
 * Construit le lien WhatsApp avec indicatif 229 si nécessaire
 */
function lienWhatsApp(?string $contact): string
{
    $numero = preg_replace('/[^0-9]/', '', $contact ?? '');

    if ($numero === '') {
        return '#';
    }

    if (!str_starts_with($numero, '229')) {
        $numero = '229' . $numero;
    }

    return 'https://wa.me/' . $numero;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Commandes</title>
    <link rel="stylesheet" href="../bootstrap-5.2.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap-iconss/font/bootstrap-icons.css">
    <style>
        /* Petit ajustement : bouton accordéon sans bordure sur mobile */
        @media (max-width: 576px) {
            .accordion-button {
                padding: 0.75rem;
            }
            .accordion-button::after {
                margin-left: 0.5rem;
                flex-shrink: 0;
            }
            .accordion-body {
                padding: 1rem 0.75rem;
            }
        }
        .badge-statut {
            font-size: 0.8rem;
        }
        .btn-action {
            font-size: 0.85rem;
        }
    </style>
</head>
<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark py-3">
    <div class="container-fluid container-lg">
        <a class="navbar-brand fs-5" href="index.php">
            <i class="bi bi-shop"></i> Admin
        </a>
        <div class="d-flex flex-wrap gap-2">
            <a href="index.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left"></i>
                <span class="">Dashboard</span>
            </a>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i>
                <span class="">Déconnexion</span>
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid container-lg py-4 px-3">

    <h2 class="fs-3 fw-bold mb-4">
        <i class="bi bi-receipt"></i> Commandes reçues
    </h2>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success fs-6">
            <?= htmlspecialchars($_GET['msg']) ?>
        </div>
    <?php endif; ?>

    <!-- =========================================================
         STATISTIQUES
         ========================================================= -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-3 p-md-4">
                    <i class="bi bi-cart-check fs-2 text-primary"></i>
                    <h3 class="mt-2 mb-1 fs-3">
                        <?= $stats['total_commandes'] ?>
                    </h3>
                    <div class="text-muted small">Commandes totales</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-3 p-md-4">
                    <i class="bi bi-hourglass-split fs-2 text-warning"></i>
                    <h3 class="mt-2 mb-1 fs-3"><?= $stats['en_attente'] ?></h3>
                    <div class="text-muted small">En attente</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-3 p-md-4">
                    <i class="bi bi-check-circle fs-2 text-success"></i>
                    <h3 class="mt-2 mb-1 fs-3"><?= $stats['livrees'] ?></h3>
                    <div class="text-muted small">Livrées</div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================
         FILTRES
         ========================================================= -->
    <div class="mb-3 d-flex flex-wrap gap-2">
        <a href="commandes.php"
            class="btn btn-sm <?= $filtreStatut === '' ? 'btn-dark' : 'btn-outline-dark' ?>">
            Toutes
        </a>
        <a href="commandes.php?filtre=en_attente"
            class="btn btn-sm <?= $filtreStatut === 'en_attente' ? 'btn-warning' : 'btn-outline-warning' ?>">
            En attente
        </a>
        <a href="commandes.php?filtre=confirmee"
            class="btn btn-sm <?= $filtreStatut === 'confirmee' ? 'btn-info' : 'btn-outline-info' ?>">
            Confirmées
        </a>
        <a href="commandes.php?filtre=livree"
            class="btn btn-sm <?= $filtreStatut === 'livree' ? 'btn-success' : 'btn-outline-success' ?>">
            Livrées
        </a>
        <a href="commandes.php?filtre=annulee"
            class="btn btn-sm <?= $filtreStatut === 'annulee' ? 'btn-danger' : 'btn-outline-danger' ?>">
            Annulées
        </a>
    </div>

    <!-- =========================================================
         LISTE DES COMMANDES
         ========================================================= -->
    <?php if (empty($commandes)): ?>
        <div class="alert alert-info text-center py-4">
            <i class="bi bi-inbox fs-1"></i>
            <h5 class="mt-2 fs-6">Aucune commande pour le moment</h5>
            <p class="mb-0 small">Les commandes passées apparaîtront ici.</p>
        </div>
    <?php else: ?>
        <div class="accordion" id="accordionCommandes">
            <?php foreach ($commandes as $cmd): ?>
                <?php
                    $statut = $cmd['statut'] ?? 'en_attente';
                    $waLink = lienWhatsApp($cmd['client_contact']);
                ?>
                <div class="accordion-item mb-2 shadow-sm border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#cmd<?= $cmd['id'] ?>">

                            <div class="d-flex flex-column flex-sm-row
                                        justify-content-between
                                        align-items-start align-items-sm-center
                                        w-100 gap-2">

                                <!-- Infos client -->
                                <div class="flex-grow-1 text-start">
                                    <div class="fw-bold">
                                        #<?= $cmd['id'] ?> —
                                        <?= htmlspecialchars($cmd['client_nom'] ?: 'Client anonyme') ?>
                                    </div>
                                    <small class="text-muted d-block">
                                        <i class="bi bi-telephone"></i>
                                        <?= htmlspecialchars($cmd['client_contact'] ?: '—') ?>
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="bi bi-clock"></i>
                                        <?= date('d/m/Y à H:i', strtotime($cmd['created_at'])) ?>
                                    </small>
                                </div>

                                <!-- Statut + total -->
                                <div class="text-start text-sm-end">
                                    <span class="badge bg-<?= badgeStatut($statut) ?> badge-statut">
                                        <?= libelleStatut($statut) ?>
                                    </span>
                                    <div class="fw-bold text-primary mt-1">
                                        <?= number_format($cmd['total'], 0, ',', ' ') ?> FCFA
                                    </div>
                                </div>
                            </div>
                        </button>
                    </h2>

                    <div id="cmd<?= $cmd['id'] ?>" class="accordion-collapse collapse"
                        data-bs-parent="#accordionCommandes">

                        <div class="accordion-body p-3">

                            <h6 class="text-muted mb-3 small fw-bold">
                                <i class="bi bi-bag"></i> Détails de la commande
                            </h6>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Détails</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $lignes = array_filter(
                                            explode("\n", $cmd['details'] ?? '')
                                        );
                                        if (empty($lignes)):
                                        ?>
                                            <tr>
                                                <td class="text-muted text-center small">
                                                    Aucun détail enregistré
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($lignes as $ligne): ?>
                                                <tr>
                                                    <td class="small">
                                                        <?= htmlspecialchars($ligne) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-dark">
                                            <td class="text-end">
                                                <strong>
                                                    TOTAL : <?= number_format($cmd['total'], 0, ',', ' ') ?> FCFA
                                                </strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Actions -->
                            <div class="d-grid d-sm-flex flex-wrap gap-2 mt-3">

                                <a href="commandes.php?id=<?= $cmd['id'] ?>&statut=confirmee"
                                    class="btn btn-sm btn-info btn-action">
                                    <i class="bi bi-check"></i> Confirmer
                                </a>

                                <a href="commandes.php?id=<?= $cmd['id'] ?>&statut=livree"
                                    class="btn btn-sm btn-success btn-action">
                                    <i class="bi bi-check-circle"></i> Marquer livrée
                                </a>

                                <a href="commandes.php?id=<?= $cmd['id'] ?>&statut=annulee"
                                    class="btn btn-sm btn-danger btn-action">
                                    <i class="bi bi-x-circle"></i> Annuler
                                </a>

                                <?php if ($waLink !== '#'): ?>
                                    <a href="<?= $waLink ?>"
                                        target="_blank"
                                        class="btn btn-sm btn-outline-success btn-action">
                                        <i class="bi bi-whatsapp"></i> WhatsApp
                                    </a>
                                <?php endif; ?>

                                <a href="commandes.php?supprimer=<?= $cmd['id'] ?>"
                                    class="btn btn-sm btn-outline-danger btn-action ms-sm-auto"
                                    onclick="return confirm('Supprimer définitivement cette commande ?')">
                                    <i class="bi bi-trash"></i> Supprimer
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="../bootstrap-5.2.3-dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>