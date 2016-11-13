<h3>Search result</h3>
<div>
    <?php foreach ($search_results as $search_result) : ?>
    <p>
    <?= $search_result['page_nr']?>. <?= $search_result['title'] ?> :
    <a href="<?= $search_result['url'] ?>"><?= $search_result['url'] ?></a>
    </p>
    <?php endforeach ?>
</div>

<h3>Crawled pages</h3>

<div>
    <?php foreach ($crawled_urls as $url) : ?>
    <p>
    <?= $url['page_nr']?>. <?= $url['title'] ?> :
    <a href="<?= $url['url'] ?>"><?= $url['url'] ?></a>
    </p>
    <?php endforeach ?>
</div>

<h3 class="error">Errors</h3>

<div>
    <?php foreach ($error_links as $error_link) : ?>
    <p>
    Url: <?= $error_link['url'] ?><br />
    <?= $error_link['description'] ?>
    </p>
    <?php endforeach ?>
</div>

