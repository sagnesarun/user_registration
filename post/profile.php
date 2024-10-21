<?php
session_start();
include '../templates/header.php';
include '../models/User.php';
include '../models/Post.php';

$user = new User();
$post = new Post();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_data = $user->getById($user_id);
    $user_posts = $post->getByUserId($user_id);
} else {
    header('Location: login.php');
    exit;
}
?>

<style>
    .container {
        max-width: 800px;
        margin: auto;
        padding: 20px;
    }

    h1 {
        color: #e74c3c;
        text-align: center;
    }

    .user-avatar {
        width: 100px;
        height: 100px;
        background-color: #3498db;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        font-weight: bold;
        text-transform: uppercase;
        margin: 0 auto;
    }

    .profile-info {
        text-align: center;
        margin: 20px 0;
    }

    .btn-link {
        font-size: 16px;
        color: #3498db;
        text-decoration: none;
    }

    .btn-link:hover {
        color: #2980b9;
        text-decoration: underline;
    }

    .post {
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
    }

    .post-footer {
        font-size: 12px;
        color: #7f8c8d;
    }
</style>

<div class="container">
    <h1>User Profile</h1>

    <div class="user-avatar">
        <span><?= strtoupper(substr($user_data['username'], 0, 2)) ?></span>
    </div>

    <div class="profile-info">
        <h2><?= htmlspecialchars($user_data['username'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p>Email: <?= htmlspecialchars($user_data['email'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <h3>Your Posts</h3>

    <?php if (!empty($user_posts)): ?>
        <?php foreach ($user_posts as $post): ?>
            <div class="post">
                <p><?= htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8') ?></p>
                <p class="post-footer">Posted on <?= htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No posts available.</p>
    <?php endif; ?>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</div>

<?php include '../templates/footer.php'; ?>
