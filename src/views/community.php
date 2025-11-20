<?php
/** @var array<int,array<string,mixed>> $challenges */
/** @var array<int,array<string,mixed>> $stories */
/** @var int|null $selectedChallenge */
?>
<section class="mb-5">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <h2 class="display-6 fw-semibold">Join the DragonStone Community</h2>
            <p class="lead text-muted">
                Take on planet-friendly challenges, share your progress, and earn EcoPoints rewards for inspiring others.
            </p>
            <p class="text-muted mb-0">
                Active challenges below can be completed at home. Submit your progress via the form and our team will review each story.
            </p>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm border-success border-opacity-25">
                <div class="card-body">
                    <h3 class="h5">How it works</h3>
                    <ol class="text-muted mb-0 small ps-3">
                        <li>Select an active or scheduled challenge.</li>
                        <li>Complete the eco-friendly actions in your own space.</li>
                        <li>Share your tips with the community and earn EcoPoints when approved.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h4 mb-0">Current challenges</h3>
        <span class="text-muted small">EcoPoints reward listed per completion.</span>
    </div>
    <?php if (empty($challenges)): ?>
        <div class="alert alert-info">
            Community challenges are being curated. Check back soon or follow us on social for early announcements.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($challenges as $challenge): ?>
                <?php $status = $challenge['status']; ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-success-subtle text-success text-uppercase mb-2">
                                <?= htmlspecialchars($status) ?>
                            </span>
                            <h4 class="h5"><?= htmlspecialchars($challenge['title']) ?></h4>
                            <p class="text-muted flex-grow-1"><?= htmlspecialchars($challenge['description']) ?></p>
                            <div class="small text-muted mb-2">
                                <?= htmlspecialchars($challenge['start_date']) ?> – <?= htmlspecialchars($challenge['end_date']) ?>
                            </div>
                            <div class="fw-semibold">Reward: <?= (int)$challenge['eco_points_reward'] ?> EcoPoints</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="mb-5">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="h5 mb-0">Submit your challenge entry</h3>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= htmlspecialchars(rtrim($baseUrl, '/')) ?>/" class="vstack gap-3">
                        <input type="hidden" name="action" value="community_submit">
                        <input type="hidden" name="redirect" value="?page=community">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="first_name">First name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="last_name">Last name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="phone">Phone (optional)</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="city">City</label>
                                <input type="text" class="form-control" id="city" name="city">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="country">Country</label>
                                <input type="text" class="form-control" id="country" name="country">
                            </div>
                        </div>
                        <div>
                            <label class="form-label" for="challenge_id">Challenge</label>
                            <select class="form-select" id="challenge_id" name="challenge_id" <?= empty($challenges) ? 'disabled' : '' ?>>
                                <option value="">Pick a challenge</option>
                                <?php foreach ($challenges as $challenge): ?>
                                    <option value="<?= (int)$challenge['id'] ?>" <?= $selectedChallenge === (int)$challenge['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($challenge['title']) ?> (<?= htmlspecialchars($challenge['status']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="entry_title">Entry title</label>
                            <input type="text" class="form-control" id="entry_title" name="entry_title" required>
                        </div>
                        <div>
                            <label class="form-label" for="entry_body">Share your progress</label>
                            <textarea class="form-control" id="entry_body" name="entry_body" rows="5" placeholder="Describe what you changed, learned, or measured." required></textarea>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-success" type="submit" <?= empty($challenges) ? 'disabled' : '' ?>>Submit for review</button>
                        </div>
                    </form>
                    <?php if (empty($challenges)): ?>
                        <p class="text-muted small mt-3">We’ll re-open submissions once the next challenge is live.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h3 class="h5 mb-0">Community spotlights</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($stories)): ?>
                        <p class="text-muted mb-0">Approved stories will appear here once community submissions are reviewed.</p>
                    <?php else: ?>
                        <div class="vstack gap-3">
                            <?php foreach ($stories as $story): ?>
                                <div class="border rounded-3 p-3">
                                    <?php if (!empty($story['challenge_title'])): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary mb-2"><?= htmlspecialchars($story['challenge_title']) ?></span>
                                    <?php endif; ?>
                                    <h4 class="h6 mb-1"><?= htmlspecialchars($story['title']) ?></h4>
                                    <p class="text-muted small mb-2">By <?= htmlspecialchars($story['first_name'] . ' ' . $story['last_name']) ?> – <?= htmlspecialchars(date('M j, Y', strtotime($story['created_at']))) ?></p>
                                    <p class="mb-0 small"><?= nl2br(htmlspecialchars($story['body'])) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
