<html>
<head>
    <title>
        <?php echo $title; ?>
    </title>
    <script type="text/javascript">
        var base_url = "<?= base_url(); ?>";
    </script>
    <link type='text/css' rel='stylesheet' href='<?= base_url(); ?>style.css'/>
    <link type='text/css' href='<?= base_url(); ?>css/contact.css' rel='stylesheet' media='screen'/>
    <script type="text/javascript" src="<?= base_url(); ?>script.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>jquery.js"></script>
    <script type='text/javascript' src='<?= base_url(); ?>js/jquery.simplemodal.js'></script>
    <script type='text/javascript' src='<?= base_url(); ?>js/contact.js'></script>
</head>
<body>
<div id='top'>
</div>
<div id='page'>
    <div id='menu'>
        <a href='<?= base_url(); ?>' class='topMenu'>ГЛАВНАЯ</a>
        <a href='<?= base_url(); ?>about/' class='topMenu'>О КОМПАНИИ</a>
        <a href='<?= base_url(); ?>uslugi/' class='topMenu'>УСЛУГИ</a>
        <a href='<?= base_url(); ?>korporpred/' class='topMenu'>ДЛЯ БИЗНЕСА</a>
        <div id='mainL'>
        </div>
    </div>

    <div id='toplogo'>
        <img src='<?= base_url(); ?>/images/logo.png'/>
    </div>

    <div id='textPriority'>
        <div class='textTop'>
            БЫСТРАЯ ДОСТАВКА
        </div>
        <div class='textTop'>
            НАДЕЖНЫЙ СЕРВИС
        </div>
        <div class='bar'>
        </div>
    </div>