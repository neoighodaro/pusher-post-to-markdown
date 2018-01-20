<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pusher Post to Markdown</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Playfair+Display:700,900">
    <link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.2/css/bootstrap.css'>
    <style type="text/css">
        .blog-header{line-height:1;border-bottom:1px solid #e5e5e5}
        .blog-header .blog-header-logo{font-family:"Playfair Display",Georgia,"Times New Roman",serif;font-size:2.25rem}
        .blog-header .form-inline{top:3px;position:relative}
        .markdown{margin-top:20px}
        .markdown .no-posts{margin-top:50px;}
        .markdown .no-posts h2,.markdown .no-posts h6{text-align:center}
        .markdown .no-posts h2{color:#474747;margin-bottom:10px;font-family:"Playfair Display",Georgia,"Times New Roman"}
        .markdown .no-posts h6,.markdown .post{font-family:'Open Sans','Helvetica Neue',sans-serif}
        .markdown .no-posts h6{color:#999;font-size:14px;font-weight:400}
        .markdown .post{max-height:800px;overflow:scroll;padding:80px 20px 30px;background:#f9f9f9;border-radius:5px;position:relative;white-space: pre-wrap;white-space: -moz-pre-wrap;white-space: -pre-wrap;white-space: -o-pre-wrap;word-wrap: break-word;}
        .markdown .copy-to-clipboard{position:fixed;top:120px;z-index:99;margin-left:20px}
        .markdown .copy-to-clipboard .btn{opacity:.8}
        .markdown .copy-to-clipboard .btn:hover{opacity:1;cursor:pointer}
    </style>
</head>
<body>
    <div class="container">
        <header class="blog-header py-3">
            <div class="row flex-nowrap justify-content-between align-items-center">
            <div class="col-6">
                <a class="blog-header-logo text-dark" href="https://blog.pusher.com/" target="_blank">Pusher</a>
            </div>
            <div class="col-6 d-flex justify-content-end align-items-center">
                <form class="form-inline" action="/" method="post">
                <div class="input-group mb-2 mr-sm-2">
                    <input type="text" value="<?= $url ?? '' ?>" class="form-control" name="pusher_url" required placeholder="URL e.g https://blog.pusher.com/create-a-time-tracking-application-using-laravel-and-vue/">
                </div>
                <button type="submit" class="btn btn-primary mb-2">Submit</button>
                </form>
            </div>
            </div>
        </header>
        <div class="markdown row align-items-center">
            <?php if ($markdown): ?>
            <div class="col">
                <div class="copy-to-clipboard">
                    <button class="btn btn-primary" onclick="copyContentsToClipboard()" id="copybtn">Copy to Clipboard</button>
                    <a href="<?= $url ?>" target="_blank" class="btn btn-primary">Go to Article</a>
                </div>
                <pre class="post" id="md"><?= htmlspecialchars($markdown) ?></pre>
            </div
            <?php else: ?>
            <div class="col no-posts">
                <h2>Nothing to fetch yet.</h2>
                <h6>Use the form at the top right to fetch content.</h6>
            </div>
            <?php endif ?>
        </div>
    </div>
    <script>
        function copyContentsToClipboard() {
            window.getSelection().selectAllChildren(document.getElementById('md'))
            document.execCommand("Copy")
            document.getElementById('copybtn').innerHTML = "Copied!"
            setTimeout(_ => {
                document.getElementById('copybtn').innerHTML = "Copy to Clipboard!"
                window.getSelection().removeAllRanges()
            }, 2000);
        }
    </script>
</body>
</html>
