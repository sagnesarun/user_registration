<?php
session_start();
include '../templates/header.php'; 
include '../models/Post.php';
include '../models/Comment.php';
include '../models/Like.php';

$postModel = new Post();
$commentModel = new Comment();
$likeModel = new Like();
$posts = $postModel->getAll();
?>

<style>
    .post {
        padding: 15px 0;
    }

    .user-avatar {
        width: 50px;
        height: 50px;
        background-color: #3498db;
        color: #e67e22;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .username {
        font-size: 20px;
        font-weight: bold;
        color: #e67e22;
        margin-left: 15px;
        display: inline-block;
        vertical-align: middle;
    }

    .content {
        font-size: 16px;
        color: #e67e22;
        margin-top: 10px;
    }

    .post-meta {
        font-size: 12px;
        color: #e67e22;
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

    .post-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
        color: #7f8c8d;
        margin-top: 10px;
    }

    .divider {
        border-bottom: 1px solid #3498db;
        margin: 15px 0;
    }

    .container {
        max-width: 800px;
        margin: auto;
    }

    h1 {
        color: #e74c3c;
        text-align: center;
    }

    .notification {
        color: #e74c3c;
        background-color: #f9ebea;
        border: 1px solid #e74c3c;
        padding: 10px;
        text-align: center;
        margin-bottom: 20px;
        display: none;
    }

    .comments-list {
        margin-top: 10px;
        display: none;
    }

    .comment-item {
        padding: 5px 0;
    }

    .delete-comment {
        color: #e74c3c;
        cursor: pointer;
        font-size: 14px;
        margin-left: 10px;
    }
</style>

