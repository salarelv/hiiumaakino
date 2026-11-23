<?php /* @var $film \Kinola\KinolaWp\Film */ ?>

<section class="kinola-film-screenings">
    <h3 class="mb-2"><?php echo __( 'Screenings', 'kinola' ); ?></h3>
    <?php echo $rendered_filter; ?>
    <?php if ( count( $events ) ): ?>
        <?php foreach ( $events as $event ): ?>
            <?php /* @var $event \Kinola\KinolaWp\Event */ ?>
            <div class="rounded-md bg-white th-shadow mb-2 p-2 pb-4 ">
                <div><strong><?php echo $event->get_date(); ?> </strong><span><?php echo $event->get_time(); ?><span></div>
                <p><?php echo $event->get_venue_name(); ?></p>
                <div class="flex justify-center">
                    <a href="<?php echo $event->get_checkout_url(); ?>">
                        <button class="th-btn m-auto bg-primary"><?php _e( 'Buy ticket', 'kinola' ); ?></button>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <?php _e( 'No upcoming screenings', 'kinola' ); ?>
    <?php endif; ?>
</section>
