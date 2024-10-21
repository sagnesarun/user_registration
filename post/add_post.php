<?php
session_start();
include '../templates/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../models/Post.php';
    $post = new Post();
    $content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');
    $post->create($content, $_SESSION['user_id']);
    header('Location: index.php');
    exit();
}
?>

<div class="container">
    <h1>Add a New Post</h1>
    <form action="add_post.php" method="POST">
        <div class="form-group">
            <textarea name="content" class="form-control" placeholder="Write something interesting..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<?php include '../templates/footer.php'; ?>