<div class="container mt-5">
    <div class="notification" id="notification">
        You must be logged in to like, comment, or add a post. Redirecting to login...
    </div>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="add_post.php" class="btn btn-primary mb-4">Add a Post</a>
    <?php endif; ?>

    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <div class="d-flex align-items-center">
                    <div class="user-avatar">
                        <span><?= strtoupper($post['username'][0]) . strtoupper($post['username'][1]) ?></span>
                    </div>
                    <div class="username"><?= htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8') ?></div>
                </div>

                <p class="content"><?= htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8') ?></p>

                <div class="post-footer">
                    <p class="post-meta">Posted <?= htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8') ?></p>

                    <div class="d-flex">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button id="like-btn-<?= $post['id'] ?>" class="btn btn-link text-primary like-btn" data-post-id="<?= $post['id'] ?>" data-user-id="<?= $_SESSION['user_id'] ?>">
                                <i class="fas fa-heart"></i> <?= $likeModel->countLikes($post['id']) ?>
                            </button>

                            <button type="button" class="btn btn-link text-primary comment-btn" data-post-id="<?= $post['id'] ?>">
                                <i class="fas fa-comments"></i> <?= count($commentModel->getByPostId($post['id'])) ?> Comments
                            </button>
                        <?php else: ?>
                            <button class="btn btn-link text-primary" onclick="showLoginNotification()">
                                <i class="fas fa-heart"></i> <?= $likeModel->countLikes($post['id']) ?>
                            </button>

                            <button class="btn btn-link text-primary" onclick="showLoginNotification()">
                                <i class="fas fa-comments"></i> <?= count($commentModel->getByPostId($post['id'])) ?> Comments 
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <form id="comment-form-<?= $post['id'] ?>" class="mt-3" style="display: none;">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <div class="form-group">
                            <textarea name="comment" class="form-control" placeholder="Write a comment..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">Add Comment</button>
                    </form>
                <?php endif; ?>

                <div class="comments-list" id="comments-list-<?= $post['id'] ?>">
                    <?php $comments = $commentModel->getByPostId($post['id']); ?>
                    <?php if (!empty($comments)): ?>
                        <h5>Comments:</h5>
                        <ul class="list-unstyled">
                            <?php foreach ($comments as $comment): ?>
                                <li class="comment-item">
                                    <strong><?= htmlspecialchars($comment['username'], ENT_QUOTES, 'UTF-8') ?>:</strong>
                                    <?= htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8') ?>
                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $comment['user_id']): ?>
                                        <span class="delete-comment" data-comment-id="<?= $comment['id'] ?>">Delete Comment</span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No comments yet.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="divider"></div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No posts available at the moment.</p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.like-btn').on('click', function() {
        <?php if (!isset($_SESSION['user_id'])): ?>
            showLoginNotification();
        <?php else: ?>
            const post_id = $(this).data('post-id');
            const user_id = $(this).data('user-id');
            const likeBtn = $(`#like-btn-${post_id}`);

            $.ajax({
                url: 'like_post.php',
                type: 'POST',
                data: { post_id: post_id, user_id: user_id },
                success: function(response) {
                    const res = JSON.parse(response);

                    if (res.status === 'success') {
                        likeBtn.html('<i class="fas fa-heart"></i> ' + res.like_count);
                        location.reload();
                    } else if (res.error) {
                        alert(res.error);
                    }
                }
            });
        <?php endif; ?>
    });

    $('.comment-btn').on('click', function() {
        const post_id = $(this).data('post-id');
        const form = $(`#comment-form-${post_id}`);
        const commentsList = $(`#comments-list-${post_id}`);

        form.toggle();
        commentsList.toggle();
    });

    $('form[id^="comment-form-"]').on('submit', function(e) {
        e.preventDefault();
        
        <?php if (!isset($_SESSION['user_id'])): ?>
            showLoginNotification();
        <?php else: ?>
            const form = $(this);
            const post_id = form.find('input[name="post_id"]').val();
            const comment_text = form.find('textarea[name="comment"]').val();

            $.ajax({
                url: 'add_comment.php',
                type: 'POST',
                data: {
                    action: 'add',
                    post_id: post_id,
                    user_id: <?= $_SESSION['user_id'] ?>,
                    comment_text: comment_text
                },
                success: function(response) {
                    const res = JSON.parse(response);

                    if (res.status === 'success') {
                        loadComments(post_id);
                        form[0].reset();
                        location.reload();
                    } else {
                        alert(res.error);
                    }
                }
            });
        <?php endif; ?>
    });

    function loadComments(post_id) {
        $.ajax({
            url: 'fetch_comments.php',
            type: 'POST',
            data: { post_id: post_id },
            success: function(response) {
                const commentsList = $(`#comments-list-${post_id}`);
                commentsList.html('');

                const comments = JSON.parse(response);
                if (comments.length > 0) {
                    commentsList.append('<h5>Comments:</h5><ul class="list-unstyled">');
                    comments.forEach(comment => {
                        commentsList.append(`
                            <li class="comment-item">
                                <strong>${comment.username}:</strong>
                                ${comment.content}
                                <span class="delete-comment" data-comment-id="${comment.id}">Delete Comment</span>
                            </li>
                        `);
                    });
                    commentsList.append('</ul>');
                } else {
                    commentsList.append('<p>No comments yet.</p>');
                }
                commentsList.show();
            }
        });
    }

    $('.delete-comment').on('click', function() {
        const comment_id = $(this).data('comment-id');

        if (confirm('Are you sure you want to delete this comment?')) {
            $.ajax({
                url: 'delete_comment.php',
                type: 'POST',
                data: { comment_id: comment_id },
                success: function(response) {
                    const res = JSON.parse(response);

                    if (res.status === 'success') {
                        location.reload();
                    } else {
                        alert(res.error);
                    }
                }
            });
        }
    });
});

function showLoginNotification() {
    var notification = document.getElementById('notification');
    notification.style.display = 'block';
    setTimeout(function() {
        window.location.href = 'login.php';
    }, 3000);
}
</script>

<?php include '../templates/footer.php'; ?>
