<!--doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title><--?php echo \Kinola\KinolaWp\Checkout::get_title(); ?></title>
</head>
<body-->
<?php get_header(); ?>
<?php do_action('kinola/checkout/before_content'); ?>
<div class="kinola-container mt-12">
    <div id="kinola-container"></div>
</div>
<script src="<?php echo \Kinola\KinolaWp\Checkout::get_kinola_js_url(); ?>"></script>
<script>
    const container = document.getElementById('kinola-container')

    window.Kinola.render(container, {
        apiBaseUrl: '<?php echo \Kinola\KinolaWp\Checkout::get_plugin_api_base_url(); ?>',
        eventId: '<?php echo \Kinola\KinolaWp\Checkout::get_event_id(); ?>',
        scheduleUrl: '<?php echo get_home_url(); ?>',
        hasNewsletterCheckbox: <?php echo constant('KINOLA_SHOW_NEWSLETTER_CHECKBOX') ? 'true' : 'false'; ?>,
        selectedLang: '<?php echo \Kinola\KinolaWp\Helpers::get_language(); ?>',
        strings: <?php echo json_encode(\Kinola\KinolaWp\Checkout::get_strings()); ?>,

        <?php if (constant('KINOLA_TERMS_LINK')): ?>
            termsLink: '<?php echo KINOLA_TERMS_LINK; ?>',
        <?php endif; ?>
    })
</script>

<?php do_action('kinola/checkout/after_content'); ?>
<style>
</style>
<?php
get_footer();