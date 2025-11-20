<?php

declare(strict_types=1);

use DragonStone\Auth\AdminAuth;
use DragonStone\Repositories\AdminRepository;
use DragonStone\Repositories\ChallengeRepository;

$basePath = realpath(__DIR__ . '/..');
if ($basePath === false || !file_exists($basePath . '/vendor/autoload.php')) {
    $basePath = realpath(dirname(__DIR__));
}

require_once $basePath . '/vendor/autoload.php';
require_once $basePath . '/config/database.php';
require_once $basePath . '/src/helpers.php';

loadEnv($basePath ?: dirname(__DIR__));

session_start();

if (!AdminAuth::check()) {
    header('Location: login.php');
    exit;
}

$pdo = databaseConnection();
$adminId = AdminAuth::id();
$permissions = AdminRepository::permissions($pdo, $adminId ?? 0);

if (!in_array('community.moderate', $permissions, true)) {
    http_response_code(403);
    renderAdmin('admin/error', [
        'title' => 'Access denied',
        'message' => 'You do not have permission to moderate the community.',
        'permissions' => $permissions,
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['form']) && $_POST['form'] === 'challenge_create') {
        $title = trim((string)($_POST['challenge_title'] ?? ''));
        $description = trim((string)($_POST['challenge_description'] ?? ''));
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $reward = (int)($_POST['eco_points_reward'] ?? 0);
        $status = $_POST['challenge_status'] ?? 'scheduled';
        $statusAllowed = ['scheduled', 'active', 'completed'];

        if ($title === '' || $description === '' || $startDate === '' || $endDate === '' || $reward <= 0 || !in_array($status, $statusAllowed, true)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Please complete all challenge fields with valid values.'];
        } elseif (strtotime($startDate) === false || strtotime($endDate) === false || strtotime($startDate) > strtotime($endDate)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Please provide a valid date range for the challenge.'];
        } else {
            ChallengeRepository::create($pdo, [
                'title' => $title,
                'description' => $description,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'eco_points_reward' => $reward,
                'status' => $status,
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Challenge created successfully.'];
        }
    } else {
        $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
        $status = $_POST['status'] ?? '';
        $allowed = ['pending', 'approved', 'flagged'];
        if ($postId > 0 && in_array($status, $allowed, true)) {
            $stmt = $pdo->prepare('UPDATE community_posts SET status = :status WHERE id = :id');
            $stmt->execute([
                ':status' => $status,
                ':id' => $postId,
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Post status updated.'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid update request.'];
        }
    }

    header('Location: /assignment-ecommerce/prototype/public/admin/community.php');
    exit;
}

$posts = $pdo->query(
    'SELECT cp.*, c.first_name, c.last_name, ch.title AS challenge_title
     FROM community_posts cp
     INNER JOIN customers c ON cp.customer_id = c.id
     LEFT JOIN challenges ch ON cp.challenge_id = ch.id
     ORDER BY cp.created_at DESC'
)->fetchAll() ?: [];

$commentsStmt = $pdo->query(
    'SELECT cc.post_id, cc.body, cc.created_at, c.first_name, c.last_name
     FROM community_comments cc
     INNER JOIN customers c ON cc.customer_id = c.id
     ORDER BY cc.created_at DESC'
);
$comments = [];
foreach ($commentsStmt->fetchAll() ?: [] as $comment) {
    $comments[(int)$comment['post_id']][] = $comment;
}

$challengeList = ChallengeRepository::all($pdo);

renderAdmin('admin/community', [
    'title' => 'Community Moderation',
    'posts' => $posts,
    'comments' => $comments,
    'challenges' => $challengeList,
    'permissions' => $permissions,
]);
