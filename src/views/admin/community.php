<?php
/** @var array<int,array<string,mixed>> $posts */
/** @var array<int,array<int,array<string,mixed>>> $comments */
/** @var array<int,array<string,mixed>> $challenges */
?>
<div class="mb-4">
    <h2 class="h3 mb-1">Community Moderation</h2>
    <p class="text-muted">Approve sustainable living posts, review challenge submissions, and monitor feedback.</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h3 class="h6 mb-0">Create a new challenge</h3>
            </div>
            <div class="card-body">
                <form method="post" action="community.php" class="vstack gap-3">
                    <input type="hidden" name="form" value="challenge_create">
                    <div>
                        <label class="form-label" for="challenge_title">Title</label>
                        <input type="text" class="form-control" id="challenge_title" name="challenge_title" required>
                    </div>
                    <div>
                        <label class="form-label" for="challenge_description">Description</label>
                        <textarea class="form-control" id="challenge_description" name="challenge_description" rows="3" required></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="start_date">Start date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="end_date">End date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="eco_points_reward">EcoPoints reward</label>
                            <input type="number" min="10" step="10" class="form-control" id="eco_points_reward" name="eco_points_reward" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="challenge_status">Status</label>
                            <select class="form-select" id="challenge_status" name="challenge_status">
                                <option value="scheduled">Scheduled</option>
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-success" type="submit">Publish challenge</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h3 class="h6 mb-0">Challenge timeline</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                        <tr>
                            <th>Title</th>
                            <th>Dates</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($challenges)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No challenges yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($challenges as $challenge): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($challenge['title']) ?></div>
                                        <small class="text-muted">Reward: <?= (int)$challenge['eco_points_reward'] ?> pts</small>
                                    </td>
                                    <td><?= htmlspecialchars($challenge['start_date']) ?> – <?= htmlspecialchars($challenge['end_date']) ?></td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-uppercase text-muted"><?= htmlspecialchars($challenge['status']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="accordion" id="communityModeration">
    <?php if (empty($posts)): ?>
        <div class="alert alert-info">No community activity yet. Encourage members to join challenges and share tips.</div>
    <?php endif; ?>
    <?php foreach ($posts as $index => $post): ?>
        <?php $postId = (int)$post['id']; ?>
        <div class="accordion-item mb-3 shadow-sm">
            <h2 class="accordion-header" id="heading<?= $postId ?>">
                <button class="accordion-button<?= $index > 0 ? ' collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#post<?= $postId ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="post<?= $postId ?>">
                    <div class="d-flex flex-column">
                        <span class="fw-semibold"><?= htmlspecialchars($post['title']) ?></span>
                        <small class="text-muted">By <?= htmlspecialchars($post['first_name'] . ' ' . $post['last_name']) ?> · <?= htmlspecialchars($post['created_at']) ?></small>
                    </div>
                </button>
            </h2>
            <div id="post<?= $postId ?>" class="accordion-collapse collapse<?= $index === 0 ? ' show' : '' ?>" aria-labelledby="heading<?= $postId ?>" data-bs-parent="#communityModeration">
                <div class="accordion-body">
                    <?php if (!empty($post['challenge_title'])): ?>
                        <span class="badge bg-success bg-opacity-10 text-success mb-2">Challenge: <?= htmlspecialchars($post['challenge_title']) ?></span>
                    <?php endif; ?>
                    <p><?= nl2br(htmlspecialchars($post['body'])) ?></p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <span class="badge bg-primary bg-opacity-10 text-primary text-capitalize">Status: <?= htmlspecialchars($post['status']) ?></span>
                        </div>
                        <form method="post" action="community.php" class="d-flex gap-2">
                            <input type="hidden" name="post_id" value="<?= $postId ?>">
                            <select name="status" class="form-select form-select-sm">
                                <?php foreach (['pending','approved','flagged'] as $status): ?>
                                    <option value="<?= $status ?>" <?= $status === $post['status'] ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-sm btn-success" type="submit">Update</button>
                        </form>
                    </div>
                    <div class="mt-4">
                        <h3 class="h6">Comments</h3>
                        <?php if (empty($comments[$postId])): ?>
                            <p class="text-muted">No comments yet.</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($comments[$postId] as $comment): ?>
                                    <li class="list-group-item">
                                        <div class="fw-semibold"><?= htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($comment['created_at']) ?></small>
                                        <p class="mb-0"><?= nl2br(htmlspecialchars($comment['body'])) ?></p>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
