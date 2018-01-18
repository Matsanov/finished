<?php $this->load->view('header'); ?>


<table id="id_users_table" class="table table-striped">
    <thead>
    <tr>
        <th>Username</th>
        <th>Images Count</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($results as $data): ?>
        <tr>
            <td><?= $data->username; ?></td>
            <td><?= $data->images_count; ?></td>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>

<?= $links; ?>

<?php $this->load->view('footer'); ?>

