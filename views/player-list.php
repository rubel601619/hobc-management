<div class="hobc-player">
    <div class="player-thumb">
        <img src="<?php echo esc_url( $image_url ); ?>" alt=" <?php echo esc_attr($name); ?>">
    </div>
    <div class="player-info">
        <h3 class="player-name"><?php echo esc_html( $name ); ?></h3>
        <p class="player-category">
            <span class="fw-bold">
                <?php esc_html_e( 'Category', 'hobc-management' ); ?>
            </span>
            <?php esc_html_e( $category, 'hobc-management'); ?>
        </p>
        <p class="player-team">
            <span class="fw-bold">
                <?php esc_html_e( 'Club', 'hobc-management' ); ?>
            </span>
            <?php esc_html_e( $club_team, 'hobc-management'); ?>
        </p>
        <p class="player-contact">
            <span class="fw-bold">
                <?php esc_html_e( 'Contact', 'hobc-management' ); ?>
            </span>
            <?php esc_html_e( $contact, 'hobc-management'); ?>
        <p>
            <?php
                printf(
                    esc_html__( 'Agreed to terms : %1$s', 'hobc-management' ),
                    $terms ? 'Yes' : 'No'
                );
            ?>
        </p>
        
    </div>
</div>