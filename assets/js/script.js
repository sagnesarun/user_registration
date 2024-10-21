$(document).ready(function() {
    $('.like-btn').on('click', function(e) {
        e.preventDefault();
        const postId = $(this).data('postid');
        const likeBtn = $(this);
        
        $.ajax({
            url: 'like_post.php',
            type: 'POST',
            data: { post_id: postId },
            success: function(response) {
                let likesCount = parseInt(likeBtn.siblings('.like-count').text());
                if (likeBtn.hasClass('liked')) {
                    likeBtn.removeClass('liked').text('Like');
                    likeBtn.siblings('.like-count').text(likesCount - 1);
                } else {
                    likeBtn.addClass('liked').text('Liked');
                    likeBtn.siblings('.like-count').text(likesCount + 1);
                }
            }
        });
    });
});

$('.comment-btn').on('click', function() {
    const postId = $(this).data('postid');
    $(`#comment-section-${postId}`).toggle();
});

$('.comment-form').on('submit', function(e) {
    e.preventDefault();
    const postId = $(this).data('postid');
    const commentInput = $(`#comment-input-${postId}`);
    const commentContent = commentInput.val();
    
    if (commentContent.trim() === '') {
        alert("Comment cannot be empty!");
        return;
    }

    $.ajax({
        url: 'add_comment.php',
        type: 'POST',
        data: {
            post_id: postId,
            content: commentContent
        },
        success: function(response) {
            const newComment = `<div class="comment"><strong>You</strong>: ${commentContent}</div>`;
            $(`#comment-list-${postId}`).append(newComment);
            commentInput.val('');
        }
    });
});
