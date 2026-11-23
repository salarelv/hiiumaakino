<?php
/**
 * This template is the output of [kinola_events] shortcode.
 * It displays a list of all upcoming events along with venue, date and time filters.
 */
?>
<div  class="xl:relative w-full">
    <?php echo $rendered_filter; ?>
    <?php if ( count( $events ) ): ?>
            <div class="kinola-events grid lg:grid-cols-4 md:grid-cols-3 gap-4">
                <?php foreach ( $events as $event ): ?>
                    <?php /* @var $event \Kinola\KinolaWp\Event */ ?>
                    <div class="kinola-event">
                        <a class="kinola-event-title" href="<?php echo $event->get_film_url(); ?>">
                            <div class="kinola-event-poster">
                                <img src="<?php echo $event->get_field( 'production_poster' ); ?>"/>
                            </div>
                        </a>
                        
                        <div class="kinola-event-details">
                            <p>
                                <a class="kinola-event-title" href="<?php echo $event->get_film_url(); ?>">
                                    <strong>
                                        <?php echo $event->get_field( 'production_title' ); ?>
                                    </strong>
                                </a>
                                <br>
                                <span class="kinola-event-venue">
                                    <?php echo $event->get_venue_name(); ?>
                                </span>
                                <br>
                                <span class="kinola-event-date">
                                    <?php echo $event->get_date() . ' ' . $event->get_time(); ?>
                                </span>
                            </p>
                            <div class="flex justify-center">
                                <a class="kinola-event-tickets-link m-auto" href="<?php echo $event->get_checkout_url(); ?>">
                                    <button class="th-btn bg-primary"><?php _e( 'Buy ticket', 'kinola' ); ?></button>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
    <?php else: ?>
        <div>
            <?php _e( 'No upcoming events.', 'kinola' ); ?>
        </div>
    <?php endif; ?>
</div>
