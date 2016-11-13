<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <!-- Mobile -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Crawling</title>
    <link rel="stylesheet" href="static/css/style.css">
</head>
<body>
    <a href="/"><h1>&quot;Let me crawl that for you&quot;</h1></a>
    <main>
        <div class="crawlform">
            <form action="/crawl">
                <p>
                    <label for="search_phrase">Search phrase:</label>
                    <input type="text" name="search-phrase" value="<?= htmlentities($searchPhrase); ?>">
                </p>
                <p>
                    <label for="crawl-url">Address:</label>
                    <select id="scheme" name="scheme">
                        <option value="http://" <?= $scheme === 'http://' ? 'selected' : ''; ?>selected>http://</option>
                        <option value="https://" <?= $scheme === 'https://' ? 'selected' : ''; ?>>https://</option>
                    </select>
                    <input type="text" name="crawl-url" value="<?= htmlentities($url);?>">
                </p>
                <p>
                <label for="depth">Crawl depth: </label>
                <select id="depth" name="depth">
                    <option value="2" <?= $depth === 2 ? 'selected' : '' ?>>2</option>
                    <option value="3" <?= $depth === 3 ? 'selected' : '' ?>>3</option>
                    <?php /* <option value="4" <?= $depth === 4 ? 'selected' : '' ?>>4</option>
                    <option value="5" <?= $depth === 5 ? 'selected' : '' ?>>5</option> */ ?>
                </select>
                </p>
                <input type="submit" onclick="loader();">
            </form>
        </div>

        <div id="result" class="result">
            <?= $result ?>
        </div>
    </main>
<script>
function loader () {
    var randomMsg = [
        'patience',
        'crawling is slow today',
        'wait for it',
        'the server IS working',
        'do NOT press reload'
    ];
    var result = document.getElementById('result');
    result.textContent = "Crawling . . . ";
    window.setInterval(function () {
        waitMsg = Math.random() > 0.9 ? randomMsg[Math.floor(Math.random() * (randomMsg.length))] : '.';
        result.textContent += waitMsg + ' ';
    }, 900);
}
</script>
</body>
</html>
