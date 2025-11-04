<?php
/** @var array<int,array<string,mixed>> $posts */
/** @var array<int,array<int,array<string,mixed>>> $comments */
?>
<div class="mb-4">
    <h2 class="h3 mb-1">Community Moderation</h2>
    <p class="text-muted">Approve sustainable living posts, review challenge submissions, and monitor feedback.</p>
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
