<table style="width:100%; margin-top: 5px">

    <tr>
        <th>Username</th>
        <th style="text-align:center;">Comment</th>
    </tr>
    <div class="row" id="alerts">
    <?php foreach ($comments as $comment): ?>
        <tr id="comment_<?= $comment['comment_id']; ?>">
            <td><?= $comment['username']; ?></td>
            <td style="text-align:center;"><?= $comment['comment_text']; ?></td>

            <?php if ($this->session->userdata('user')['role_id'] == 2): ?>
                <!--<td><a href="<?= base_url(); ?>admin/comment/<?= $comment['comment_id']; ?>/delete">Delete comment</a></td>-->
                <td style="text-align:center; margin: 0px; padding: 0px"><input type="submit" name="submit"
                                                                                class="commentDelete"
                                                                                value="Delete Comment"
                                                                                data-comment-id="<?= $comment['comment_id']; ?>">
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>

</table>
