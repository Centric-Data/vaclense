<?php
/**
 *
 * @package vacancies
 */
?>
<?php

  $args = array (
    'post_type'     =>  'centric_vacancy',
    'post_status'   =>  'publish',
    'posts_per_page'  =>  6,
    'order'           =>  'ASC',
  );

  $query = new WP_Query( $args );

?>


<div class="vacancy__wrapper flex flex-wrap justify-between gap-2">
<?php if( $query->have_posts() ) : while( $query->have_posts() ) : $query->the_post(); ?>
  <div class="vacancy__card flex flex-col w-3/7 rounded bg-yellow-300 p-4 shadow-md w-5/12">
    <div class="vacancy__card--top flex justify-between">
      <div class="company__logo">
        <img class="w-3/5" src="<?php echo VL_PLUGIN_URL . 'img/logo-vac.png'; ?>" alt="logo">
      </div>
      <div class="details w-4/5">
        <ul class="p-0">
          <li class="text-xs text-current"><?php echo esc_attr( get_post_meta( get_the_ID(), 'vacancy_org', true ) ); ?></li>
          <li class="text-xl text-gray-700 font-bold"><?php the_title(); ?></li>
          <li><?php echo esc_attr( get_post_meta( get_the_ID(), 'vacancy_pay', true ) ); ?></li>
        </ul>
      </div>
    </div>
    <div class="vacancy__card--bottom flex justify-between items-center mt-2">
      <div class="bottom__details">
        <ul class="flex gap-2 p-0">
          <li class="bg-gray-100 pt-0.5 pb-0.5 pl-1 pr-1 text-gray-400 rounded">Full Time</li>
          <li class="bg-gray-100 pt-0.5 pb-0.5 pl-1 pr-1 text-gray-400 rounded">Senior</li>
          <li class="bg-gray-100 pt-0.5 pb-0.5 pl-1 pr-1 text-gray-400 rounded">UX/UI</li>
        </ul>
      </div>
      <div class="more__details">
        <a class="bg-black p-2 text-white no-underline rounded" href="<?php echo get_permalink( $post->ID ); ?>">View Job</a>
      </div>
    </div>
  </div>
<?php endwhile; else:
        echo esc_html__( 'Sorry, no positions open', 'vaclense' );
      endif;
   ?>
</div>
