<?php
/** @var array<int,array<string,mixed>> $customers */
/** @var array<int,array<string,mixed>> $transactions */
?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h3 class="h6 mb-0">Adjust EcoPoints</h3>
            </div>
            <div class="card-body">
                <form method="post" action="ecopoints.php" class="vstack gap-3">
                    <div>
                        <label class="form-label" for="customer_id">Customer</label>
                        <select class="form-select" id="customer_id" name="customer_id" required>
                            <option value="" disabled selected>Select customer</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?= (int)$customer['id'] ?>">
                                    <?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?> (<?= (int)$customer['eco_points'] ?> pts)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="points">Points (positive or negative)</label>
                        <input type="number" class="form-control" id="points" name="points" required>
                    </div>
                    <div>
                        <label class="form-label" for="note">Reference note</label>
                        <textarea class="form-control" id="note" name="note" rows="2" placeholder="Reason for adjustment">Manual adjustment</textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Apply adjustment</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h3 class="h6 mb-0">Recent transactions</h3>
                <span class="badge bg-success bg-opacity-10 text-success">Last 30 entries</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Source</th>
                            <th>Reference</th>
                            <th class="text-end">Points</th>
                            <th class="text-end">Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No EcoPoints activity yet.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?= htmlspecialchars($transaction['first_name'] . ' ' . $transaction['last_name']) ?></td>
                                <td class="text-capitalize">
                                    <span class="badge bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars($transaction['source_type']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($transaction['source_reference'] ?? '-') ?></td>
                                <td class="text-end <?= (int)$transaction['points'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= (int)$transaction['points'] ?>
                                </td>
                                <td class="text-end"><small><?= htmlspecialchars($transaction['created_at']) ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
