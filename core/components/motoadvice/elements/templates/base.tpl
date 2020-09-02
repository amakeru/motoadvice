<!doctype html>
<html lang="ru" class="_no-js _loading">
<head>
    <base href="{'site_url'|config}">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {block 'head'}
        {include 'file:chunks/common/head.tpl'}
    {/block}

    {'!MinifyX'|snippet: [
        'cssSources' => '
            /assets/motoadvice/scss/fonts.css,
            /assets/motoadvice/scss/normalize.css,
			/assets/motoadvice/plugins/lightgallery/css/lightgallery.min.css,
			/assets/motoadvice/plugins/lightslider/css/lightslider.min.css,
            /assets/motoadvice/scss/style.scss,
        ',
        'jsSources' => '
            /assets/motoadvice/plugins/jquery.min.js,
			/assets/motoadvice/plugins/lightgallery/js/lightgallery.min.js,
			/assets/motoadvice/plugins/lightgallery/js/lg-video.min.js,
            /assets/motoadvice/plugins/lightslider/js/lightslider.min.js,
            /assets/motoadvice/js/script.js
        '
    ]}
    {$_modx->getPlaceholder('MinifyX.css')}

    <script>
      document.documentElement.classList.remove('_no-js');
      document.documentElement.classList.add('_js');
    </script>
</head>
<body>

    {block 'header'}
        {include 'file:chunks/common/header.tpl'}
    {/block}
    {block 'content'}
        {include 'file:chunks/common/content.tpl'}
    {/block}
    {block 'footer'}
        {include 'file:chunks/common/footer.tpl'}
    {/block}

    {include 'file:chunks/common/modals.tpl'}

    {$_modx->getPlaceholder('MinifyX.javascript')}
</body>
</html>
