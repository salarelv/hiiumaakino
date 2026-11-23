<?php
/**
 * This template is rendered inside the_content of a single Film post type.
 * It displays the detailed information of a film and all future screenings of that film.
 */
?>

<?php /* @var $film \Kinola\KinolaWp\Film */ ?>
<?php 
    
    if (gettype($film->get_field("gallery")) == "array" && count($film->get_field("gallery")) > 0 ) { 
        $gallery_count = count($film->get_field("gallery"));
    ?>
    <div class="kinola-header bg-no-repeat bg-cover mb-4 lg:mb-12" style="background-image: url('<?php echo $film->get_field("gallery")[$gallery_count - 1]["src"] ?>');">
        <div class="container text-white"><h1><?php echo  $film->get_title(); ?></h1></div>
    </div>
<?php } else { ?>
    <div class="mt-12"></div>
<?php } ?>
<div class="container kinola-film-content lg:flex">
    <div class="kinola-sidebar">
        <img class="poster" src="<?php echo $film->get_field( 'poster' ); ?>"/>
        <div class="kinola-sidebar__block">
          <h3><?php echo  $film->get_title(); ?></h3>
          <span><?php echo $film->get_field("countries"); ?></span>
        </div>
        <?php if ($film->get_field("languages")) {?>
        <div class="kinola-sidebar__block">
          <h3>Keel</h3>
          <span><?php echo $film->get_field("languages"); ?></span>
        </div>
        <?php } ?>
        <?php if ($film->get_field("subtitles")) {?>
            <div class="kinola-sidebar__block">
            <h3>Subtiitrid</h3>
            <span><?php echo $film->get_field("subtitles"); ?></span>
            </div>
        <?php } ?>
        <?php if ($film->get_field("runtime")) {?>
        <div class="kinola-sidebar__block">
          <h3>Pikkus</h3>
          <span><?php echo $film->get_field("runtime"); ?> min</span>
        </div>
        <?php } ?>
        <?php if ($film->get_field("rating")) {?>
        <div class="kinola-sidebar__block">
          <h3>Vanusepiirang</h3>
          <span><?php echo $film->get_field("rating"); ?></span>
        </div>
        <?php } ?>
        <!--?php print_r($film->get_field("gallery")); ?-->
    </div>
    <div class="kinola-content flex-auto overflow-hidden mt-4 md:mt-0 md:mx-12">
        <div class="mb-4"><?php echo $film->get_field( 'description' ); ?></div>
        <div class="mb-4"><?php echo \Kinola\KinolaWp\View::render( 'film/trailer', [ 'film' => $film ] ); ?></div>
        <div class="mb-4"><?php echo \Kinola\KinolaWp\View::render( 'film/gallery', [ 'film' => $film ] ); ?></div>
    </div>
    <div class="kinola-sidebar">
        <?php echo \Kinola\KinolaWp\View::render( 'film/screenings', [
            'film'            => $film,
            'events'          => $events,
            'rendered_filter' => false,
        ] ); ?>
    </div>
</div>
