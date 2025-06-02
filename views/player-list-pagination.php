<?php
if ($total_pages > 1) {
    echo '<div class="hobc-pagination">';

    // Prev button
    if ($paged > 1) {
        $prev_link = add_query_arg('paged', $paged - 1, $current_url);
        echo '<a href="' . esc_url($prev_link) . '" class="hobc-btn">« Prev</a> ';
    } else {
        echo '<span class="hobc-btn disabled">« Prev</span> ';
    }

    // Page numbers
    for ($i = 1; $i <= $total_pages; $i++) {
        $link = add_query_arg('paged', $i, $current_url);
        if ($i === $paged) {
            echo '<span class="fw-bold">' . $i . '</span> ';
        } else {
            echo '<a href="' . esc_url($link) . '">' . $i . '</a> ';
        }
    }

    // Next button
    if ($paged < $total_pages) {
        $next_link = add_query_arg('paged', $paged + 1, $current_url);
        echo '<a href="' . esc_url($next_link) . '" class="hobc-btn">Next »</a>';
    } else {
        echo '<span class="hobc-btn disabled">Next »</span>';
    }

    echo '</div>';
}