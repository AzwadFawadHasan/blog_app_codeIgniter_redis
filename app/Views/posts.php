<h2>Blog Posts</h2>
<ul>
    <?php foreach ($posts as $post): ?>
        <li>
            <a href="<?= site_url('blog/view/' . $post['id']) ?>"><?= esc($post['title']); ?></a>
            <p><?= esc($post['created_at']); ?></p>
        </li>
    <?php endforeach; ?>
</ul>
