<?php
/**
 * Ranking Badge Template Part
 * 
 * @package Adorkini
 */

$rank = get_query_var( 'rank' );
$badge_color = get_query_var( 'badge_color' );
$border_color = get_query_var( 'border_color' );

if ( ! $rank ) return;
?>

<div class="ranking-badge absolute top-2 left-2 z-10 flex flex-col items-center">
    <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-lg border-2 font-bold text-sm <?php echo esc_attr( $badge_color . ' ' . $border_color ); ?>">
        #<?php echo esc_html( $rank ); ?>
    </div>
</div>
